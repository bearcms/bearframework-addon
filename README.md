![Bear CMS](http://bearcms.github.io/bearcms-logo-for-github.png)

Addon for Bear Framework

This addon enables you add CMS functionality to your [Bear Framework](https://bearframework.com/) powered website. Learn more at [bearcms.com](https://bearcms.com/).

[![Build Status](https://travis-ci.org/bearcms/bearframework-addon.svg)](https://travis-ci.org/bearcms/bearframework-addon)
[![Latest Stable Version](https://poser.pugx.org/bearcms/bearframework-addon/v/stable)](https://packagist.org/packages/bearcms/bearframework-addon)
[![codecov.io](https://codecov.io/github/bearcms/bearframework-addon/coverage.svg?branch=master)](https://codecov.io/github/bearcms/bearframework-addon?branch=master)
[![License](https://poser.pugx.org/bearcms/bearframework-addon/license)](https://packagist.org/packages/bearcms/bearframework-addon)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/45344c8c617d466bad42e4cbd5313b65)](https://www.codacy.com/app/ivo_2/bearframework-addon)

## Download and install

**Install via Composer**

```shell
composer require bearcms/bearframework-addon
```

**Download an archive**

Download the [latest release](https://github.com/bearcms/bearframework-addon/releases) from the [GitHub page](https://github.com/bearcms/bearframework-addon) and include the autoload file.
```php
include '/path/to/the/addon/autoload.php';
```

## Enable the addon
Enable the addon for your Bear Framework application.

```php
$app->addons->add('bearcms/bearframework-addon', [
    'serverUrl' => 'https://example.bearcms.com/',
    'siteID' => 'example',
    'siteSecret' => 'example',
    'addonsDir' => realpath(__DIR__ . '/../addons/'),
    'language' => 'en'
]);
```

## Documentation

You can configure the addon when added to your application. Here is a list of all [configuration options](http://).

### Direct data access

[$app->bearCMS->data->addons](https://github.com/bearcms/bearframework-addon/blob/master/docs/classes/BearCMS.Data.Addons.md)

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retrieve information about the CMS managed addons.

[$app->bearCMS->data->blog](https://github.com/bearcms/bearframework-addon/blob/master/docs/classes/BearCMS.Data.Blog.md)

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retrieve information about the blog posts created by the CMS.

[$app->bearCMS->data->pages](https://github.com/bearcms/bearframework-addon/blob/master/docs/classes/BearCMS.Data.Pages.md)

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retrieve information about the pages created by the CMS.

[$app->bearCMS->data->settings](https://github.com/bearcms/bearframework-addon/blob/master/docs/classes/BearCMS.Data.Settings.md)

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retrieve information about the site settings available in the CMS.

[$app->bearCMS->data->themes](https://github.com/bearcms/bearframework-addon/blob/master/docs/classes/BearCMS.Data.Themes.md)

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retrieve information about the themes customized by the administrators.

[$app->bearCMS->data->users](https://github.com/bearcms/bearframework-addon/blob/master/docs/classes/BearCMS.Data.Users.md)

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retrieve information about the administators.

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

### Current theme

[$app->bearCMS->currentTheme](https://github.com/bearcms/bearframework-addon/blob/master/docs/classes/BearCMS.CurrentTheme.md)

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Provides information about the current theme and it's customizations.

### Current user

[$app->bearCMS->currentUser](https://github.com/bearcms/bearframework-addon/blob/master/docs/classes/BearCMS.CurrentUser.md)

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Provides information about the current user and it's permissions.

## License
Bear CMS addon for Bear Framework is open-sourced software. It's free to use under the MIT license. See the [license file](https://github.com/bearcms/bearframework-addon/blob/master/LICENSE) for more information.

## Author
This addon is created by the Bear CMS team. Feel free to contact us at [support@bearcms.com](mailto:support@bearcms.com) or [bearcms.com](https://bearcms.com/).
