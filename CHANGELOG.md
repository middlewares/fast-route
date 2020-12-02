# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [2.0.1] - 2020-12-02
### Added
- Support for PHP 8

## [2.0.0] - 2019-11-29
### Removed
- Support for PHP 7.0 and 7.1
- The `responseFactory()` option. Use the `__construct` argument.

## [1.2.1] - 2019-04-01
### Fixed
- Added support for encoded path [#11]

## [1.2.0] - 2018-10-22
### Added
- Added `responseFactory` option to `__construct`

### Deprecated
- `responseFactory()` option as a method. Use the contructor argument instead.

### Fixed
- Use `phpstan` as a dev dependency to detect bugs

## [1.1.0] - 2018-08-04
### Added
- PSR-17 support
- New option `responseFactory`

## [1.0.0] - 2018-01-27
### Added
- Improved testing and added code coverage reporting
- Added tests for PHP 7.2

### Changed
- Upgraded to the final version of PSR-15 `psr/http-server-middleware`

### Fixed
- Updated license year

## [0.9.0] - 2017-11-13
### Changed
- Replaced `http-interop/http-middleware` with  `http-interop/http-server-middleware`.

### Removed
- Removed support for PHP 5.x.

## [0.8.0] - 2017-09-21
### Changed
- Append `.dist` suffix to phpcs.xml and phpunit.xml files
- Changed the configuration of phpcs and php_cs
- Upgraded phpunit to the latest version and improved its config file
- Updated to `http-interop/http-middleware#0.5`

## [0.7.0] - 2017-04-20
### Changed
- Handlers are no longer executed, only passed as attribute references.

## [0.6.0] - 2017-04-13
### Added
- New option `container()` that works as a shortcut to use a PSR-11 container as a resolver.

### Changed
- The option `resolver()` accepts any instance of `Middlewares\Utils\CallableResolver\CallableResolverInterface`.

### Fixed
- The `405` response includes an `Allow` header with the allowed methods for the request.

## [0.5.0] - 2017-02-27
### Changed
- Replaced `container-interop` by `psr/container`

## [0.4.0] - 2017-02-05
### Changed
- Updated to `middlewares/utils#~0.9`
- Improved route target resolution

## [0.3.0] - 2016-12-26
### Changed
- Updated tests
- Updated to `http-interop/http-middleware#0.4`
- Updated `friendsofphp/php-cs-fixer#2.0`

## [0.2.0] - 2016-11-27
### Changed
- Updated to `http-interop/http-middleware#0.3`

## 0.1.0 - 2016-10-09
First version

[#11]: https://github.com/middlewares/fast-route/issues/11

[2.0.1]: https://github.com/middlewares/fast-route/compare/v2.0.0...v2.0.1
[2.0.0]: https://github.com/middlewares/fast-route/compare/v1.2.1...v2.0.0
[1.2.1]: https://github.com/middlewares/fast-route/compare/v1.2.0...v1.2.1
[1.2.0]: https://github.com/middlewares/fast-route/compare/v1.1.0...v1.2.0
[1.1.0]: https://github.com/middlewares/fast-route/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/middlewares/fast-route/compare/v0.9.0...v1.0.0
[0.9.0]: https://github.com/middlewares/fast-route/compare/v0.8.0...v0.9.0
[0.8.0]: https://github.com/middlewares/fast-route/compare/v0.7.0...v0.8.0
[0.7.0]: https://github.com/middlewares/fast-route/compare/v0.6.0...v0.7.0
[0.6.0]: https://github.com/middlewares/fast-route/compare/v0.5.0...v0.6.0
[0.5.0]: https://github.com/middlewares/fast-route/compare/v0.4.0...v0.5.0
[0.4.0]: https://github.com/middlewares/fast-route/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/middlewares/fast-route/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/middlewares/fast-route/compare/v0.1.0...v0.2.0
