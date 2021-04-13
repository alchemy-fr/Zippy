# CHANGELOG

## [Unreleased changes]
- No changes

## [0.4.10] - TBD
### Changed
- Allow Symfony 5.0 components
- Drop support for PHP5 (it is long gone)

## [0.4.9] - 2018-02-27
### Changed
- Allow Symfony 4.0 in composer.json

## [0.4.8] - 2017-03-03
### Fixed
- #125: Fix invalid paths with relative locations, courtesy of @paulredmond

## [0.4.7] - 2017-02-23
### Fixed
- #125: Type errors when date time cannot be parsed, courtesy of @joachimdoerr

## [0.4.6] - 2017-01-30
### Fixed
- #123: invalid `@info` docblocks were causing issues with Syfony's annotation parser, courtesy of @jducro

## [0.4.5] - 2016-12-19
### Changed
- Require Symfony's mbstring polyfill instead of the PHP extension

### Fixed
- #111, #115: Parsing Zip archive dates failed on CentOS
- Docblocks for Archive class, courtesyy of @Koc 

## [0.4.4] - 2016-11-03
### Added
- #116: Add an option to override archive type detection in `Zippy::open`, similar to `Zippy::create`, courtesy of @GiantCowFilms

## [0.4.3] - 2016-11-03
### Fixed
- #114: Recent versions of TAR trigger errors when using incompatible options (instead of silently ignoring them, see https://lists.gnu.org/archive/html/bug-tar/2016-05/msg00016.html for more information)

## [0.4.2] - 2016-08-05
### Fixed
- #113: Fix issue with FilesystemWriter and missing target directories, courtesy of @mikemeier

## [0.4.1] - 2016-08-05
### Changed
- Use generic teleporter instead of Guzzle specific teleporter
- Deprecate static method `create` on teleporters
- Deprecate class `\Alchemy\Zippy\Resource\Teleporter\GuzzleTeleporter`
- Deprecate class `\Alchemy\Zippy\Resource\Teleporter\LegacyGuzzleTeleporter`
- Deprecate class `\Alchemy\Zippy\Resource\Teleporter\AbstractTeleporter`

### Removed 
- Remove usage of deprecated test method `getMock`

## [0.4.0] - 2016-07-19
### Changed
- #106: Improve PHPDoc comments (thanks @GoktugOzturk)
- #107: Alias `Resource` as `ZippyResource` in order to prepare for PHP7 compatibility (thanks @GoktugOzturk)
- #109: Adds `Resource::getResource` method to extract original resource
- Rewrite teleporters to allow usage of the new Guzzle library (v4 and up)

### Fixed
- #110: Fixes issue with non string resources (thanks @mikemeyer)
- Improper checking of files to delete in tests

## [0.3.5] - 2016-02-15
### Fixed
- Issue #100: Some characters are dropped from UTF-8 filenames

## [0.3.4] - 2016-02-02
### Fixed
- Issue #98: Adds the "mbstring" extension as a platform requirement in composer.json

## [0.3.3] - 2016-01-27
### Fixed
- Issue #96: Enables overwrite of existing destination files when extracting single archive members

## [0.3.2] - 2016-01-12
### Fixed
- Issue #93: It was not possible to add files in subfolders on Windows platforms

## [0.3.1] - 2015-12-15
### Fixed
- Issue #86: Allow setting a custom date format on output parser

## [0.3.0] - 2015-12-15
### Changed
- Uses PSR-4 autoloading - thanks to @afurculita
- Makes dependency to Guzzle optional (only required if you need to fetch remote archives) - thanks to @afurculita
- Improves testing process (allows easier local testing)

### Removed
- Removes dependency to Pimple

## [0.2.1] - 2014-12-10
### Added
- Add .gitattribute to limit package size

### Fixed
- Fix strategy implementation

## [0.2.0] - 2014-04-04
### Fixed
- Fix the use of "teleporter" for local files
- Fix adding a new file using tar adapter ( --append option )

### Added
- Add support for archives relative path
- Archive in context when a single resource is added

### Changed
- Allow all adapters to be instantiated even if they are not supported
- Move support detection logic in distinct classes
- Use Symfony Process working directory instead of changing working directory

## [0.1.1] - 2013-12-04
### Added
- Throw exception in case chdir failed
- Use guzzle stream download to handle large files without large memory usage

## [0.1.0] - 2013-03-11
### Added
- First stable version.
- Support for GNUtar, BSDtar, Zip, PHPZip.
