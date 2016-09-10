# Bear CMS
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
    'addonsDir' => realpath(__DIR__ . '/../addons/'),
    'language' => 'en'
]);
```

## License
Bear CMS addon for Bear Framework is open-sourced software. It's free to use under the MIT license. See the [license file](https://github.com/bearcms/bearframework-addon/blob/master/LICENSE) for more information.

## Author
This addon is created by the Bear CMS team. Feel free to contact us at [support@bearcms.com](mailto:support@bearcms.com) or [bearcms.com](https://bearcms.com/).
