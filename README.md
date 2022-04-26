# PHPStan for Silverstripe

A module allowing PHPStan to work with Silverstripe

[![🎭 Tests](https://github.com/syntro-opensource/silverstripe-phpstan/workflows/%F0%9F%8E%AD%20Tests/badge.svg)](https://github.com/syntro-opensource/silverstripe-phpstan/actions?query=workflow%3A%22%F0%9F%8E%AD+Tests%22+branch%3A%22master%22)
[![codecov](https://codecov.io/gh/syntro-opensource/silverstripe-phpstan/branch/master/graph/badge.svg)](https://codecov.io/gh/syntro-opensource/silverstripe-phpstan)
![Dependabot](https://img.shields.io/badge/dependabot-active-brightgreen?logo=dependabot)
[![phpstan](https://img.shields.io/badge/PHPStan-enabled-success)](https://github.com/phpstan/phpstan)
[![composer](https://img.shields.io/packagist/dt/syntro/silverstripe-phpstan?color=success&logo=composer)](https://packagist.org/packages/syntro/silverstripe-phpstan)
[![Packagist Version](https://img.shields.io/packagist/v/syntro/silverstripe-phpstan?label=stable&logo=composer)](https://packagist.org/packages/syntro/silverstripe-phpstan)


**Features:**

- Support for `DataObject::get()`, ie. it understands you have a DataList of iterable SiteTree records.
- Support for DataObject `db`, `has_one`, `has_many` and `many_many` magic properties and methods, ie. it knows SiteTree::Title is a string, that SiteTree::ParentID is an integer and that SiteTree::Parent() is a SiteTree record.
- Support for `singleton('SiteTree')` and `Injector::inst()->get('SiteTree')`, ie. it knows these will return "SiteTree". If you override these with the injector, it'll also know what class you're actually using.
- Support for config properties

This PHPStan module is able to reason about extensions installed specific to your project as it bootstraps the SilverStripe config system. So if you've added an extension to your `Page` object that adds an additional `db` field, PHPStan will be able to reason about it.

## Composer Install

SilverStripe 4.X
```
composer require --dev syntro/silverstripe-phpstan
```


## Requirements

* SilverStripe 4.3+

## Documentation

* [Quick Start](docs/en/quick-start.md)
* [Advanced Usage](docs/en/advanced-usage.md)
* [License](LICENSE.md)

## Known Limitations

* The type of the `owner` property can't be reasoned about for extensions. You must use `getOwner()`. Related Issues: [#1043](https://github.com/phpstan/phpstan/issues/1043) and [#1044](https://github.com/phpstan/phpstan/issues/1044)
