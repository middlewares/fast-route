# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## [0.8.0] - 2017-09-21

### Changed

* Append `.dist` suffix to phpcs.xml and phpunit.xml files
* Changed the configuration of phpcs and php_cs
* Upgraded phpunit to the latest version and improved its config file
* Updated to `http-interop/http-middleware#0.5`

## [0.7.0] - 2017-04-20

### Changed

* Handlers are no longer executed, only passed as attribute references.

## [0.6.0] - 2017-04-13

### Changed

* The option `resolver()` accepts any instance of `Middlewares\Utils\CallableResolver\CallableResolverInterface`.

### Added

* New option `container()` that works as a shortcut to use a PSR-11 container as a resolver.

### Fixed

* The `405` response includes an `Allow` header with the allowed methods for the request.

## Fixed

## [0.5.0] - 2017-02-27

## Changed

* Replaced `container-interop` by `psr/container`

## [0.4.0] - 2017-02-05

## Changed

* Updated to `middlewares/utils#~0.9`
* Improved route target resolution

## [0.3.0] - 2016-12-26

### Changed

* Updated tests
* Updated to `http-interop/http-middleware#0.4`
* Updated `friendsofphp/php-cs-fixer#2.0`

## [0.2.0] - 2016-11-27

### Changed

* Updated to `http-interop/http-middleware#0.3`

## 0.1.0 - 2016-10-09

First version

[0.8.0]: https://github.com/middlewares/fast-route/compare/v0.7.0...v0.8.0
[0.7.0]: https://github.com/middlewares/fast-route/compare/v0.6.0...v0.7.0
[0.6.0]: https://github.com/middlewares/fast-route/compare/v0.5.0...v0.6.0
[0.5.0]: https://github.com/middlewares/fast-route/compare/v0.4.0...v0.5.0
[0.4.0]: https://github.com/middlewares/fast-route/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/middlewares/fast-route/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/middlewares/fast-route/compare/v0.1.0...v0.2.0
