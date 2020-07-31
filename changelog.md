# Changelog
All notable changes to this project will be documented in this file.

## [1.5.1] - 2020-07-31

### Fixed
 - Resolved the Geo Block issue

## [1.5.0] - 2020-07-22

### Fixed
 - Resolved the player rendering issues in responsive view

## [1.4.9] - 2020-07-13

### Added
- Added New option Aspact Ratio for all the slider so we can load the images based on the screen resolution using that aspect ratio.

### Changed
- Display the category display name insted of title.
- Update the player on the channel page 
- Update the lock icon styles

## [1.4.8] - 2020-07-08
- Style changes post-player update

### Changed

## [1.4.7] - 2020-07-07

### Changed
- Change old player with new player also added some options for styling the player

## [1.4.6] - 2020-06-10

### Fixed
- Resolved one issue for the seo metadata content on the videos page and also add seo metadata information on channel page

## [1.4.5] - 2020-06-10

### Changed
- Remove a channel from my list directly from the channel or video page instead of going into my list page to remove the channel

## [1.4.4] - 2020-02-04

### Removed
- Removed sharing functionality form video player

## [1.4.3] - 2020-01-07

### Fixed
- Resolved not in platform error in video page

## [1.4.2] - 2019-12-17

### Fixed
- Resolved minor issue related to the theme options

## [1.4.1] - 2019-11-29

### Fixed
- Resolved one issue for the open graph metadata content on the videos page

## [1.4.0] - 2019-11-22

### Fixed
- Resolved one issue for the child theme if we overwrite any function in it

## [1.3.9] - 2019-11-09

### Fixed
- Resolved parent/child channel Add To Mylist issue
- Spelling correction

## [1.3.8] - 2019-11-08

### Added
- Added some new hover effects for the video thumbs for the video/channel rails

## [1.3.7] - 2019-10-29

### Fixed
- Fixed an issue with jQuery for newer wordpress version

## [1.3.6] - 2019-8-8

### Fixed
- Fixed an issue where an incorrect count creating issues with updating while ingesting webhook data

## [1.3.5] - 2019-7-26

### Added
- Added H1 toggle to theme options

### Fixed
- Updated cache keys to be dynamic for certain features; static keys were causing info to not load properly
- Set up dynamic page titles for some templates that were missing them

## [1.3.4] - 2019-7-10

### Added
- Added a theme option for how many slides to load per rail for all rails on the homepage besides the main rail; the main rail is currently hardcoded to 100 max, which should work for plausible use cases.
- Added a theme option to show or hide the category detail page banner

### Fixed
- The images in the main carousel load all at once, so we don't run into janky UI issues with lazy loading.
- Set default logo height to 75 pixels
- JS file cachebusters were not being properly honored due to registering the script for no reason; they do now.

## [1.3.3] - 2019-7-9

### Fixed
- Switched lazy loading methods for images due to the current method reloading images unnecessarily.

### Added
- Set up a config option for whether or not to show or hide the category banner

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


