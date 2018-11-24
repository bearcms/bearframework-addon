# BearCMS\Data\Settings
Information about the site settings

## Methods

```php
public \BearCMS\DataObject get ( void )
```

Returns an object containing the site settings

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;An object containing the site settings

## Examples

List all site settings.

```php
$settings = Internal2::$data2->settings->get();

// \BearCMS\DataObject
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
