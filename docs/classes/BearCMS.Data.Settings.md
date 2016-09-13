# BearCMS\Data\Settings
Information about the site settings

## Methods

```php
public array get ( void )
```

Returns an array containing the site settings

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;An array containing the site settings

## Examples

List all site settings.

```php
$settings = $app->bearCMS->data->settings->get();

// Array
// (
//     [title] => Company Ltd.
//     [description] => welcome to our website
//     [language] => en
//     [allowSearchEngines] => 1
//     [externalLinks] => 1
//     [keywords] => 
//     [icon] => 
// )
```
