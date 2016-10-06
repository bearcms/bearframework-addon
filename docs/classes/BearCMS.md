# BearCMS
Contains references to all Bear CMS related objects.

## Constants

`const string VERSION`

## Properties

`public \BearFramework\App\ServiceContainer $container`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dependency Injection container

`public \BearCMS\Data $data`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A reference to the data related objects

`public \BearCMS\CurrentTheme $currentTheme`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Information about the current theme

`public \BearCMS\CurrentUser $currentUser`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Information about the current loggedin user

## Methods

```php
public __construct ( void )
```

The constructor

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