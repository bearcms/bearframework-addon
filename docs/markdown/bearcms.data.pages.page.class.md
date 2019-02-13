# BearCMS\Data\Pages\Page

```php
BearCMS\Data\Pages\Page extends BearFramework\Models\Model {

	/* Properties */
	public readonly BearFramework\Models\ModelsList|BearCMS\Data\Pages\Page[] $children
	public string|null $descriptionTagContent
	public string|null $id
	public string|null $keywordsTagContent
	public string|null $name
	public string|null $parentID
	public string|null $path
	public string|null $slug
	public string|null $status
	public string|null $titleTagContent

	/* Methods */
	public __construct ( void )
	public static void fromJSON ( string $data )

}
```

## Extends

##### BearFramework\Models\Model

## Properties

##### public readonly BearFramework\Models\ModelsList|[BearCMS\Data\Pages\Page[]](bearcms.data.pages.page.class.md) $children

##### public string|null $descriptionTagContent

##### public string|null $id

##### public string|null $keywordsTagContent

##### public string|null $name

##### public string|null $parentID

##### public string|null $path

##### public string|null $slug

##### public string|null $status

##### public string|null $titleTagContent

## Methods

##### public [__construct](bearcms.data.pages.page.__construct.method.md) ( void )

##### public static void [fromJSON](bearcms.data.pages.page.fromjson.method.md) ( string $data )

### Inherited from BearFramework\Models\Model

##### public static void fromArray ( array $data )

##### public array toArray ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the object data converted as an array.

##### public string toJSON ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the object data converted as JSON.

## Details

Location: ~/classes/BearCMS/Data/Pages/Page.php

---

[back to index](index.md)

