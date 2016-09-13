# BearCMS\Data\Blog
Information about the blog posts

## Methods

```php
public array|null getPost ( string $id )
```

Retrieves information about the blog post specified

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$id`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The blog post ID

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The blog post data or null if blog post not found

```php
public array getList ( void )
```

Retrieves a list of all blog posts

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;List containing all blog posts data

## Examples

Returns an array containing information about the user specified 

```php
$list = $app->bearCMS->data->blog->getList();

// Array
// (
//     [0] => Array
//         (
//             [id] => abcdefghijk1
//             [title] => My first blog post
//             [slug] => 
//             [createdTime] => 1234567890
//             [status] => draft
//             [titleTagContent] => 
//             [descriptionTagContent] => 
//             [keywordsTagContent] => 
//         )
//     [1] => Array
//         (
//             [id] => abcdefghijk2
//             [title] => My second blog post
//             [slug] => my-second-blog-post
//             [createdTime] => 1234567891
//             [status] => published
//             [publishedTime] => 1234567892
//             [titleTagContent] => 
//             [descriptionTagContent] => 
//             [keywordsTagContent] => 
//         )
//     ...
// )
```

Returns an array containing all users data

```php
$post = $app->bearCMS->data->blog->getPost('abcdefghijk1');

// Array
// (
//     [id] => abcdefghijk1
//     [title] => My first blog post
//     [slug] => 
//     [createdTime] => 1234567890
//     [status] => draft
//     [titleTagContent] => 
//     [descriptionTagContent] => 
//     [keywordsTagContent] => 
// )

```
