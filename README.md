# Element Status Events Extension for Craft CMS 3

The Element Status Events extension provides events that are triggered whenever an element’s status changes. It is intended to be used by Craft modules and plugins.

Note that the events are triggered only when elements are saved. An entry that is enabled with a future post date will not automatically trigger the event when the post date is reached. For that functionality, take a look at the [Published Event](https://github.com/sjelfull/craft3-publishedevent) plugin.

To get an understanding of how the extension works, read the [Challenge #6 – The Chicken or the Egg](https://craftcodingchallenge.com/challenge-6-the-chicken-or-the-egg) solution.

## License

This extension is licensed for free under the MIT License.

## Requirements

This extension requires Craft CMS 3.0.0 or later.

## Usage

To use the extension, simply require it in your module or plugin’s `compose.json` file.
   
    "require": {
        "putyourlightson/craft-element-status-events": "^1.3.0"
    },

## Events

The module provides the following event.

### `ElementStatusEvents::EVENT_STATUS_CHANGED`

Triggered whenever an element’s status is changed. The element will have a `statusBeforeSave` (string) and `statusChanged` (boolean) parameter available to it.

    Event::on(ElementStatusEvents::class, ElementStatusEvents::EVENT_STATUS_CHANGED, function(ElementEvent $event) {
        /** @var Element $element */
        $element = $event->element;
        
        $oldStatus = $element->statusBeforeSave;
        $newStatus = $element->status;
        $statusChanged = $element->statusChanged;
    }); 

<small>Created by [PutYourLightsOn](https://putyourlightson.com/).</small>
