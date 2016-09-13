# BearCMS\Data\Addons
Information about the addons managed by Bear CMS

## Methods

```php
public array|null getAddon ( string $id )
```

Retrieves information about the addon specified

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$id`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The addon ID

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The addon data or null if addon not found

```php
public array getList ( void )
```

Retrieves a list of all addons

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;List containing all addons data

## Examples

List of all Bear CMS managed addons.

```php
$list = $app->bearCMS->data->addons->getList();

// Array
// (
//     [0] => Array
//         (
//             [id] => vendor1/example-addon-1
//             [enabled] => 1
//         )
//     [1] => Array
//         (
//             [id] => vendor2/example-addon-2
//             [enabled] => 0
//         )
//     ...
// )
```

Information about a specific Bear CMS managed addon.

```php
$addon = $app->bearCMS->data->addons->getAddon('test/test1-addon');

// Array
// (
//     [id] => vendor1/example-addon-1
//     [enabled] => 1
// )

```
