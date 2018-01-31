## Composer Package Generator

Tweeked for organisation (PHPPackage), if you want a more generic version [use this](https://github.com/lcherone/composer-package-generator). 

This very simple PHP script will generate the following structure, ready to start 
creating your composer package.

     ┐
     ├── src
     │   ├── MyPackage.php (generated based upon your package name)
     ├── tests
     │   ├── fixtures
     │   ├── PHPPackageMyPackageTest.php (generated based upon your namespace)
     │   └── bootstrap.php
     ├── .gitignore
     ├── .scrutinizer.yml
     ├── .styleci.yml
     ├── .travis.yml
     ├── CONTRIBUTING.md
     ├── LICENSE
     ├── phpunit.xml
     ├── README.md
     └── composer.json
     

## Install

Git clone this project:

``` bash
$ git clone git@github.com:phppackage/package-generator.git .
```


## Generate your package files

Open up and edit `setup.php`, enter the details of your package in the following array:


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

Once you finished save the file and run the following to generate your project files:

``` bash
$ php setup.php
```

At the end it will ask you if you want to remove the `setup` files and execute composer install and run unit tests.

**Badges:**

Markdown links and images will have been added to the `README.md` and should work 
once you push your project and enable the project on these 3rd party sites.

 - [`Build Status (https://travis-ci.org)`](https://travis-ci.org)
 - [`StyleCI (https://styleci.io)`](https://styleci.io)
 - [`Scrutinizer (https://scrutinizer-ci.com)`](https://scrutinizer-ci.com)
 - [`Packagist (https://packagist.org)`](https://packagist.org/)
<!-- end list -->

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Credits

 - [Lawrence Cherone](http://github.com/lcherone)
 - [All Contributors](../../contributors)


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
