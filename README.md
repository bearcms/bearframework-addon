![Bear CMS](https://bearcms.github.io/bearcms-logo-for-github.png)

Addon for Bear Framework

This addon enables you add CMS functionality to your [Bear Framework](https://bearframework.com/) powered website. Learn more at [bearcms.com](https://bearcms.com/).

[![Build Status](https://travis-ci.org/bearcms/bearframework-addon.svg)](https://travis-ci.org/bearcms/bearframework-addon)
[![Latest Stable Version](https://poser.pugx.org/bearcms/bearframework-addon/v/stable)](https://packagist.org/packages/bearcms/bearframework-addon)
[![codecov.io](https://codecov.io/github/bearcms/bearframework-addon/coverage.svg?branch=master)](https://codecov.io/github/bearcms/bearframework-addon?branch=master)
[![License](https://poser.pugx.org/bearcms/bearframework-addon/license)](https://packagist.org/packages/bearcms/bearframework-addon)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/45344c8c617d466bad42e4cbd5313b65)](https://www.codacy.com/app/ivo_2/bearframework-addon)

## Standalone version

There is a standalone version that is easier to install and update. You can download the installer from your [bearcms.com](https://bearcms.com/) account.

## Install via Composer

```shell
composer require bearcms/bearframework-addon
```

## Enable the addon
Enable the addon for your Bear Framework application.

```php
$app->addons->add('bearcms/bearframework-addon');
$app->bearCMS->initialize([
    'serverUrl' => 'https://example.bearcms.com/',
    'appSecretKey' => '...',
    'language' => 'en'
]);
```

## Documentation

Full [documentation](https://github.com/bearcms/bearframework-addon/blob/master/docs/markdown/index.md) is available as part of this repository.

### Components

[&lt;component src="bearcms-elements" /&gt;](https://github.com/bearcms/bearframework-addon/blob/master/docs/components/bearcms-elements.md)

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Creates an elements block.

[&lt;component src="bearcms-blog-posts-element" /&gt;](https://github.com/bearcms/bearframework-addon/blob/master/docs/components/bearcms-blog-posts-element.md)

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Creates a block that renders a list of blog posts.

[&lt;component src="bearcms-heading-element" /&gt;](https://github.com/bearcms/bearframework-addon/blob/master/docs/components/bearcms-heading-element.md)

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Creates a heading.

[&lt;component src="bearcms-html-element" /&gt;](https://github.com/bearcms/bearframework-addon/blob/master/docs/components/bearcms-html-element.md)

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Renders HTML code.

[&lt;component src="bearcms-image-element" /&gt;](https://github.com/bearcms/bearframework-addon/blob/master/docs/components/bearcms-image-element.md)

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Creates an image.

[&lt;component src="bearcms-image-gallery-element" /&gt;](https://github.com/bearcms/bearframework-addon/blob/master/docs/components/bearcms-image-gallery-element.md)

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Creates an image gallery.

[&lt;component src="bearcms-link-element" /&gt;](https://github.com/bearcms/bearframework-addon/blob/master/docs/components/bearcms-link-element.md)

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Creates a link.

[&lt;component src="bearcms-navigation-element" /&gt;](https://github.com/bearcms/bearframework-addon/blob/master/docs/components/bearcms-navigation-element.md)

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Creates a navigation.

[&lt;component src="bearcms-text-element" /&gt;](https://github.com/bearcms/bearframework-addon/blob/master/docs/components/bearcms-text-element.md)

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Creates a text block.

[&lt;component src="bearcms-video-element" /&gt;](https://github.com/bearcms/bearframework-addon/blob/master/docs/components/bearcms-video-element.md)

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Creates a video block.

### Configuration

Here is a list of the configuration options of the CMS:

`serverUrl`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The URL of the CMS server. Can be found at your Bear CMS account.

`appSecretKey`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The site secret key. Can be found at your Bear CMS account.

`language`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The language of the CMS admin interface. Available values: en, bg.

`features`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;An array containing the enabled CMS features. Available values:

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`ALL` Enables all features.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`ADDONS` Enables addons.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`FILES` Enables user files management (uploads, sharing, etc.).

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`BLOG` Enables blog posts.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`PAGES` Enables managing pages.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`ELEMENTS` Enables creating elements.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`ELEMENTS_HEADING` Enables the heading element.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`ELEMENTS_TEXT` Enables the text element.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`ELEMENTS_LINK` Enables the link element.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`ELEMENTS_IMAGE` Enables the image element.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`ELEMENTS_IMAGE_GALLERY` Enables the image gallery element.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`ELEMENTS_VIDEO` Enables the video element.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`ELEMENTS_NAVIGATION` Enables the navigation element.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`ELEMENTS_HTML` Enables the HTML element.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`ELEMENTS_BLOG_POSTS` Enables the blog posts element.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`ELEMENTS_COLUMNS` Enables the columns element.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`THEMES` Enables themes management.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`ABOUT` Enables viewing the system information about the website.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`SETTINGS` Enables managing settings.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`USERS` Enables users.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`USERS_LOGIN_DEFAULT` Enables users the login the default way (login form, lost password form, etc.)

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`USERS_LOGIN_ANONYMOUS` Enables anonymous user login (by calling the CMS server with code).

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`USERS_MANAGE_ACCOUNT` Enables the user to manage his account (change password and emails).

`adminPagesPathPrefix`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The path prefix for the administrators login, lost password and invite pages. The default value is "/admin/".

`blogPagesPathPrefix`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The path prefix for the blog posts pages. The default value is "/b/".

`autoCreateHomePage`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Automatically create editable elements container in the home page if no other response is defined. The default value is `true`.

## License
This project is licensed under the MIT License. See the [license file](https://github.com/bearcms/bearframework-addon/blob/master/LICENSE) for more information.

## Author
This addon is created and maintained by the Bear CMS team. Feel free to contact us at [support@bearcms.com](mailto:support@bearcms.com) or [bearcms.com](https://bearcms.com/).
