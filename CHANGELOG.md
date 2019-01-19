# Element Status Events Changelog

## 2.0.0 - UNRELEASED
### Changed
- No need to bootstrap or register the Module
- Extension implements `BootstrapInterface`
- Event `ElementStatusChange::EVENT_STATUS_CHANGED` 

### Added
- Event object `StatusChangeEvent` with access to element via `getElement()` and check methods:
    - `changedTo(string $nameOfStatus)`
    - `changedToPublished()`
    - `changedToUnpublished()`

- craft cli command `element-status-change/scheduled` to take scheduled elements into account    

## 1.2.0 - 2019-01-17
### Changed
- Removed the `ElementStatusService::EVENT_ELEMENT_STATUSES_CHANGED` event as it cannot be guaranteed that it will be triggered under every circumstance. 

## 1.1.0 - 2019-01-15
### Changed
- Changed the module to use a service so that it can be loaded from any other module or plugin without having to be bootstrapped.

## 1.0.0 - 2019-01-14
- Initial release.
