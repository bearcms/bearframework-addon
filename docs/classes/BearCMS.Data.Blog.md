# BearCMS\Data\Blog
Information about the blog posts

## Methods

```php
public \BearCMS\DataObject|null getPost ( string $id )
```

Retrieves information about the blog post specified

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$id`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The blog post ID

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The blog post data or null if blog post not found

```php
public \BearCMS\DataList getList ( void )
```

Retrieves a list of all blog posts

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;List containing all blog posts data

## Examples

Returns a collection containing all blog posts

```php
$list = Internal2::$data2->blogPosts->getList();

// \BearCMS\DataList
// (
//     [0] => \BearCMS\DataObject
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
//     [1] => \BearCMS\DataObject
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

Returns an object containing the blog post data

```php
$post = Internal2::$data2->blogPosts->getPost('abcdefghijk1');

// \BearCMS\DataObject
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
