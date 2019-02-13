# BearCMS

```php
BearCMS {

	/* Constants */
	const string VERSION

	/* Properties */
	public readonly BearCMS\Addons $addons
	public readonly BearCMS\CurrentUser $currentUser
	public readonly BearCMS\Data $data
	public readonly BearCMS\Themes $themes

	/* Methods */
	public __construct ( void )
	public self addEventListener ( string $name , callable $listener )
	public void apply ( BearFramework\App\Response $response )
	public void applyAdminUI ( BearFramework\App\Response $response )
	public void applyDefaults ( BearFramework\App\Response $response )
	public void applyTheme ( BearFramework\App\Response $response )
	public BearFramework\App\Response|null disabledCheck ( void )
	public self dispatchEvent ( string $name [, mixed $details ] )
	public bool hasEventListeners ( string $name )
	public void initialize ( array $config )
	public self removeEventListener ( string $name , callable $listener )

}
```

## Constants

##### const string VERSION

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bear CMS version.

## Properties

##### public readonly [BearCMS\Addons](bearcms.addons.class.md) $addons

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Information about the enabled Bear CMS addons.

##### public readonly [BearCMS\CurrentUser](bearcms.currentuser.class.md) $currentUser

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Information about the current CMS administrator.

##### public readonly [BearCMS\Data](bearcms.data.class.md) $data

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Access to the CMS data.

##### public readonly [BearCMS\Themes](bearcms.themes.class.md) $themes

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Information about the enabled Bear CMS themes.

## Methods

##### public [__construct](bearcms.__construct.method.md) ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Constructs a new Bear CMS instance.

##### public self [addEventListener](bearcms.addeventlistener.method.md) ( string $name , callable $listener )

##### public void [apply](bearcms.apply.method.md) ( BearFramework\App\Response $response )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Applies all Bear CMS modifications (the default HTML, theme and admin UI) to the response.

##### public void [applyAdminUI](bearcms.applyadminui.method.md) ( BearFramework\App\Response $response )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Add the Bear CMS admin UI to the response, if an administrator is logged in.

##### public void [applyDefaults](bearcms.applydefaults.method.md) ( BearFramework\App\Response $response )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Add the default Bear CMS HTML to the response.

##### public void [applyTheme](bearcms.applytheme.method.md) ( BearFramework\App\Response $response )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Applies the currently selected Bear CMS theme to the response provided.

##### public BearFramework\App\Response|null [disabledCheck](bearcms.disabledcheck.method.md) ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A middleware to be used in routes that returns a temporary unavailable response if an administrator has disabled the app.

##### public self [dispatchEvent](bearcms.dispatchevent.method.md) ( string $name [, mixed $details ] )

##### public bool [hasEventListeners](bearcms.haseventlisteners.method.md) ( string $name )

##### public void [initialize](bearcms.initialize.method.md) ( array $config )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Initializes the Bear CMS instance.

##### public self [removeEventListener](bearcms.removeeventlistener.method.md) ( string $name , callable $listener )

## Details

Location: ~/classes/BearCMS.php

---

[back to index](index.md)

