# BearCMS\Themes\Theme

```php
BearCMS\Themes\Theme {

	/* Properties */
	public callable|null $apply
	public callable|null $get
	public readonly string $id
	public callable|null $initialize
	public callable|null $manifest
	public callable|null $options
	public callable|null $styles
	public string|null $version

	/* Methods */
	public __construct ( string $id )
	public BearCMS\Themes\Theme\Manifest makeManifest ( void )
	public BearCMS\Themes\Theme\Options makeOptions ( void )
	public BearCMS\Themes\Theme\Style makeStyle ( void )

}
```

## Properties

##### public callable|null $apply

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A function to be called to apply the theme. A \BearFramework\App\Response object and a options object are passed.

##### public callable|null $get

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A function to be called to retrieve the theme template.

##### public readonly string $id

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The theme id.

##### public callable|null $initialize

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A function to be called to initialize the theme.

##### public callable|null $manifest

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A function to be called to retrieve the theme manifest (name, description, etc.).

##### public callable|null $options

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A function to be called to retrieve the theme options.

##### public callable|null $styles

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A function to be called to retrieve the theme styles.

##### public string|null $version

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The theme version.

## Methods

##### public [__construct](bearcms.themes.theme.__construct.method.md) ( string $id )

##### public [BearCMS\Themes\Theme\Manifest](bearcms.themes.theme.manifest.class.md) [makeManifest](bearcms.themes.theme.makemanifest.method.md) ( void )

##### public [BearCMS\Themes\Theme\Options](bearcms.themes.theme.options.class.md) [makeOptions](bearcms.themes.theme.makeoptions.method.md) ( void )

##### public [BearCMS\Themes\Theme\Style](bearcms.themes.theme.style.class.md) [makeStyle](bearcms.themes.theme.makestyle.method.md) ( void )

## Details

Location: ~/classes/BearCMS/Themes/Theme.php

---

[back to index](index.md)

