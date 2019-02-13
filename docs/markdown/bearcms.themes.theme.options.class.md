# BearCMS\Themes\Theme\Options

```php
BearCMS\Themes\Theme\Options {

	/* Methods */
	public self add ( BearCMS\Themes\Theme\Options\Option|BearCMS\Themes\Theme\Options\Group $optionOrGroup )
	public self addCustomCSS ( [ string $id = 'customCSS' ] )
	public self addElements ( string $idPrefix , string $parentSelector )
	public BearCMS\Themes\Theme\Options\Group addElementsGroup ( string $idPrefix , string $parentSelector )
	public BearCMS\Themes\Theme\Options\Group addGroup ( string $name [, string $description = '' ] )
	public self addOption ( string $id , string $type , string $name [, array $details = [] ] )
	public self addPages ( void )
	public BearCMS\Themes\Theme\Options\Group addPagesGroup ( void )
	public string getHTML ( void )
	public array getList ( void )
	public self setValue ( string $id , mixed $value )
	public self setValues ( array $values )

}
```

## Methods

##### public self [add](bearcms.themes.theme.options.add.method.md) ( [BearCMS\Themes\Theme\Options\Option](bearcms.themes.theme.options.option.class.md)|[BearCMS\Themes\Theme\Options\Group](bearcms.themes.theme.options.group.class.md) $optionOrGroup )

##### public self [addCustomCSS](bearcms.themes.theme.options.addcustomcss.method.md) ( [ string $id = 'customCSS' ] )

##### public self [addElements](bearcms.themes.theme.options.addelements.method.md) ( string $idPrefix , string $parentSelector )

##### public [BearCMS\Themes\Theme\Options\Group](bearcms.themes.theme.options.group.class.md) [addElementsGroup](bearcms.themes.theme.options.addelementsgroup.method.md) ( string $idPrefix , string $parentSelector )

##### public [BearCMS\Themes\Theme\Options\Group](bearcms.themes.theme.options.group.class.md) [addGroup](bearcms.themes.theme.options.addgroup.method.md) ( string $name [, string $description = '' ] )

##### public self [addOption](bearcms.themes.theme.options.addoption.method.md) ( string $id , string $type , string $name [, array $details = [] ] )

##### public self [addPages](bearcms.themes.theme.options.addpages.method.md) ( void )

##### public [BearCMS\Themes\Theme\Options\Group](bearcms.themes.theme.options.group.class.md) [addPagesGroup](bearcms.themes.theme.options.addpagesgroup.method.md) ( void )

##### public string [getHTML](bearcms.themes.theme.options.gethtml.method.md) ( void )

##### public array [getList](bearcms.themes.theme.options.getlist.method.md) ( void )

##### public self [setValue](bearcms.themes.theme.options.setvalue.method.md) ( string $id , mixed $value )

##### public self [setValues](bearcms.themes.theme.options.setvalues.method.md) ( array $values )

## Details

Location: ~/classes/BearCMS/Themes/Theme/Options.php

---

[back to index](index.md)

