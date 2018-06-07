# BearCMS\Data
Contains reference to the different data types

## Properties

`public \BearFramework\App\ServiceContainer $container`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dependency Injection container

`public \BearCMS\Data\Addons $addons`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Information about the addons managed by BearCMS

`public \BearCMS\Data\Blog $blog`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Information about the blog posts

`public \BearCMS\Data\Pages $pages`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Information about the site pages

`public \BearCMS\Data\Settings $settings`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Information about the site settings

`public \BearCMS\Data\Themes $themes`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Information about the site themes

`public \BearCMS\Data\Users $users`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Information about the CMS users (administrators)

## Methods

```php
public __construct ( void )
```

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No value is returned.

```php
public object __get ( string $name )
```

Returns an object from the dependency injection container

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$name`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The service name

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Object from the dependency injection container

```php
public boolen __isset ( string $name )
```

Returns information about whether the service is added in the dependency injection container

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$name`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The name of the service

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TRUE if services is added. FALSE otherwise.

```php
public string getRealFilename ( string $filename )
```

Converts data:, app:, addon:id: filenames to real filenames

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$filename`

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The real filename