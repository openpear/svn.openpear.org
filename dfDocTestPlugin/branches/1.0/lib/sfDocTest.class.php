<?php
/**
 *
 */
class sfDocTest
{
    public static function getCacheDir(){
        return "cache/".SF_APP."/".SF_ENVIRONMENT."/sfDocTestPlugin";
    }

    public static function getTestFile($file){
        $tester =  str_replace(SF_ROOT_DIR."/","",$file);
        $dir = sfDocTest::getCacheDir()."/tests/".dirname($tester);
        if(!is_dir($dir)){
            mkdir($dir,0777,true);
        }
        return sprintf("%s/%s",$dir,basename($tester));
    }
    public static function compile_if_modified($file){
        $cache = new sfFunctionCache(sfDocTest::getCacheDir());
        $id = md5(serialize(array("sfDocTest::compile", $file)));
        if($cache->lastModified($id) < filemtime($file)){
            $cache->remove($id);
        }
        $test = $cache->call("sfDocTest::compile", $file);
        $testfile = sfDocTest::getTestFile($file);
        file_put_contents($testfile,$test);
        return $testfile;
    }
    /**
     */
    public static function compile($file){
        $body = file_get_contents($file);
        $docs = sfDocTest::parse($body);
        if(!count($docs)) return;
        $out = "<?php\n";
        $out .= "
define('SF_ROOT_DIR',    '".SF_ROOT_DIR."');
define('SF_APP',         '".SF_APP."');
define('SF_ENVIRONMENT', '".SF_ENVIRONMENT."');
define('SF_DEBUG',       1);
require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');\n
";
        $out .= "require_once(\$sf_symfony_lib_dir.'/vendor/lime/lime.php');\n

\$databaseManager = new sfDatabaseManager();
\$databaseManager->initialize();

";
        
        
        $out .= "
if(!function_exists('pake_desc')){
  function pake_desc(){}
}
if(!function_exists('pake_task')){
  function pake_task(){}
}
require_once \"${file}\";
";
        $out .= "\$__test = new lime_test(null,new lime_output_color);\n
\$__test->comment('file: $file');
//ob_start();
";


        
        foreach($docs as $doc){
            $out.=sfDocTest::compile_doc($doc);
        }
        return $out;
    }
    public static function compile_doc($doc){
        $lines = explode("\n",$doc);
        $start = false;
        $compiled = "";
        $code =false;
        foreach($lines as $line){
            if(preg_match("/ +\* #test *(.*)/",$line,$m)){
                $start = true;
                $compiled.=sprintf("#comment(\"test: %s\");\n",$m[1]);
                continue;
            }
            if($start){
                if(preg_match("/^ +\* @/",$line)){
                    break;
                }
                if(preg_match("/^ +\* <code> */",$line)){
                    $code = true;
                    continue;
                }
                if(preg_match("/^ +\* <\/code> */",$line)){
                    $code = false;
                    continue;
                }
                if($code){
                    if(preg_match("/^ +\* +(.*)/",$line,$m)){
                        $compiled.=$m[1];
                        $compiled.="\n";
                    }
                }
            }
        }
        return sfDocTest::expand_macro($compiled);
    }
    public static function expand_macro($compiled){
        $compiled
            = preg_replace("/^#eq\(/m","#is(",$compiled);
        $compiled
            = preg_replace("/^#true\(/m","#ok(",$compiled);
        $compiled
            = preg_replace("/^#false\(/m","#ok(!",$compiled);
        
        return preg_replace("/^#([a-z_]+)\(/m","\$__test->\${1}(",$compiled);
    }
    public static function parse($body){
        $tokens = token_get_all($body);
        $doccoments = array();
        foreach($tokens as $token){
            if(!is_string($token)){
                list($id, $text) = $token;
                switch ($id) {
                case T_DOC_COMMENT:
                    if(preg_match("/ +\* +#test */",$text)){
                        $doccoments[] = $text;
                    }
                    break;
                }
            }
        }
        return $doccoments;
    }
    
}

