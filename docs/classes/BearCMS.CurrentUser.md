# BearCMS\CurrentUser
Information about the current loggedin user

## Methods

```php
public boolean exists ( void )
```

Returns information about whether there is a current user logged in

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TRUE if there is a current user logged in, FALSE otherwise

```php
public string|null getSessionKey ( void )
```

Returns the session key if there is a logged in user

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The session key if there is a logged in user, NULL otherwise

```php
public string|null getID ( void )
```

Returns the current logged in user ID

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ID of the current logged in user or null

```php
public array getPermissions ( void )
```

Returns the current logged in user permissions

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Array containing the permission of the current logged in user

```php
public boolean hasPermission ( string $name )
```

Checks whether the current logged in user has the specified permission

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$name`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The name of the permission

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TRUE if the current logged in user has the permission specified, FALSE otherwise

## Examples

Check if there is a currently logged in user

```php
$result = $app->bearCMS->currentUser->exists();

// TRUE or FALSE
```

Get the session key of the currently logged in user

```php
$key = $app->bearCMS->currentUser->getSessionKey();

// abcdefghijklmnopgqstuvwxyz1234567890
```

Get the id of the currently logged in user

```php
$id = $app->bearCMS->currentUser->getID();

// abcdefghijk1
```

Get the permissions of the currently logged in user

```php
$permissions = $app->bearCMS->currentUser->getPermissions();

// Array
// (
//     [0] => modifyContent
//     [1] => managePages
//     [2] => manageAppearance
//     [3] => manageBlog
//     [4] => manageFiles
//     [5] => manageAddons
//     [6] => manageAdministrators
//     [7] => manageSettings
//     [8] => viewAboutInformation
// )
```

Check if the currently logged in user has specific permission

```php
$result = $app->bearCMS->currentUser->hasPermission('managePages');

// TRUE or FALSE
```