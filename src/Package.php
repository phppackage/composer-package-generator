<?php
namespace App;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class Package
{

    public function __construct(App &$app)
    {
        $this->app = $app;
    }
    
    public function basepath()
    {
        $basepath = '.';
        if (!empty($pharPath = \Phar::running(false))) {
            $basepath = dirname($pharPath);
        }
        return $basepath;
    }

    public function config()
    {
        $package = [
            'name' => '',
            'title' => '',
            'description' => '',
            'type' => 'library',
            'keywords' => [],
            'homepage' => '',
            'authors' => [
                [
                    'name' => '',
                    'email' => '',
                    'homepage' => '',
                    'role' => 'Owner'
                ]
            ],
            'autoload' => [
                'psr-4' => []
            ],
            'autoload-dev' => [
                'psr-4' => []
            ]
        ];
        
        $basepath = $this->basepath();
        
        if (file_exists($basepath.'/package.yaml')) {
            try {
                $package = Yaml::parseFile($basepath.'/package.yaml')+$package;
            } catch (ParseException $e) {
                $this->app->cli->error(
                    sprintf(
                        'Error: unable to parse package.yaml near: %s line %s.', 
                        $e->getSnippet(), 
                        $e->getParsedLine()
                    )
                );
                exit;
            }
        }
        
        // get vendor/package
        if (!empty($package['name'])) {
            list($package['vendor'], $package['package']) = explode('/', $package['name']);
        }
        
        return $package;
    }
    
    public function save_config($package = [])
    {
        file_put_contents(
            $this->basepath().'/package.yaml',
            Yaml::dump($package)
        );
    }
    
    public function wizard()
    {
        $package = $this->config();
        
        $fix_event_characters = function ($value) {
            return trim(str_ireplace([
                "\e[A", "^[[A", '\e[A', '^[[A', 
                "\e[B", "^[[B", '\e[B', '^[[B', 
                "\e[C", "^[[C", '\e[C', '^[[C', 
                "\e[D", "^[[D", '\e[D', '^[[D', 
            ], null, $value));
        };
        
        // vendor
        $package['vendor'] = $this->app->cli->prompt(
            'Enter vendor name ['.(!empty($package['vendor']) ? $package['vendor'] : 'phppackage').']:',
            (!empty($package['vendor']) ? $package['vendor'] : 'phppackage')
        );
        
        // package
        $package['package'] = $this->app->cli->prompt(
            'Enter package name ['.(!empty($package['package']) ? $package['package'] : 'my-package').']:',
            (!empty($package['package']) ? $package['package'] : 'my-package')
        );
        
        // namespaces
        $package['autoload']['psr-4'] = [$package['vendor'].'\\'.$package['package'].'\\' => 'src'];
        $package['autoload-dev']['psr-4'] = [$package['vendor'].'\\'.$package['package'].'\\Tests\\' => 'tests'];

        // name
        $package['name'] = strtolower($package['vendor']).'/'.strtolower($package['package']);
        
        // title
        $package['title'] = $this->app->cli->prompt(
            'Enter package title ['.(!empty($package['title']) ? $package['title'] : 'my-title').']:',
            (!empty($package['title']) ? $package['title'] : 'My Package')
        );

        // description
        $package['description'] = $this->app->cli->prompt(
            'Enter package description:'.(!empty($package['description']) ? ' '.$package['description'] : null),
            (!empty($package['description']) ? $package['description'] : null)
        );

        // keywords
        $keywords = $this->app->cli->prompt(
            'Enter package keywords ['.(!empty($package['keywords']) ? implode(', ', $package['keywords']) : 'space separated').']:',
            (!empty($package['keywords']) ? implode(', ', $package['keywords']) : null)
        );
        $keywords = explode(' ', $keywords);
        array_walk($keywords, function(&$value, $key) use ($fix_event_characters) {
            $value = $fix_event_characters(trim($value, ','));
        });
        $keywords = array_filter($keywords);
        $package['keywords'] = $keywords;
        
        // homepage
        $package['homepage'] = $this->app->cli->prompt(
            'Enter homepage ['.(!empty($package['homepage']) ? $package['homepage'] : 'http://github.com/'.$package['vendor'].'/'.$package['package']).']:',
            (!empty($package['homepage']) ? $package['homepage'] : 'http://github.com/'.$package['vendor'].'/'.$package['package'])
        );
        $package['authors'][0]['homepage'] = $package['homepage'];
        
        // authors - name
        $package['authors'][0]['name'] = $this->app->cli->prompt(
            'Enter your name:'.(!empty($package['authors'][0]['name']) ? ' ['.$package['authors'][0]['name'].']:' : null),
            (!empty($package['authors'][0]['name']) ? $package['authors'][0]['name'] : null)
        );

        // authors - email
        $package['authors'][0]['email'] = $this->app->cli->prompt(
            'Enter your email:'.(!empty($package['authors'][0]['email']) ? ' ['.$package['authors'][0]['email'].']:' : null),
            (!empty($package['authors'][0]['email']) ? $package['authors'][0]['email'] : null)
        );

        // save
        $this->save_config($package);
        
        return $package;
    }
    
    public function init()
    {
        $package = $this->config();

        // vendor
        $package['vendor'] = (!empty($package['vendor']) ? $package['vendor'] : 'phppackage');

        // package
        $package['package'] = (!empty($package['package']) ? $package['package'] : 'my-package');
        
        // namespaces
        $package['autoload']['psr-4'] = [$package['vendor'].'\\'.$package['package'].'\\' => 'src'];
        $package['autoload-dev']['psr-4'] = [$package['vendor'].'\\'.$package['package'].'\\Tests\\' => 'tests'];

        // name
        $package['name'] = strtolower($package['vendor']).'/'.strtolower($package['package']);
        
        // title
        $package['title'] = (!empty($package['title']) ? $package['title'] : 'My Package');

        // description
        $package['description'] = (!empty($package['description']) ? $package['description'] : 'My package description.');

        // keywords
        $package['keywords'] = (!empty($package['keywords']) ? $package['keywords'] : ['package', 'keywords']);
        
        // homepage
        $package['homepage'] = (!empty($package['homepage']) ? $package['homepage'] : 'http://github.com/'.$package['vendor'].'/'.$package['package']);
        
        // authors - homepage
        $package['authors'][0]['homepage'] = $package['homepage'];
        
        // authors - name
        $package['authors'][0]['name'] = (!empty($package['authors'][0]['name']) ? $package['authors'][0]['name'] : null);

        // authors - email
        $package['authors'][0]['email'] = (!empty($package['authors'][0]['email']) ? $package['authors'][0]['email'] : null);
        
        // save
        $this->save_config($package);
        
        return $package;
    }
    
}