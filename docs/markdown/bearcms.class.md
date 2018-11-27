# BearCMS

## Properties

##### public readonly [BearCMS\Addons](bearcms.addons.class.md) $addons

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Information about the enabled Bear CMS addons.

##### public readonly [BearCMS\CurrentUser](bearcms.currentuser.class.md) $currentUser

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Information about the current CMS administrator.

##### public readonly [BearCMS\Themes](bearcms.themes.class.md) $themes

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Information about the enabled Bear CMS themes.

## Methods

##### public [__construct](bearcms.__construct.method.md) ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Constructs a new Bear CMS instance.

##### public void [apply](bearcms.apply.method.md) ( \BearFramework\App\Response $response )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Applies all Bear CMS modifications (the default HTML, theme and admin UI) to the response.

##### public void [applyAdminUI](bearcms.applyadminui.method.md) ( \BearFramework\App\Response $response )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Add the Bear CMS admin UI to the response, if an administrator is logged in.

##### public void [applyDefaults](bearcms.applydefaults.method.md) ( \BearFramework\App\Response $response )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Add the default Bear CMS HTML to the response.

##### public void [applyTheme](bearcms.applytheme.method.md) ( \BearFramework\App\Response $response )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Applies the currently selected Bear CMS theme to the response provided.

##### public \BearFramework\App\Response|null [disabledCheck](bearcms.disabledcheck.method.md) ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A middleware to be used in routes that returns a temporary unavailable response if an administrator has disabled the app.

##### public void [initialize](bearcms.initialize.method.md) ( array $config )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Initializes the Bear CMS instance.

##### protected object [defineProperty](bearcms.defineproperty.method.md) ( string $name [, array $options = [] ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Defines a new property.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns: Returns a reference to the object.

## Details

File: /classes/BearCMS.php

---

[back to index](index.md)

