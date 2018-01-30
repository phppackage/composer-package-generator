<?php
/**
 * Define the package settings
 */
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

### Dont change anything below this point ###

/**
 * Define directory constants
 */
define('SOURCE_DIR', __DIR__.'/setup');
define('TARGET_DIR', __DIR__);

/**
 * Create src directory
 */
if (!file_exists(TARGET_DIR.'/src')) {
    mkdir(TARGET_DIR.'/src', 0755, true);
}

/**
 * Create [tests|fixtures] directory
 */
if (!file_exists(TARGET_DIR.'/tests/fixtures')) {
    mkdir(TARGET_DIR.'/tests/fixtures', 0755, true);
}

/**
 * Replace placeholders in files
 */
function process_file($filename, $replace) {
    file_put_contents(
        TARGET_DIR.'/'.$filename,
        preg_replace_callback("/{{([\w_]{1,})}}/", function ($match) use ($replace) {
            return array_key_exists($match[1], $replace) ? $replace[$match[1]] : '';
        }, file_get_contents(SOURCE_DIR.'/'.$filename))
    );
}

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
    if (file_exists(SOURCE_DIR.'/'.$file)) {
        copy(SOURCE_DIR.'/'.$file, TARGET_DIR.'/'.$file);
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
process_file('README.md', [
    'name' => $package['name'],
    'title' => $package['title'],
    'description' => $package['description'],
    'authors' => implode(PHP_EOL, $authors)
]);

// composer.json
file_put_contents(
    TARGET_DIR.'/composer.json',
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
$testName = str_replace([' ', $vendor], null, ucwords(str_replace('\\', ' ', $namespace)));

$authors = [];
foreach ($package['authors'] as $author) {
    $authors[] = ' |   '.sprintf('%s <%s>', $author['name'], $author['email']);
}
// create class
file_put_contents(TARGET_DIR.'/src/'.$testName.'.php', '<?php
/*
 +-----------------------------------------------------------------------------+
 | '.$vendor.' - '.$package['title'].'
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
    public function exampleMetod()
    {
        return \'foobar\';
    }

}'.PHP_EOL);

// create unit test
file_put_contents(TARGET_DIR.'/tests/'.$testName.'Test.php', '<?php

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
    public function testExampleMetod()
    {
        $this->assertEquals(\'foobar\', $this->instance->exampleMetod());             
    }

}'.PHP_EOL);


function ask($options, $callback) {
    $response = null;
    do {
        $response = readline($options['question']);
    } while (!in_array($response, $options['expected']));
    readline_add_history($response);
    
    return $callback($response);
}

echo 'Your package files have been generated successfully!'.PHP_EOL;

ask([
    'question' => 'Would you like to remove the setup files?',
    'expected' => ['y', 'yes', 'n', 'no']
], function ($response) {
    if (in_array($response, ['y', 'yes'])) {
        `rm -R ./setup  && rm -f setup.php && rm -R .git/`;
        echo 'Setup files have been removed.'.PHP_EOL;
    }
});

echo 'Happy coding!'.PHP_EOL;
