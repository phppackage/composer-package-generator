<?php
namespace App;

use PHPPackage\MagicClass;

class App extends MagicClass
{
    public function __construct()
    {
        parent::__construct();
        
        $this->cli = new Cli($this);
        $this->package = new Package($this);
        $this->config = new Config($this);
        
        $this->filesystem = new Filesystem($this);
    }
    
    public function run()
    {
        $this->cli->clear();
        $this->cli->arguments();
        
        $this->config->check_php_version(7);
        
        if ($this->arguments['wizard']) {
            $package = $this->package->wizard();
        }
        
        if ($this->arguments['init']) {
            $package = $this->package->init();
        }
        
        // start building package
        $this->filesystem->target_dir = $this->config->basepath().'/MY-PACKAGE';
        $this->filesystem->source_dir = __DIR__.'/setup';
        
        $this->filesystem->create_directory('src');
        $this->filesystem->create_directory('tests/fixtures');
        
        /**
         * Move unchanged files
         */
        foreach ([
            '.gitignore',
            '.scrutinizer.yml',
            '.styleci.yml',
            '.travis.yml',
            'LICENSE',
            'CONTRIBUTING.md',
            'phpunit.xml',
            'tests/bootstrap.php',
        ] as $file) {
            if (file_exists($this->filesystem->source_dir.'/'.$file)) {
                copy($this->filesystem->source_dir.'/'.$file, $this->filesystem->target_dir.'/'.$file);
            }
        }

        /**
         * Process/Create files which change
         */
        
        // README.md
        $authors = [];
        foreach ($package['authors'] as $author) {
            $authors[] = ' - '.sprintf('[%s](%s)', $author['name'], $author['homepage']);
        }
        $this->filesystem->process_file('README.md', [
            'name' => $package['name'],
            'title' => $package['title'],
            'description' => $package['description'],
            'authors' => implode(PHP_EOL, $authors)
        ]);
        
        // composer.json
        file_put_contents(
            $this->filesystem->target_dir.'/composer.json',
            json_encode([
                'name' => $package['name'],
                'type' => 'library',
                'description' => $package['description'],
                'license' => 'MIT',
                'keywords' => $package['keywords'],
                'homepage' => $package['homepage'],
                'authors' => $package['authors'],
                'require' => [
                    'php' => '~5.6|~7.0'
                ],
                'require-dev' => [
                    'phpunit/phpunit' => '4.*',
                    'symfony/thanks' => '^1.0'
                ],
                'autoload' => $package['autoload'],
                'autoload-dev' => $package['autoload-dev'],
                'scripts' => [
                    'test' => 'phpunit --configuration phpunit.xml --coverage-text',
                ],
                'minimum-stability' => 'stable'
            ], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT)
        );
        
        // create placeholder class
        $namespace = rtrim(array_search('src', $package['autoload']['psr-4']), '\\');
        $testName = str_replace([' ', $package['vendor']], null, ucwords(str_replace('\\', ' ', $namespace)));
        
        $authors = [];
        foreach ($package['authors'] as $author) {
            $authors[] = ' |   '.sprintf('%s <%s>', $author['name'], $author['email']);
        }
        
        // create class
        file_put_contents($this->filesystem->target_dir.'/src/'.$testName.'.php', '<?php
/*
 +-----------------------------------------------------------------------------+
 | '.$package['vendor'].' - '.$package['title'].'
 +-----------------------------------------------------------------------------+
 | Copyright (c)'.date('Y').' ('.$package['homepage'].')
 +-----------------------------------------------------------------------------+
 | This source file is subject to MIT License
 | that is bundled with this package in the file LICENSE.
 |
 | If you did not receive a copy of the license and are unable to
 | obtain it through the world-wide-web, please send an email
 | to '.$package['authors'][0]['email'].' so we can send you a copy immediately.
 +-----------------------------------------------------------------------------+
 | Authors:
'.implode(PHP_EOL, $authors).'
 +-----------------------------------------------------------------------------+
 */

namespace '.$namespace.';

class '.$testName.'
{

    /**
     *
     */
    public function __construct()
    {

    }

    /**
     *
     */
    public function exampleMethod()
    {
        return \'foobar\';
    }

}'.PHP_EOL);

        // create unit test
        file_put_contents($this->filesystem->target_dir.'/tests/'.$testName.'Test.php', '<?php

namespace '.$namespace.';

use PHPUnit\Framework\TestCase;

class '.$testName.'Test extends TestCase
{

    /**
     *
     */
    public function setUp()
    {
        $this->instance = new '.$testName.'();
    }

    /**
     * @coversNothing
     */
    public function testTrueIsTrue()
    {
        $this->assertTrue(true);
    }
    
    /**
     * has class initialised?
     */
    public function testObjectInstanceOf()
    {
        $this->assertInstanceOf(\'\\'.$namespace.'\\'.$testName.'\', $this->instance);
    }
    
    /**
     * @covers \\'.$namespace.'\\'.$testName.'::exampleMetod()
     */
    public function testExampleMethod()
    {
        $this->assertEquals(\'foobar\', $this->instance->exampleMethod());             
    }

}'.PHP_EOL);

        echo 'Your package files have been successfully generated!'.PHP_EOL;

        /**
         * Ask question, do callback.
         */
        $ask = function($options, $callback) {
            $response = null;
            do {
                $response = readline($options['question']);
            } while (!in_array($response, $options['expected']));
            readline_add_history($response);
            
            return $callback($response);
        };
        
        $yesno = ['y', 'yes', 'n', 'no'];

        $ask([
            'question' => 'Would you like to run composer install and run tests? [yes|no]:',
            'expected' => $yesno
        ], function ($response) {
            if (in_array($response, ['y', 'yes'])) {
                `composer install`;
                echo `composer test`;
            }
        });
        
        echo 'Happy coding! - If you liked this, star it!'.PHP_EOL;
    }

}
