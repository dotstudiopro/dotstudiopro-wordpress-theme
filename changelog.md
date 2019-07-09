# Changelog
All notable changes to this project will be documented in this file.

## [1.3.3] - 2019-7-9

### Fixed
- Switched lazy loading methods for images due to the current method reloading images unnecessarily.

## [1.3.2] - 2019-6-10

### Fixed
- Discovered discrepancy between minified version of search autocomplete JS file and regular version; reminified our JS to fix.

## [1.3.1] - 2019-5-22

### Fixed
- Fixed a bug where the subscription class was being called when it didn't exist after a user logs in. The script now checks for the class before attempting to use it.

## [1.3.0] - 2019-3-1

### Fixed
- Fixed some issues with caching related to user's location, as the country code needs to be included for caching so we cache results by country instead of homogenously

## [1.2.2] - 2019-2-22

### Added
- Added option to add external image URL in theme options.

## [1.2.1] - 2019-2-21

### Added
- Set CSS/JS sources to either the proper CDN (in the case of assets like Bootstrap) or our Wordpress CDN for theme assets

## [1.2.0] - 2019-2-21

### Added
- Set up CSS/JS footer load optimizations
- Minified all of the assets that needed it

### Changed
- Updated assets to pull minified versions

## [1.1.2] - 2019-2-17

### Changed
- Updated missing image dimension logic for search results

## [1.1.1] - 2019-2-05

### Changed
- Updated missing units in `Dimensions (Height) Option for the Home page logo after resize` setting

## [1.1.0] - 2019-1-31

### Changed
- Updated language for theme options to fix spelling and grammar errors
- Updated the theme description to not be generic
- Updated various options that were missing appropriate values for selects
- Fixed the channel locking logic to account for parent channels
- Set up video template to display a list only when the WP Auth0 plugin is activated

### Removed
- Removed some duplicate code in our Redux config


