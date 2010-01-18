<?php
/**
* Lazy Reflection
* - Scraping PHP Class.
*
* @author Shuhei Tanuma
* @copyright 2010 Shuhei Tanuma
* @created 23:05 2010/01/18
* @licence Apache License 2.0
*
*   Copyright 2010 Shuhei Tanuma
*
*   Licensed under the Apache License, Version 2.0 (the "License");
*   you may not use this file except in compliance with the License.
*   You may obtain a copy of the License at
*
*       http://www.apache.org/licenses/LICENSE-2.0
*
*   Unless required by applicable law or agreed to in writing, software
*   distributed under the License is distributed on an "AS IS" BASIS,
*   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
*   See the License for the specific language governing permissions and
*   limitations under the License.
*/
class Lazy_Reflection{
  public $data;
  public $template;
  
  public $class_name;
  public $class_visibility;
  public $parents;

  public $constants;
  public $properties;
  
  public $comments;
  public $multiline_comments;

  public $methods;
  
  public $left_delimiter;
  public $right_delimiter;
  
  const DEFAULT_LEFT_DELIMITER  = "<%";
  const DEFAULT_RIGHT_DELIMITER = "%>";

  const REGEXP_PHP_CLASS    = "/(?P<visibility>abstract)?\s*class\s*(?P<class_name>[a-zA-Z0-9_-]+?)\s*((?:extends\s+)(?P<parents_class>[a-zA-Z0-9_-]+)\s*)?(?P<braces>\{(?P<data>((?>[^{}]+)|(?P>braces))*)\})/";
  const REGEXP_PHP_COMMENT  = "/((?ms)(?<multiline_comment>\/\*.+?\*\/)(?-ms)|(?P<comment>\/\/.+))/";
  const REGEXP_PHP_METHOD   = "/((?P<visibility>(public|protected|private)(\s+static)?)\s*)?function\s*(?P<name>[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*\((?P<attributes>(.*?))\)\s*(?P<braces>\{(?P<data>((?>[^{}]+)|(?P>braces))*)\})/";
  const REGEXP_PHP_PROPERTY = "/(?P<visibility>(public|protected|private|const)(\s*static)?)?\s*(?P<name>(((?<!const)\\$|)[a-zA-Z0-9_-]+))\s*(?:=\s+(?P<right>(?P<s_quote>'((?>[^']+)|(?P>s_quote))*')|(?P<d_quote>\"((?>[^\"]+)|(?P>d_quote))*\")|.+(?P<paren>\(((?>[^()]+)|(?P>paren))*\))|[a-zA-Z0-9.+-]+);|;)/";
  const REGEXP_PHP_VARNAME = "/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/";

  const CLASS_VISIBILITY = "class_visibility";
  const CLASS_NAME       = "class_name";
  const CLASS_PARENTS    = "parents_class";

  public function __construct(){
    $this->left_delimiter   = self::DEFAULT_LEFT_DELIMITER;
    $this->right_delimiter  = self::DEFAULT_RIGHT_DELIMITER;
  }
  
  private function createTag($string)
  {
    return sprintf("%s %s %s",$this->left_delimiter,$string,$this->right_delimiter);
  }
  
