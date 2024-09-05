# FormField

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

This is where your description should go. Take a look at [contributing.md](contributing.md) to see a to do list.

## Installation

Via Composer

``` bash
$ composer require ilbronza/formfield
```

## Usage

### Select or multiple fields

#### Set the possible values for a relation

Given a relation (ex. Categories) you can set the possible values for a select or multiple field with the following code
to populate the select or multiple field options

``` bash

public function getPossibleClientsValuesArray() : array
{
    return Category::select('name', 'id')->take(10)->pluck('name', 'id')->toArray();
}

```

[ico-version]: https://img.shields.io/packagist/v/ilbronza/formfield.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/ilbronza/formfield.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/ilbronza/formfield/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/ilbronza/formfield
[link-downloads]: https://packagist.org/packages/ilbronza/formfield
[link-travis]: https://travis-ci.org/ilbronza/formfield
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/ilbronza
[link-contributors]: ../../contributors
