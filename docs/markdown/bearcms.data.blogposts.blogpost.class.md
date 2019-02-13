# BearCMS\Data\BlogPosts\BlogPost

```php
BearCMS\Data\BlogPosts\BlogPost extends BearFramework\Models\Model {

	/* Properties */
	public array $categoriesIDs
	public int|null $createdTime
	public array $custom
	public string|null $descriptionTagContent
	public string|null $id
	public string|null $keywordsTagContent
	public int|null $lastChangeTime
	public string|null $publishedTime
	public string|null $slug
	public string|null $status
	public string|null $title
	public string|null $titleTagContent
	public string|null $trashedTime

	/* Methods */
	public __construct ( void )

}
```

## Extends

##### BearFramework\Models\Model

## Properties

##### public array $categoriesIDs

##### public int|null $createdTime

##### public array $custom

##### public string|null $descriptionTagContent

##### public string|null $id

##### public string|null $keywordsTagContent

##### public int|null $lastChangeTime

##### public string|null $publishedTime

##### public string|null $slug

##### public string|null $status

##### public string|null $title

##### public string|null $titleTagContent

##### public string|null $trashedTime

## Methods

##### public [__construct](bearcms.data.blogposts.blogpost.__construct.method.md) ( void )

### Inherited from BearFramework\Models\Model

##### public static void fromArray ( array $data )

##### public static void fromJSON ( string $data )

##### public array toArray ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the object data converted as an array.

##### public string toJSON ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the object data converted as JSON.

## Details

Location: ~/classes/BearCMS/Data/BlogPosts/BlogPost.php

---

[back to index](index.md)

