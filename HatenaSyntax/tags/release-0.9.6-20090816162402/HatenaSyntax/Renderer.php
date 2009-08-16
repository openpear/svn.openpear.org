<?php
/**
 * @package HatenaSyntax
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */

class HatenaSyntax_Renderer
{
    protected $config, $footnote, $fncount, $padding, $encoding;
    
    function __construct(Array $config = array())
    {
        $this->config = $config + array(
            'headerlevel' => 1,
            'htmlescape' => true,
            'id' => uniqid('sec'),
            'sectionclass' => 'section',
            'footnoteclass' => 'footnote',
            'superprehandler' => array($this, 'superPreHandler')
        );
    }
    
    function render(HatenaSyntax_Node $node)
    {
        $this->footnote = '';
        $this->fncount = 0;
        $this->padding = 0;

        $ret = $this->renderNode($node);
        $ret = '<div class="' . $this->config['sectionclass'] . '">' . PHP_EOL . $ret . PHP_EOL . '</div>' . PHP_EOL;
        if ($this->fncount > 0) {
            $ret .= PHP_EOL . PHP_EOL . '<div class="' . $this->config['footnoteclass'] . '">' . 
                    PHP_EOL . $this->footnote .  '</div>';
        }
        
        return $ret;
    }
    
    static function superPreHandler($type, $lines)
    {
        $body = join(PHP_EOL, array_map(array('HatenaSyntax_Renderer', 'escape'), $lines));
        return '<pre class="superpre">' . PHP_EOL . $body . '</pre>';
    }
    
    protected function renderNode(HatenaSyntax_Node $node)
    {
        $this->padding++;
        $ret = $this->{'render' . $node->getType()}($node->getData());
        $this->padding--;
        return $ret;
    }
    
    protected function renderRoot(Array $arr)
    {
        $this->padding--;
        foreach ($arr as &$elt) $elt = $this->renderNode($elt);
        $this->padding++;
        return join(PHP_EOL, $arr);
    }
    
    protected function renderHeader(Array $data)
    {
        $level = $data['level'] + $this->config['headerlevel'];        
        return $this->line("<h{$level}>" . $this->renderLineSegment($data['body']) . "</h{$level}>");
    }
    
    protected function renderLineSegment(Array $data)
    {
        foreach ($data as &$elt) 
            $elt = !$elt instanceof HatenaSyntax_Node ? ($this->config['htmlescape'] ? $this->escape($elt) : $elt) 
                                                      : $this->renderNode($elt);
        return join('', $data);
    }
    
    protected function renderFootnote(Array $data)
    {
        $this->fncount++;
        $id = $this->config['id'];
        $n = $this->fncount;
        $title = $body = $this->renderLineSegment($data);
        $title = strip_tags($body);
        if (!$this->config['htmlescape']) {
            $title = $this->escape($title);
        }
        
        $this->footnote .= sprintf('  <p><a href="#%s_%d" name="%s_footnote_%d">*%d</a>: %s</p>' . PHP_EOL, $id, $n, $id , $n, $n, $body);
        return sprintf('(<a href="#%s_footnote_%d" name="%s_%d" title="%s">*%d</a>)', $id, $n, $id, $n, $title, $n);
    }
    
    protected function renderHttpLink(Array $data)
    {
        list($href, $title) = array($data['href'], $data['title']);
        $title = $title ? $title : $href;
        if ($this->config['htmlescape']) $title = $this->escape($title); 
        $href = $this->escape($href);
        return sprintf('<a href="%s">%s</a>', $href, $title);
    }
    
    protected function renderImageLink($url)
    {
        $url = self::escape($url);
        return '<a href="' . $url . '"><img src="' . $url . '" /></a>';
    }
    
    protected function renderRelativeLink($path)
    {
        $path = self::escape($path);
        return '<a href="' . $path . '">' . $path . '</a>';
    }
    
    protected function renderDefinitionList(Array $data)
    {
        foreach ($data as &$elt) $elt = $this->renderDefinition($elt);
        return join(PHP_EOL, array($this->line('<dl>'), join(PHP_EOL, $data), $this->line('</dl>')));
    }
    
    protected function renderDefinition(Array $data)
    {
        list($dt, $dd) = $data;
        $ret = array();
        if ($dt) $ret[] = $this->line('<dt>' . $this->renderLineSegment($dt) . '</dt>', 1);
        $ret[] = $this->line('<dd>' . $this->renderLineSegment($dd) . '</dd>', 1);
        return join(PHP_EOL, $ret);
    }
    
    protected function renderPre(Array $data)
    {
        $ret = array();
        $ret[] = $this->line('<pre>');
        foreach ($data as &$elt) $elt = $this->renderLineSegment($elt);
        $ret[] = join(PHP_EOL, $data) . '</pre>';
        return join(PHP_EOL, $ret);
    }
    
    protected function renderSuperPre(Array $data)
    {
        $ret = array();
        list($type, $lines) = array($data['type'], $data['body']);
        $ret[] = call_user_func($this->config['superprehandler'], $type, $lines);
        return join(PHP_EOL, $ret);
    }
    
    protected function renderTable(Array $data)
    {
        $ret = array();
        $ret[] = $this->line('<table>');
        $this->padding++;
        foreach ($data as $tr) {
            $ret[] = $this->line('<tr>');
            foreach ($tr as $td) $ret[] = $this->renderTableCell($td[0], $td[1]);
            $ret[] = $this->line('</tr>');
        }
        $this->padding--;
        $ret[] = $this->line('</table>');
        return join(PHP_EOL, $ret);
    }
    
    protected function renderTableCell($header, $segment)
    {
        $tag = $header ? 'th' : 'td'; 
        $ret = $this->line("<{$tag}>" . $this->renderLineSegment($segment) . "</{$tag}>", 1);
        return $ret;
    }
    
    protected function renderBlockQuote(Array $arr)
    {
        $ret = array();
        $ret[] = $this->line('<blockquote>');
        foreach ($arr['body'] as $elt) $ret[] = $this->renderNode($elt);
        if ($arr['url']) $ret[] = $this->line('<cite><a href="' . self::escape($arr['url']) . '">' . self::escape($arr['url']) . '</a></cite>');
        $ret[] = $this->line('</blockquote>');
        return join(PHP_EOL, $ret);
    }
    
    protected function renderParagraph(Array $data)
    {
        return $this->line('<p>' . $this->renderLineSegment($data) . '</p>');
    }
    
    protected function renderEmptyParagraph($data)
    {
        $ret = array();
        for ($data--; $data > 0; $data--) $ret[] = $this->line('<br>');
        return join(PHP_EOL, $ret);
    }
    
    protected function renderList(Array $data)
    {
        $this->padding--;
        $ret = $this->renderListItem($data);
        $this->padding++;
        return $ret;
    }
    
    protected function renderListItem(Array $data)
    {
        $this->padding++;
        if (is_string($data[0])) { // leaf case
            $result = $this->line('<li>' . $this->renderLineSegment($data[1]) . '</li>');
        }
        else {
            $buf = array();
            $name = $data[0][0] === '+' ? 'ol' : 'ul';
            $buf[] = $this->line("<{$name}>");
            foreach ($data as $elt) $buf[] = $this->renderListItem($elt);
            $buf[] = $this->line("</{$name}>");
            $result = join(PHP_EOL, $buf);
        }
        $this->padding--;
        return $result;
    }
    
    protected function line($str = '', $padding = 0)
    {
        return str_repeat('  ', max($this->padding + $padding, 0)) . $str;
    }
    
    protected static function escape($str)
    {
        return htmlspecialchars($str, ENT_QUOTES);
    }
}
