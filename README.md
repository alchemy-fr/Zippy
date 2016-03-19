# Zippy

A Object-Oriented PHP library to manipulate any archive format (de)compression
through commandline utilities or PHP extension.

[![GitHub license](https://img.shields.io/github/license/alchemy-fr/Zippy.svg?style=flat-square)](https://github.com/alchemy-fr/Zippy/LICENSE)
[![Packagist](https://img.shields.io/packagist/v/alchemy/zippy.svg?style=flat-square)](https://packagist.org/packages/alchemy/zippy)
[![Travis](https://img.shields.io/travis/alchemy-fr/Zippy.svg?style=flat-square)](https://travis-ci.org/alchemy-fr/Zippy)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/alchemy-fr/Zippy.svg?style=flat-square)](https://scrutinizer-ci.com/g/alchemy-fr/Zippy/)
[![Packagist](https://img.shields.io/packagist/dt/alchemy/zippy.svg?style=flat-square)](https://packagist.org/packages/alchemy/zippy/stats)

## Adapters

Zippy currently supports

 - zip
 - PHP zip
 - GNU tar
 - BSD tar

Which brings support to file types

 - .tar
 - .zip
 - .tar.gz
 - .tar.bz2

## API Example

### Archive listing and extraction :

```php
use Alchemy\Zippy\Zippy;

$zippy = Zippy::load();
$zippy->create('archive.zip', '/path/to/folder');

$archive = $zippy->open('build.tar');

// extract content to `/tmp`
$archive->extract('/tmp');

// iterates through members
foreach ($archive as $member) {
    echo "archive contains $member \n";
}
```

### Archive creation

```php
use Alchemy\Zippy\Zippy;

$zippy = Zippy::load();
// creates an archive.zip that contains a directory "folder" that contains
// files contained in "/path/to/directory" recursively
$archive = $zippy->create('archive.zip', array(
    'folder' => '/path/to/directory'
), true);
```

### Customize file and directory names inside archive

```php
use Alchemy\Zippy\Zippy;

$zippy = Zippy::load();
$archive = $zippy->create('archive.zip', array(
    'folder' => '/path/to/directory',            // will create a folder at root
    'http://www.google.com/logo.jpg',            // will create a logo.jpg file at root
    fopen('https://www.facebook.com/index.php'), // will create an index.php at root
    'directory/image.jpg' => 'image.jpg',        // will create a image.jpg in 'directory' folder
));
```

##API Browser

## Documentation

Documentation hosted at [read the docs](https://zippy.readthedocs.org/) !

##License

This project is licensed under the [MIT license](http://opensource.org/licenses/MIT).


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/romainneutron/zippy/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

