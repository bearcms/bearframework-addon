# BearCMS\Themes\Theme\Options\Group

```php
BearCMS\Themes\Theme\Options\Group {

	/* Properties */
	public string $description
	public string $name

	/* Methods */
	public self add ( BearCMS\Themes\Theme\Options\Option|BearCMS\Themes\Theme\Options\Group $optionOrGroup )
	public self addCustomCSS ( [ string $id = 'customCSS' ] )
	public self addElements ( string $idPrefix , string $parentSelector )
	public BearCMS\Themes\Theme\Options\Group addElementsGroup ( string $idPrefix , string $parentSelector )
	public BearCMS\Themes\Theme\Options\Group addGroup ( string $name [, string $description = '' ] )
	public self addOption ( string $id , string $type , string $name [, array $details = [] ] )
	public self addPages ( void )
	public BearCMS\Themes\Theme\Options\Group addPagesGroup ( void )
	public array getList ( void )

}
```

## Properties

##### public string $description

##### public string $name

## Methods

##### public self [add](bearcms.themes.theme.options.group.add.method.md) ( [BearCMS\Themes\Theme\Options\Option](bearcms.themes.theme.options.option.class.md)|[BearCMS\Themes\Theme\Options\Group](bearcms.themes.theme.options.group.class.md) $optionOrGroup )

##### public self [addCustomCSS](bearcms.themes.theme.options.group.addcustomcss.method.md) ( [ string $id = 'customCSS' ] )

##### public self [addElements](bearcms.themes.theme.options.group.addelements.method.md) ( string $idPrefix , string $parentSelector )

##### public [BearCMS\Themes\Theme\Options\Group](bearcms.themes.theme.options.group.class.md) [addElementsGroup](bearcms.themes.theme.options.group.addelementsgroup.method.md) ( string $idPrefix , string $parentSelector )

##### public [BearCMS\Themes\Theme\Options\Group](bearcms.themes.theme.options.group.class.md) [addGroup](bearcms.themes.theme.options.group.addgroup.method.md) ( string $name [, string $description = '' ] )

##### public self [addOption](bearcms.themes.theme.options.group.addoption.method.md) ( string $id , string $type , string $name [, array $details = [] ] )

##### public self [addPages](bearcms.themes.theme.options.group.addpages.method.md) ( void )

##### public [BearCMS\Themes\Theme\Options\Group](bearcms.themes.theme.options.group.class.md) [addPagesGroup](bearcms.themes.theme.options.group.addpagesgroup.method.md) ( void )

##### public array [getList](bearcms.themes.theme.options.group.getlist.method.md) ( void )

## Details

Location: ~/classes/BearCMS/Themes/Theme/Options/Group.php

---

[back to index](index.md)

