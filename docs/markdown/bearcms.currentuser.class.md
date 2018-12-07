# BearCMS\CurrentUser

## Methods

##### public bool [exists](bearcms.currentuser.exists.method.md) ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns information about whether there is a current user logged in

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns: TRUE if there is a current user logged in, FALSE otherwise

##### public string|null [getID](bearcms.currentuser.getid.method.md) ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the current logged in user ID

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns: ID of the current logged in user or null

##### public array [getPermissions](bearcms.currentuser.getpermissions.method.md) ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the current logged in user permissions

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns: Array containing the permission of the current logged in user

##### public string|null [getSessionKey](bearcms.currentuser.getsessionkey.method.md) ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the session key if there is a logged in user

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns: The session key if there is a logged in user, NULL otherwise

##### public bool [hasPermission](bearcms.currentuser.haspermission.method.md) ( string $name )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Checks whether the current logged in user has the specified permission

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns: TRUE if the current logged in user has the permission specified, FALSE otherwise

##### public bool [login](bearcms.currentuser.login.method.md) ( string $userID )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Login a user without email and password validation. This methods must be enabled on the CMS server.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns: Returns TRUE if the user is logged in successfully, FALSE otherwise.

##### public void [logout](bearcms.currentuser.logout.method.md) ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Logout the current user.

## Details

File: /classes/BearCMS/CurrentUser.php

---

[back to index](index.md)

