# BearCMS\Data\Templates
Information about the site templates

## Methods

```php
public array getOptions ( string $id )
```

Returns a list containing the options for the template specified

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$id`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The id of the template

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A list containing the template options

```php
public array getTempOptions ( array $id , array $userID )
```

Returns a list containing the template options a specific user has made

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$id`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The id of the template

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$userID`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The id of the user

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A list containing the template options

## Examples

List last saved template options.

```php
$options = $app->bearCMS->data->templates->getOptions('bearcms/default1');

// Array
// (
//     [headerCSS] => {"max-width":"800px","margin-left":"auto","margin-right":"auto",...
//     [headerLogoImage] => 
//     [headerLogoImageCSS] => {"width":"300px"}
//     ...
// )
```

List last unsaved template options for a specific user.

```php
$options = $app->bearCMS->data->templates->getTempOptions('bearcms/default1', 'abcdefghijk1');

// Array
// (
//     [headerCSS] => {"max-width":"800px","margin-left":"auto","margin-right":"auto",...
//     [headerLogoImage] => 
//     [headerLogoImageCSS] => {"width":"300px"}
//     ...
// )
```