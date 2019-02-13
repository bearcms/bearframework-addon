# BearCMS\CurrentUser

```php
BearCMS\CurrentUser {

	/* Methods */
	public bool exists ( void )
	public string|null getID ( void )
	public array getPermissions ( void )
	public string|null getSessionKey ( void )
	public bool hasPermission ( string $name )
	public bool login ( string $userID )
	public void logout ( void )

}
```

## Methods

##### public bool [exists](bearcms.currentuser.exists.method.md) ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns information about whether there is a current user logged in

##### public string|null [getID](bearcms.currentuser.getid.method.md) ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the current logged in user ID

##### public array [getPermissions](bearcms.currentuser.getpermissions.method.md) ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the current logged in user permissions

##### public string|null [getSessionKey](bearcms.currentuser.getsessionkey.method.md) ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the session key if there is a logged in user

##### public bool [hasPermission](bearcms.currentuser.haspermission.method.md) ( string $name )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Checks whether the current logged in user has the specified permission

##### public bool [login](bearcms.currentuser.login.method.md) ( string $userID )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Login a user without email and password validation. This methods must be enabled on the CMS server.

##### public void [logout](bearcms.currentuser.logout.method.md) ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Logout the current user.

## Details

Location: ~/classes/BearCMS/CurrentUser.php

---

[back to index](index.md)

