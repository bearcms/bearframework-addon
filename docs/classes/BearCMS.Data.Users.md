# BearCMS\Data\Users
Information about the CMS users (administrators)

## Methods

```php
public \BearCMS\DataObject|null getUser ( string $id )
```

Retrieves information about the user specified

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$id`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The user ID

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The user data or null if user not found

```php
public \BearCMS\DataCollection getList ( void )
```

Retrieves a list of all users

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;List containing all users data

```php
public boolean hasUsers ( void )
```

Checks if there are any users

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TRUE if there is at least one user, FALSE if there are no users

## Examples

Returns an object containing information about the user specified 

```php
$user = $app->bearCMS->data->users->getUser('abcdefghijk1');

// \BearCMS\DataObject
// (
//     [id] => abcdefghijk1
//     [registerTime] => 1234567890
//     [lastLoginTime] => 1234567891
//     [hashedPassword] => abcdefghijklmnopgqstuvwxyz1234567890
//     [emails] => Array
//         (
//             [0] => john@example.com
//         )
//     [permissions] => Array
//         (
//             [0] => modifyContent
//             [1] => managePages
//             [2] => manageAppearance
//             [3] => manageBlog
//             [4] => manageFiles
//             [5] => manageAddons
//             [6] => manageAdministrators
//             [7] => manageSettings
//             [8] => viewAboutInformation
//         )
// )
```

Returns a collection containing all users data

```php
$list = $app->bearCMS->data->users->getList();

// \BearCMS\DataCollection
// (
//     [0] => \BearCMS\DataObject
//         (
//             [id] => abcdefghijk1
//             [registerTime] => 1234567890
//             [lastLoginTime] => 1234567891
//             [hashedPassword] => abcdefghijklmnopgqstuvwxyz1234567890
//             [emails] => Array
//                 (
//                     [0] => john@example.com
//                 )
//             [permissions] => Array
//                 (
//                     [0] => modifyContent
//                     [1] => managePages
//                     [2] => manageAppearance
//                     [3] => manageBlog
//                     [4] => manageFiles
//                     [5] => manageAddons
//                     [6] => manageAdministrators
//                     [7] => manageSettings
//                     [8] => viewAboutInformation
//                 )
//         )
//     [1] => \BearCMS\DataObject
//         (
//             [id] => abcdefghijk2
//             [registerTime] => 1234567890
//             [lastLoginTime] => 1234567891
//             [hashedPassword] => abcdefghijklmnopgqstuvwxyz1234567891
//             [emails] => Array
//                 (
//                     [0] => mark@example.com
//                 )
//             [permissions] => Array
//                 (
//                     [0] => modifyContent
//                     [1] => managePages
//                     [2] => manageBlog
//                 )
//         )
// )

```

Check if there are any users

```php
$result = $app->bearCMS->data->users->hasUsers()

// TRUE or FALSE
```
