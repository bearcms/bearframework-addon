# BearCMS\Data\Settings\Settings

```php
BearCMS\Data\Settings\Settings extends BearFramework\Models\Model {

	/* Properties */
	public bool $allowSearchEngines
	public string|null $description
	public bool $disabled
	public string|null $disabledText
	public bool $enableRSS
	public bool $externalLinks
	public string|null $icon
	public string|null $keywords
	public string|null $language
	public string|null $rssType
	public string|null $title

	/* Methods */
	public __construct ( void )
	public static void fromArray ( array $data )

}
```

## Extends

##### BearFramework\Models\Model

## Properties

##### public bool $allowSearchEngines

##### public string|null $description

##### public bool $disabled

##### public string|null $disabledText

##### public bool $enableRSS

##### public bool $externalLinks

##### public string|null $icon

##### public string|null $keywords

##### public string|null $language

##### public string|null $rssType

##### public string|null $title

## Methods

##### public [__construct](bearcms.data.settings.settings.__construct.method.md) ( void )

##### public static void [fromArray](bearcms.data.settings.settings.fromarray.method.md) ( array $data )

### Inherited from BearFramework\Models\Model

##### public static void fromJSON ( string $data )

##### public array toArray ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the object data converted as an array.

##### public string toJSON ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the object data converted as JSON.

## Details

Location: ~/classes/BearCMS/Data/Settings/Settings.php

---

[back to index](index.md)

