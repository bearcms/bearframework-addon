# BearCMS\Data\Pages
Information about the site pages

## Methods

```php
public array|null getPage ( string $id )
```

Retrieves information about the page specified

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$id`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The page ID

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The page data or null if page not found

```php
public array getList ( void )
```

Retrieves a list of all pages

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;List containing all pages data

```php
public array getStructure ( void )
```

Retrieves an array containing the pages structure

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;An array containing the pages structure

## Examples

List of all pages

```php
$list = $app->bearCMS->data->pages->getList();

// Array
// (
//     [0] => Array
//         (
//             [id] => abcdefghijk1
//             [name] => Products
//             [slug] => products
//             [parentID] => 
//             [path] => /products/
//             [status] => published
//             [titleTagContent] => 
//             [descriptionTagContent] => 
//             [keywordsTagContent] => 
//         )
//     [1] => Array
//         (
//             [id] => abcdefghijk2
//             [name] => Laptops
//             [slug] => laptops
//             [parentID] => abcdefghijk1
//             [path] => /products/laptops/
//             [status] => published
//             [titleTagContent] => 
//             [descriptionTagContent] => 
//             [keywordsTagContent] => 
//         )
//     ...
// )
```

The structure of the pages in tree view. Only pages ids and children are returned.

```php
$structure = $app->bearCMS->data->pages->getStructure();

// Array
// (
//     [0] => Array
//         (
//             [id] => abcdefghijk1
//             [children] => Array
//                 (
//                     [0] => Array
//                         (
//                             [id] => abcdefghijk2
//                         )
//                 )
//         )
//     ...
// )

```
