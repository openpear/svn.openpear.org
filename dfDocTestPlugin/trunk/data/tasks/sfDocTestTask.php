<?php
pake_desc('doctest for all *.php in this project');
pake_task('doctest-all','project_exists');
pake_desc('doctest in this project');
pake_task('doctest','project_exists');

require_once(sfConfig::get('sf_symfony_lib_dir').'/vendor/lime/lime.php');

function run_doctest($task,$args)
{
    if (count($args) < 1){
        throw new Exception('You must provide the app to test.');
    }
    $app = array_shift($args);

    define('SF_ROOT_DIR', sfConfig::get('sf_root_dir'));
    define('SF_APP',         $app);
    define('SF_ENVIRONMENT', 'dev');
    $finder = sfFinder::type('file')
        ->ignore_version_control()
        ->prune(array("cache","log",".*/vendor"))
        ->follow_link();
    foreach($args as $arg){
        if(file_exists($arg)){
            if(is_dir($arg)){
                $files = $finder
                    ->name("*.php")->in($arg);
            }else{
                $files[] = $arg;
            }
        }else{
            $finder->name($arg);
            $files = $finder->in(sfConfig::get('sf_root_dir'));
        }
        
        $h = new lime_harness(new lime_output_color());
        if (DIRECTORY_SEPARATOR == '\\' || !function_exists('posix_isatty') || !@posix_isatty(STDOUT)){
            $tty = "";
        }else{
            $tty = ">".posix_ttyname(STDOUT);
        }

        foreach($files as $file){
            if(is_readable($file)){
                // echo sfDocTest::compile_if_modified($file);
                passthru(sprintf
                         ("%s -d html_errors=off -d open_basedir= -q %s 2>&1 %s"
                          ,$h->php_cli
                          ,sfDocTest::compile_if_modified($file)
                          ,$tty));
            }
        }
    }
}
function run_doctest_all($task,$args)
{
    if (!count($args)){
        throw new Exception('You must provide the app to test.');
    }
    $app = $args[0];
    
    define('SF_ROOT_DIR', sfConfig::get('sf_root_dir'));
    define('SF_APP',         $app);
    define('SF_ENVIRONMENT', 'dev');

    $finder = sfFinder::type('file')
        ->ignore_version_control()
        ->prune(array("cache","log",".*/vendor"))
        ->follow_link()->name("*.php");
    $files = $finder->in(sfConfig::get('sf_root_dir'));
    $h = new lime_harness(new lime_output_color);
    foreach($files as $file){
        $h->register(sfDocTest::compile_if_modified($file));
    }
    $h->run();
}
