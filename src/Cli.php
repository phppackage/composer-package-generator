<?php
namespace App;

use League\CLImate\CLImate;

class Cli
{
    private $climate;

    public function __construct(App &$app)
    {
        $this->app = $app;
        $this->climate = new CLImate;
    }
    
    private function fix_input_characters($value) {
        return trim(str_ireplace([
            "\e[A", "^[[A", '\e[A', '^[[A', 
            "\e[B", "^[[B", '\e[B', '^[[B', 
            "\e[C", "^[[C", '\e[C', '^[[C', 
            "\e[D", "^[[D", '\e[D', '^[[D', 
        ], null, $value));
    }
    
    public function prompt($msg, $default = null)
    {
        $input = $this->app->cli->input($msg);
        $input->defaultTo($default);
        return $this->fix_input_characters($input->prompt());
    }
    
    public function input($msg)
    {
        return $this->climate->input($msg);
    }
    
    public function error($msg)
    {
        return $this->climate->error($msg);
    }
    
    public function clear()
    {
        $this->climate->clear();
        echo file_get_contents(__DIR__.'/assets/logo.txt');
    }
    
    public function arguments()
    {
        $this->climate->arguments->add([
            'init' => [
                'prefix'      => 'i',
                'longPrefix'  => 'init',
                'description' => 'Create default package.yaml file',
                'noValue'     => true
            ],
            'wizard' => [
                'prefix'      => 'w',
                'longPrefix'  => 'wizard',
                'description' => 'Walkthough in creating your package',
                'noValue'     => true
            ],
            'help' => [
                'prefix'      => 'h',
                'longPrefix'  => 'help',
                'description' => 'Prints a usage statement',
                'noValue'     => true
            ]
        ]);
        
        $this->climate->arguments->parse();
        
        $this->app->arguments = [
            'wizard' => $this->climate->arguments->defined('wizard'),
            'init'   => $this->climate->arguments->defined('init')
        ];

        if (
            !empty($this->app->arguments['wizard']) && 
            !empty($this->app->arguments['init'])
        ) {
            $this->climate->error('Error: choose between --wizard or --init, not both.');
            exit;
        }
    }

}