<?php
namespace App;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class Filesystem
{
    public $target_dir;
    public $source_dir;
    
    public function __construct(App &$app)
    {
        $this->app = $app;
    }
    
    public function create_directory($path = '')
    {
        if (!file_exists($this->target_dir.'/'.$path)) {
            mkdir($this->target_dir.'/'.$path, 0755, true);
        }
    }
    
    /**
     * Replace placeholders in files
     */
    public function process_file($filename, $replace)
    {
        file_put_contents(
            $this->target_dir.'/'.$filename,
            preg_replace_callback("/{{([\w_]{1,})}}/", function ($match) use ($replace) {
                return array_key_exists($match[1], $replace) ? $replace[$match[1]] : '';
            }, file_get_contents($this->source_dir.'/'.$filename))
        );
    }
    
    public function create_file()
    {
        
    }
    
    public function move_file()
    {
        
    }
    
}