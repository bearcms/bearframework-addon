# BearCMS\Data\Pages
Information about the site pages

## Methods

```php
public \BearCMS\DataObject|null getPage ( string $id )
```

Retrieves information about the page specified

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$id`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The page ID

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The page data or null if page not found

```php
public \BearCMS\DataCollection getList ( void )
```

Retrieves a list of all pages

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;List containing all pages data

## Examples

List of all pages

```php
$list = $app->bearCMS->data->pages->getList();

// \BearCMS\DataCollection
// (
//     [0] => \BearCMS\DataObject
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
//             [children] => \BearCMS\DataCollection
//         )
//     [1] => \BearCMS\DataObject
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
//             [children] => \BearCMS\DataCollection
//         )
//     ...
// )
```