  private function parseClass()
  {
    $data = $this->data;
    if(preg_match(self::REGEXP_PHP_CLASS,$data,$matches)){
      $data = $matches[0];
      preg_match(self::REGEXP_PHP_CLASS,$data,$class_matches,PREG_OFFSET_CAPTURE);
      $this->class_name = $class_matches['class_name'][0];
      $data = preg_replace("/" . preg_quote($this->class_name) . "/" ,$this->createTag("class_name"),$data,1);
      
      if($class_matches['parents_class'][0] !=""){
        $this->parents = $class_matches['parents_class'][0];
        $data = preg_replace("/" . preg_quote($this->parents) . "/",$this->createTag("parents_class"),$data,1);
      }
      if($class_matches['visibility'][0] != ""){
        $this->class_visibility = $class_matches['visibility'][0];
        $data = substr_replace($data,$this->createTag("class_visibility"),$class_matches['visibility'][1],strlen($class_matches['visibility'][0]));
      }

      $this->data = $class_matches['data'][0];
      $comment_count = 0;
      while(preg_match(self::REGEXP_PHP_COMMENT,$data,$comment_matches,PREG_OFFSET_CAPTURE)){
        if(!empty($comment_matches['multiline_comment']['0'])){
          $this->comments[] = $comment_matches['multiline_comment']['0'];

          $data = substr_replace($data,$this->createTag("comments[{$comment_count}]"),$comment_matches['multiline_comment']['1'],strlen($comment_matches['multiline_comment']['0']));
          $comment_count++;
        }else{
          $this->comments[] = $comment_matches['comment']['0'];

          $data = substr_replace($data,$this->createTag("comments[{$comment_count}]"),$comment_matches['comment']['1'],strlen($comment_matches['comment']['0']));
          $comment_count++;
        }
      }

      $method_count = 0;
      while(preg_match(self::REGEXP_PHP_METHOD,$data,$matches,PREG_OFFSET_CAPTURE)){
        $method = new Lazy_Method();
        $method->setAttributes($matches['attributes'][0]);
        $method->setVisibility((empty($matches['visibility'][0]) ? "public" : $matches['visibility'][0]));
        $method->setName($matches['name'][0]);
        $method->setData($matches['data'][0]);
        $this->methods[] = $method;

        $data = substr_replace($data,$this->createTag("methods[{$method_count}]"),$matches['0']['1'],strlen($matches['0']['0']));
        $method_count++;
      }

      $property_count = 0;
      while(preg_match(self::REGEXP_PHP_PROPERTY,$data,$matches,PREG_OFFSET_CAPTURE)){
        $this->properties[] = array(
          "visibility"=> (empty($matches['visibility'][0]) ? "public" : $matches['visibility'][0]),
          "name"=>$matches['name'][0],
          "data"=>(isset($matches['right'][0])) ? $matches['right'][0] : null
        );
        
        $data = substr_replace($data,$this->createTag("properties[{$property_count}]"),$matches['0']['1'],strlen($matches['0']['0']));
        $property_count++;
      }
      $this->template = explode("\r\n",$data);
    }
  }
  
  public static function LoadString($string){
    $reflection = new Lazy_Reflection();
    $reflection->data = $string;
    $reflection->parseClass();
    
    return $reflection;
  }
  
  
  public function getMethod($method_name){
  	$index = false;
  	//var_dump($method_name);
  	if($this->hasMethod($method_name,$index)){
  		return $this->methods[$index];
  	}
  }

  public function hasMethod($method_name,&$index=null)
  {
    if(is_array($this->methods)){
      foreach($this->methods as $method_index=>$method){
        if($method->getName() == $method_name){
          $index = $method_index;
          return true;
        }
      }
    }

    return false;
  }
  
  public function hasProperty($property_name)
  {
    if(is_array($this->properties)){
      foreach($this->properties as $property){
        if($property['name'] == $property_name){
          return true;
        }
      }
    }

    return false;
  }
  
  
  public function getDocComment($method_name){
    $methods = $this->methods;
    foreach($methods as $index=>$data){
    	if($data->name == $method_name){
    		$method_index = $index;
    	}
    }

	//テンプレートの位置を調べる
    foreach($this->template as $index=>$data){
    	if(strpos($data,"methods[{$method_index}]")!==false){
    		//メソッドの直前にdoccommentがあるか？
    		if(preg_match("/comments\[(?P<number>\d+)\]/",$this->template[$index-1],$match)){
    			if(strpos($this->comments[$match['number']],"/**") === 0){
    				$doc = $this->comments[$match['number']];
    				$doc = preg_replace("|^/\*\*|","",$doc);
    				$doc = preg_replace("|\*/$|","",$doc);
    				$docs = explode("\r\n",$doc);
    				if(count($docs)){
	    				foreach($docs as $idx=>$dat){
	    					$docs[$idx] = ltrim($dat," *\t");
	    				}
	    				$doc = trim(join("\r\n",$docs));
    				}else{
    					$doc = ltrim($dat," *\t");
    				}
    				return $doc;
    			}
    		}
    	}
    }
  }

