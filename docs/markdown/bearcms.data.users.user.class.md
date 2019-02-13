# BearCMS\Data\Users\User

```php
BearCMS\Data\Users\User extends BearFramework\Models\Model {

	/* Properties */
	public array $emails
	public string|null $hashedPassword
	public string $id
	public int|null $lastLoginTime
	public array $permissions
	public int|null $registerTime

	/* Methods */
	public __construct ( void )

}
```

## Extends

##### BearFramework\Models\Model

## Properties

##### public array $emails

##### public string|null $hashedPassword

##### public string $id

##### public int|null $lastLoginTime

##### public array $permissions

##### public int|null $registerTime

## Methods

##### public [__construct](bearcms.data.users.user.__construct.method.md) ( void )

### Inherited from BearFramework\Models\Model

##### public static void fromArray ( array $data )

##### public static void fromJSON ( string $data )

##### public array toArray ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the object data converted as an array.

##### public string toJSON ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the object data converted as JSON.

## Details

Location: ~/classes/BearCMS/Data/Users/User.php

---

[back to index](index.md)

