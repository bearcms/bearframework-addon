# BearCMS\Data\Themes
Information about the site themes

## Methods

```php
public array getOptions ( string $id )
```

Returns a list containing the options for the theme specified

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$id`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The id of the theme

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A list containing the theme options

```php
public array getTempOptions ( array $id , array $userID )
```

Returns a list containing the theme options a specific user has made

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$id`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The id of the theme

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$userID`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The id of the user

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A list containing the theme options

## Examples

List last saved theme options.

```php
$options = $app->bearCMS->data->themes->getOptions('bearcms/theme1');

// Array
// (
//     [headerCSS] => {"max-width":"800px","margin-left":"auto","margin-right":"auto",...
//     [headerLogoImage] => 
//     [headerLogoImageCSS] => {"width":"300px"}
//     ...
// )
```

List last unsaved theme options for a specific user.

```php
$options = $app->bearCMS->data->themes->getTempOptions('bearcms/theme1', 'abcdefghijk1');

// Array
// (
//     [headerCSS] => {"max-width":"800px","margin-left":"auto","margin-right":"auto",...
//     [headerLogoImage] => 
//     [headerLogoImageCSS] => {"width":"300px"}
//     ...
// )
```