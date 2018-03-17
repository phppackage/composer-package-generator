## Composer Package Generator (.phar tool)

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

Git clone this project or download a prebuilt verion:

``` bash
$ git clone git@github.com:phppackage/package-generator.git . && composer install
```

## Build

To build the `package-generator.phar` run:

`bash /usr/bin/php -c /etc/php/7.0/cli/php.ini -f box.phar build -v`

## Run

`/usr/bin/php package-generator.phar -w`


**Badges:**

Markdown links and images will have been added to the `README.md` and should work 
once you push your project and enable the project on these 3rd party sites.

 - [`Build Status (https://travis-ci.org)`](https://travis-ci.org)
 - [`StyleCI (https://styleci.io)`](https://styleci.io)
 - [`Scrutinizer (https://scrutinizer-ci.com)`](https://scrutinizer-ci.com)
 - [`Packagist (https://packagist.org)`](https://packagist.org/)
<!-- end list -->

Boxing with box2
----------------

To box up you must have box installed:

`curl -LSs https://box-project.github.io/box2/installer.php | php`

Then you need to enable `phar.readonly = 0` in cli's php.ini:

Sometimes no `php.ini` file is loaded when run in cli so its simpler to just define the `php.ini` when running build...

**So build like so:**

`/usr/bin/php -c /etc/php/7.0/cli/php.ini -f box.phar build -v`


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Credits

 - [Lawrence Cherone](http://github.com/lcherone)
 - [All Contributors](../../contributors)


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
