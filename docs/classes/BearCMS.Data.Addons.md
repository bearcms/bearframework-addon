# BearCMS\Data\Addons
Information about the addons managed by BearCMS

## Methods

```php
public \BearCMS\DataObject|null getAddon ( string $id )
```

Retrieves information about the addon specified

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$id`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The addon ID

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The addon data or null if addon not found

```php
public \BearCMS\DataList getList ( void )
```

Retrieves a list of all addons

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;List containing all addons data

## Examples

List of all BearCMS managed addons.

```php
$list = $app->bearCMS->data->addons->getList();

// \BearCMS\DataList
// (
//     [0] => \BearCMS\DataObject
//         (
//             [id] => vendor1/example-addon-1
//             [enabled] => 1
//         )
//     [1] => \BearCMS\DataObject
//         (
//             [id] => vendor2/example-addon-2
//             [enabled] => 0
//         )
//     ...
// )
```

Information about a specific BearCMS managed addon.

```php
$addon = $app->bearCMS->data->addons->getAddon('test/test1-addon');

// \BearCMS\DataObject
// (
//     [id] => vendor1/example-addon-1
//     [enabled] => 1
// )

```
