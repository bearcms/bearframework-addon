# BearCMS\Data\Users
Retrieve information about the CMS users (administrators)

## Methods

```php
public array|null getUser ( string $id )
```

Retrieves information about the user specified

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$id`

 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The user ID

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The user data or null if user not found

```php
public array getList ( void )
```

Retrieves a list of all users

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;List containing all users data

```php
public boolean hasUsers ( void )
```

Checks if there are any users

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TRUE if there is atleast one user, FALSE if there are no users

## Examples

```php
$app->bearCMS->data->users->getList()
```

```php
$app->bearCMS->data->users->hasUsers()
```
