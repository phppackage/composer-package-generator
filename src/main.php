<?php
namespace App;

$app = new App;
$app->run();

//print_r($app);

die;

//$app->climate = new CLImate;
//$app->config = new Config;

//echo file_get_contents(__DIR__.'/assets/logo.txt');

$app->climate->arguments->add([
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

$app->climate->arguments->parse();

$arguments['wizard'] = $app->climate->arguments->defined('wizard');
$arguments['init']   = $app->climate->arguments->defined('init');

if (!empty($arguments['wizard']) && !empty($arguments['init'])) {
    $app->climate->error('Error: choose between --wizard or --init, not both.');
}

// do wizard
if ($arguments['wizard']) {
    // vendor
    $input = $app->climate->input('Enter vendor name [phppackage]:');
    $input->defaultTo('phppackage');
    $package['vendor'] = $input->prompt();
    
    //package
    $input = $app->climate->input('Enter package name [my-package]:');
    $input->defaultTo('my-package');
    $package['package'] = $input->prompt();
    
    //description
    $input = $app->climate->input('Enter package description:');
    $input->defaultTo('my package description, which I will fill out later.');
    $package['description'] = $input->prompt();
    
    //homepage
    $input = $app->climate->input('Enter homepage [http://github.com/'.$package['vendor'].'/'.$package['package'].']:');
    $input->defaultTo('http://github.com/'.$package['vendor'].'/'.$package['package']);
    $package['homepage'] = $input->prompt();
    
    //authors - name
    $input = $app->climate->input('Enter your name:');
    $input->defaultTo('');
    $package['authors'][0]['name'] = $input->prompt();
    
    //authors - email
    $input = $app->climate->input('Enter your email:');
    $input->defaultTo('');
    $package['authors'][0]['email'] = $input->prompt();
    
    die;
}

// do init
if ($arguments['init']) {
    echo '$wizard';    
}

if (empty($arguments)) {
    $app->climate->description('A tool to help generate a composer package structure.');
    $app->climate->usage();
}



die;


$vendor  = 'PHPPackage';
$package = 'MyPackage';

$package = [
    'name' => strtolower($vendor).'/'.strtolower($package),
    'title' => 'My Package',
    'description' => 'This is my package, description.',
    'type' => 'library',
    'keywords' => [
        'example', 'project', 'boilerplate', 'package'
    ],
    'homepage' => 'http://github.com/'.strtolower($vendor).'/'.strtolower($package),
    'authors' => [
        [
            'name' => 'Your Name',
            'email' => 'your-email@example.com',
            'homepage' => 'http://github.com/'.strtolower($vendor),
            'role' => 'Owner'
        ]
    ],
    'autoload' => [
        'psr-4' => [
            $vendor.'\\'.$package.'\\' => 'src',
        ]
    ],
    'autoload-dev' => [
        'psr-4' => [
            $vendor.'\\'.$package.'\\Tests\\' => 'tests',
        ]
    ]
];

$languages = [
    'php',
    'javascript',
    'python',
    'ruby',
    'java',
];

$progress = $app->climate->progress()->total(count($languages));

foreach ($languages as $key => $language) {
  $progress->current($key + 1, $language);

  // Simulate something happening
  usleep(180000);
}

//$yaml = Yaml::dump($package);

//file_put_contents($basepath.'/package.yaml', $yaml);


//print_r($basepath);

//$climate->clear()
//        ->green('Works!');