# Element Status Events Module for Craft CMS 3

The Element Status Events module provides events that are triggered whenever an element’s status changes. It is intended to be used a helper module for other Craft modules and plugins.

Note that the events are triggered only when elements are saved. An entry that is enabled with a future post date will not automatically trigger the event when the post date is reached. For that functionality, take a look at the [Published Event](https://github.com/sjelfull/craft3-publishedevent) plugin.

To get an understanding of how the module works, read the [Challenge #6 – The Chicken or the Egg](https://craftcodingchallenge.com/challenge-6-the-chicken-or-the-egg) solution.

## License

This module is licensed for free under the MIT License.

## Requirements

This module requires Craft CMS 3.0.0 or later.

## Usage

Install the module manually using composer.

    composer require putyourlightson/craft-element-status-events


## Events

The module provides the following event.

### `ElementStatusEvents::EVENT_STATUS_CHANGED`

Triggered whenever an element’s status is changed. The `StatusChangeEvent` provides information about the change.

```php

use putyourlightson\elementstatusevents\ElementStatusChange;
use putyourlightson\elementstatusevents\events\StatusChangeEvent;

// ...

Event::on(
    ElementStatusChange::class, 
    ElementStatusChange::EVENT_STATUS_CHANGED, 
    function(StatusChangeEvent $event) {
        $oldStatus   = $event->statusBeforeSave;
        $newStatus   = $event->element->getStatus();
        $isLive      = $event->changedToPublished();
        $isDeath     = $event->changedToUnpublished();
        $isScheduled = $event->changedTo('pending');
    }
);
```



<small>Created by [PutYourLightsOn](https://putyourlightson.com/).</small>
