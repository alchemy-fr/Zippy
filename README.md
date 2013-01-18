# Zippy

A PHP library to manipulate any archive format (de)compression through
commandline utilities or PHP extension.

[![Build Status](https://secure.travis-ci.org/alchemy-fr/Zippy.png?branch=master)](http://travis-ci.org/alchemy-fr/Zippy)

##Documentation

##API Browser

##Usage Example

```php
use Alchemy\Zippy\Zippy;

$zippy = Zippy::load();
$zippy->create('archive.zip');

$archive = $zippy->open('build.tar');

foreach ($archive as $member) {
    echo "archive contains $member \n";
}

```

##License

This project is licensed under the [MIT license](http://opensource.org/licenses/MIT).




