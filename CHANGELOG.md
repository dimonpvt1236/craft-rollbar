## 1.0.3 - 2021-06-07

## Changed

* Send full exception stack trace to rollbar


## 1.0.2 - 2021-05-24

## Changed

* Updated client-side JS library code and default version
* Updated Rollbar PHP library version
* Changed support email address

## 1.0.1 - 2020-10-01

## Fixed

* Added `parent::init()` call to SettingsController to resolve issues with request as string on Rollbar test.

## 1.0.0 - 2018-09-10

## Fixed

* Type-hint for `Throwable` not `Exception` when checking whether an error or exception should be reported

## 0.6.1 - 2018-06-26

### Added

* Added privacy/GDPR-related plugin settings.
* Rollbar.js version can be set in the plugin settings.

### Improved

* Updated `rollbar-php` and `rollbar.js`.

## 0.5.0 - 2018-06-26

### Added

* Rules can be added to filter out unwanted error messages from being reported.

## 0.4.0 - 2018-05-16

### Changed

* Various updates to support Craft 3.0.0

### Fixed

* Fixed a deprecation error when splitting the `ignoreHTTPCodes` setting to an array.

### Removed

* Removed the CSP features that were not Rollbar-specific.

## 0.3.0 - 2017-07-13

### Added

* Include details of the current user when posting errors to Rollbar.
* Allow certain error codes to be ignored.
* Allow custom environment names to be set in the plugin config.
* Allow reporting to be switched on and off entirely per environment.
* Adds JS tracking.
* Adds a CSP endpoint.

## 0.2.0 - 2017-07-12

### Added

* Errors and Exceptions are now posted to Rollbar when they occur.

### Improved

* The `accessToken` and `clientAccessToken` settings can now be set from the CP as well as the plugin's config file.

## 0.1.0 - 2017-07-12

* Initial plugin with a `Settings` model.
