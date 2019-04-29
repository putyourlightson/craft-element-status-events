# Element Status Events for Craft CMS 3

The Element Status Events extension provides events that are triggered whenever an element’s status changes. It is intended to be used a helper component for other Craft modules and plugins.

To get an understanding of how the module works, read the [Challenge #6 – The Chicken or the Egg](https://craftcodingchallenge.com/challenge-6-the-chicken-or-the-egg) solution.

## Requirements

This extension requires Craft CMS 3.0.0 or later.

## Usage

Install it manually using composer or add it as a dependency to your plugin.
```
composer require putyourlightson/craft-element-status-events
```    
    
If you work with scheduled Entries (future published or expired), make sure to set up cron that calls:
```
php craft element-status-events/scheduled
```    


## Events

Whenever an element’s status is changed, `ElementStatusEvents::EVENT_STATUS_CHANGED` is fired. The `StatusChangeEvent` object provides information about the change.

```php

use putyourlightson\elementstatusevents\ElementStatusEvents;
use putyourlightson\elementstatusevents\events\StatusChangeEvent;

// ...

Event::on(
    ElementStatusEvents::class, 
    ElementStatusEvents::EVENT_STATUS_CHANGED, 
    function(StatusChangeEvent $event) {
        $oldStatus   = $event->statusBeforeSave;
        $newStatus   = $event->element->getStatus();
        $isLive      = $event->changedToPublished();
        $isDeath     = $event->changedToUnpublished();
        $isScheduled = $event->changedTo('pending');
    }
);
```


## License

This extension is licensed for free under the MIT License.

<small>Created by [PutYourLightsOn](https://putyourlightson.com/) in cooperation with [Oliver Stark](https://github.com/ostark)</small>