  public function outputClass()
  {
    $data = join("\r\n",$this->template);
    while(preg_match("/" . $this->createTag("\s*(?<var_name>.+?)\s*") . "/",$data,$matches)){
      switch($matches['var_name']){
        case self::CLASS_VISIBILITY:
          $data = preg_replace("/{$this->left_delimiter}\s*{$matches['var_name']}\s*{$this->right_delimiter}/",$this->class_visibility,$data);
          break;
        case self::CLASS_NAME:
          $data = preg_replace("/{$this->left_delimiter}\s*{$matches['var_name']}\s*{$this->right_delimiter}/",$this->class_name,$data);
          break;
        case self::CLASS_PARENTS:
          $data = preg_replace("/{$this->left_delimiter}\s*{$matches['var_name']}\s*{$this->right_delimiter}/",$this->parents,$data);
          break;
        default:
          if(preg_match("/(?<var_name>.+?)\[(?<index>\d+)\]/",$matches['var_name'],$matche2)){
            $tmp = $this->$matche2['var_name'];
            if($matche2['var_name'] == "properties"){
              $property = $tmp[$matche2['index']];
              $replace = sprintf("%s%s%s", 
                (!empty($property['visibility'])) ? $property['visibility'] . " " : "public ",
                $property['name'],
                (!empty($property['data'])) ? " = {$property['data']};" : ";"
              );

              $data = preg_replace("/{$this->left_delimiter}\s*" . preg_quote($matches['var_name']) . "\s*{$this->right_delimiter}/",$replace,$data);
            }else if($matche2['var_name'] == "comments"){
              $comment = $tmp[$matche2['index']];
              $data = preg_replace("/{$this->left_delimiter}\s*" . preg_quote($matches['var_name']) . "\s*{$this->right_delimiter}/",$comment,$data);

            }else if($matche2['var_name'] == "methods"){
              $method = $tmp[$matche2['index']];
              $data = preg_replace("/{$this->left_delimiter}\s*" . preg_quote($matches['var_name']) . "\s*{$this->right_delimiter}/",(string)$method,$data);
            }else{
              var_dump($matche2['var_name']);
            }
           
          }
      }
    }
    return $data;
  }
  
  public function setMethod(Lazy_Method $method)
  {
    if(!($flag = $this->hasMethod($method->getName(),$index))){
      $index = count($this->methods)+1;

      $tmp2 = $this->template[count($this->template)-1];
      $c = count($this->methods)+1;
      $tmp3 = $this->createTag("methods[" . $c . "]");
      $this->template[count($this->template)-1] = "";
      $this->template[count($this->template)]   = "  " . $tmp3;
      $this->template[count($this->template)+1] = $tmp2;
    }
    $this->methods[$index] = $method;
    return true;
  }
  
  public function removeMethod($method_name){
    if(($flag = $this->hasMethod($method_name,$index))){
      unset($this->methods[$index]);
      return true;
    }else{
      return false;
    }
  }
}

class Lazy_Method{
  public $name;
  public $visibility;
  public $attributes;
  public $data;
  
  public function __construct(){
    $this->name = "";
    $this->attributes = "";
    $this->visibility = "public";
    $this->data = "";
  }
  
  public function __call($name,$arguments){
    if(preg_match("/(?P<type>set|get)(?<name>[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/",strtolower($name),$object)){
      switch($object['type']){
        case "get":
          if(isset($this->$object['name'])){
            return $this->$object['name'];
          }
          break;
        case "set":
          if(isset($this->$object['name'])){
            $this->$object['name'] = $arguments[0];
          }
          break;
      }
    }else{
      die("Can't execute magic method {$name}.\r\n");
    }
  }
  
  public function __toString(){
    return sprintf("%sfunction %s(%s)\r\n  {%s}",
          (($tmp = $this->getVisibility())) ? $tmp . " " : null,
          $this->getName(),
          $this->getAttributes(),
          $this->getData()
        );
  }
}

class Lazy_Class{
  private $name;
  private $type;
  private $parents;
  private $data;
}
