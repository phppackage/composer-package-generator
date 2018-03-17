<?php
namespace App;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class Config
{
    public $package = [];
    
    public function __construct(App &$app)
    {
        $this->app = $app;
    }
    
    public function check_php_version($min_version = 7) 
    {
        if (PHP_MAJOR_VERSION < $min_version) {
            die("\033[0;31mError: >= PHP {$min_version}.0 required!\033[0m Your current version is: ".PHP_VERSION.PHP_EOL);
        }
    }
    
    public function basepath()
    {
        $basepath = '.';
        if (!empty($pharPath = \Phar::running(false))) {
            $basepath = dirname($pharPath);
        }
        return $basepath;
    }
    
    public function load_config()
    {
        $basepath = $this->basepath();
        
        if (file_exists($basepath.'/package.yaml')) {
            try {
                $this->package = Yaml::parseFile($basepath.'/package.yaml');
            } catch (ParseException $e) {
                //
            }
        }
    }
}