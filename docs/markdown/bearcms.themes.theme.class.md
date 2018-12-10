# BearCMS\Themes\Theme

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

##### public callable|null $optionsSchema

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A function to be called to retrieve the theme optionsSchema.

##### public callable|null $styles

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A function to be called to retrieve the theme styles.

##### public string|null $version

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The theme version.

## Methods

##### public [__construct](bearcms.themes.theme.__construct.method.md) ( string $id )

##### protected object [defineProperty](bearcms.themes.theme.defineproperty.method.md) ( string $name [, array $options = [] ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Defines a new property.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns: Returns a reference to the object.

## Details

File: /classes/BearCMS/Themes/Theme.php

---

[back to index](index.md)

