<?php
/**
 * @package HatenaSyntax
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */

class HatenaSyntax_Locator
{
    protected $elt_ref = null;
    protected $objects = array();
    
    private function __construct()
    {
        $this->setup();
    }
    
    static function it()
    {
        static $obj = null;
        return $obj ? $obj : $obj = new self;
    }
    
    function __get($name)
    {
        return isset($this->objects[$name]) ? 
            $this->objects[$name] : 
            $this->objects[$name] = $this->{'create' . $name}();
    }
    
    protected function createFactory()
    {
        return new HatenaSyntax_Factory($this);
    }
    
    protected function createLineChar()
    {
        return PEG::second(PEG::not(PEG::char("\n\r")), PEG::anything());
    }
    
    protected function createEndOfLine()
    {
        return PEG::choice(PEG::newLine(), PEG::eos());
    }
    
    protected function createFootnote()
    {
        $close = '))';
        $elt = PEG::andalso(PEG::not($close), 
                            PEG::choice($this->link, $this->lineChar));
                            
        $parser = PEG::pack('((', 
                            HatenaSyntax_Util::segment(PEG::many1($elt)), 
                            $close);
                            
        return $this->factory->createNodeCreater('footnote', $parser);
    }
    
    protected function createLineElement()
    {
        return $this->factory->createLineElement();
    }
    
    protected function createLineSegment()
    {
        return HatenaSyntax_Util::segment(PEG::many($this->lineElement));
    }
    
    protected function createHttpLink()
    {
        $title_char = PEG::andalso(PEG::not(']'), 
                                   $this->lineChar);
        
        $title = PEG::second(':title=', PEG::join(PEG::many1($title_char)));
        
        $url_char = PEG::andalso(PEG::not(PEG::choice(']', ':title=')), 
                                 $this->lineChar);
                                 
        $url = PEG::join(PEG::seq(PEG::choice('http://', 'https://'), 
                                  PEG::many1($url_char)));
        $parser = PEG::seq($url, PEG::optional($title));
        
        return $this->factory->createNodeCreater('httplink', $parser, array('href', 'title'));
    }
    
    protected function createImageLink()
    {
        $url_char = PEG::subtract(PEG::anything(), ']', ':image]');
        
        $url = PEG::join(PEG::seq(PEG::choice('http://', 'https://'),
                                  PEG::many1($url_char)));
                                  
        $parser = PEG::first($url, ':image');
        
        return $this->factory->createNodeCreater('imagelink', $parser);
    }
    
    protected function createLink()
    {
        return PEG::pack('[', PEG::choice($this->imageLink, $this->httpLink), ']');
    }
    
    protected function createDefinition()
    {
        $c = PEG::token(':');
        $sep = PEG::drop($c);
        $factory = $this->factory;
        $parser = PEG::seq($sep, 
                           $factory->createLineSegment($c, true), 
                           $sep, 
                           $factory->createLineSegment($c), 
                           PEG::drop($this->endOfLine));
        return $parser;
    }
    
    protected function createDefinitionList()
    {
        $parser = PEG::many1($this->definition);
        return $this->factory->createNodeCreater('definitionlist', $parser);
    }
    
    protected function createPre()
    {
        $nl = PEG::newLine();
        $closing = PEG::seq(PEG::optional($nl), '|<', $this->endOfLine);
        $line = PEG::second($nl, $this->factory->createLineSegment($closing));
        $parser = PEG::pack('>|', PEG::many1($line), $closing);
        
        return $this->factory->createNodeCreater('pre', $parser);
    }
    
    protected function createSuperPreElement()
    {
        $cond = PEG::not(PEG::seq('||<', $this->endOfLine));
        $elt = PEG::second($cond, $this->lineChar);
        $parser = PEG::third(PEG::newLine(), $cond, PEG::join(PEG::many($elt)));
        
        return $parser;
        
    }
    
    protected function createHeader()
    {
        $parser = PEG::seq(PEG::drop('*'),
                           PEG::count(PEG::many('*')),
                           HatenaSyntax_Util::segment(PEG::many(PEG::choice($this->lineChar, $this->footnote))),
                           PEG::drop($this->endOfLine));
        
        return $this->factory->createNodeCreater('header', $parser, array('level', 'body'));
    }

    protected function createSuperPre()
    {
        $open = PEG::pack('>|', 
                          PEG::join(PEG::many(PEG::secondSeq(PEG::lookaheadNot(PEG::char("\r\n|")), PEG::anything()))),
                          '|');
        $body = PEG::many1($this->superPreElement);
        
        $close = PEG::drop(PEG::optional(PEG::newLine()),
                           '||<',
                           $this->endOfLine);
        
        $parser = PEG::seq($open, $body, $close);
        
        return $this->factory->createNodeCreater('superpre', $parser, array('type', 'body'));
    }

    protected function createList()
    {
        $item = PEG::callbackAction(array('HatenaSyntax_Util', 'processListItem'), PEG::many1(PEG::char('-+')),
                                                                                   $this->lineSegment,
                                                                                   PEG::drop($this->endOfLine));
        $list = PEG::callbackAction(array('HatenaSyntax_Util', 'normalizeList'), PEG::many1($item));
        
        return $this->factory->createNodeCreater('list', $list);
    }

    protected function createTableCell()
    {
        $parser = PEG::seq(PEG::drop('|', PEG::lookaheadNot($this->endOfLine)),
                           PEG::optional('*'),
                           $this->factory->createLineSegment(PEG::token('|'), true));
        return $parser;
    }
    
    protected function createTable()
    {
        $line = PEG::first(PEG::many1($this->tableCell), 
                           '|', 
                           $this->endOfLine);
        $parser = PEG::many1($line);
        
        return $this->factory->createNodeCreater('table', $parser);
    }

    protected function createBlockQuote()
    {
        $url = PEG::join(PEG::seq(PEG::choice('http://', 'https://'), 
                                  PEG::many1(PEG::subtract(PEG::anything(), 
                                                           PEG::seq('>', PEG::newLine()), 
                                                           PEG::newLine()))));
        
        $header = PEG::pack('>', PEG::optional($url), PEG::seq('>', PEG::newLine()));
        
        $elt = PEG::second(PEG::not('<<', $this->endOfLine), $this->element);
        
        $parser = PEG::seq($header, PEG::many1($elt), PEG::drop('<<', $this->endOfLine));
                                      
        return $this->factory->createNodeCreater('blockquote', $parser, array('url', 'body'));
    }
    
    protected function createParagraph()
    {
        $parser = PEG::first($this->lineSegment, $this->endOfLine); 
        
        return $this->factory->createNodeCreater('paragraph', $parser);
    }
    
    protected function createEmptyParagraph()
    {
        $parser = PEG::count(PEG::many1(PEG::newLine()));
        return $this->factory->createNodeCreater('emptyparagraph', $parser);
    }
    
    protected function createElement()
    {
        $parser = PEG::ref($ref);
        $this->elt_ref = &$ref;
        return $parser;
    }

    protected function createParser()
    {
        return $this->factory->createNodeCreater('root', PEG::many($this->element));
    }
    
    protected function setup()
    {
        $this->element;
        $this->elt_ref = PEG::memo(PEG::choice($this->header,
                                               $this->blockQuote,
                                               $this->definitionList,
                                               $this->table,
                                               $this->list,
                                               $this->pre,
                                               $this->superpre,
                                               $this->emptyParagraph,
                                               $this->paragraph));
    }
}