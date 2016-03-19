# CHANGELOG

## [Unreleased]
### Removed:
- Support for PHP versions lower than 5.5
### Added
- Support for Guzzle 6 when teleporting remote files
### Fixed
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
