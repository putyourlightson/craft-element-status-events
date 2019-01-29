# Element Status Events Changelog

## 2.0.0 - UNRELEASED
### Added
- Added event object `StatusChangeEvent` with access to element via `getElement()` and check methods:
    - `changedTo(string $nameOfStatus)`
    - `changedToPublished()`
    - `changedToUnpublished()`

- Added Craft CLI command `element-status-change/scheduled` to take scheduled elements into account.

### Changed
- No need to bootstrap or register the extension.
- Extension implements `BootstrapInterface`.
- Event `ElementStatusChange::EVENT_STATUS_CHANGED`.

## 1.3.0 - 2019-01-18
### Changed
- Converted from module to extension.

## 1.2.1 - 2019-01-18
### Fixed
- Fixed missing variable.

## 1.2.0 - 2019-01-17
### Changed
- Removed the `ElementStatusService::EVENT_ELEMENT_STATUSES_CHANGED` event as it cannot be guaranteed that it will be triggered under every circumstance. 

## 1.1.0 - 2019-01-15
### Changed
- Changed the module to use a service so that it can be loaded from any other module or plugin without having to be bootstrapped.

## 1.0.0 - 2019-01-14
- Initial release.
