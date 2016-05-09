<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS;

use BearFramework\App;

/**
 * @property \BearCMS\Data\Addons $addons
 * @property \BearCMS\Data\Blog $blog
 * @property \BearCMS\Data\Pages $pages
 * @property \BearCMS\Data\Settings $settings
 * @property \BearCMS\Data\Templates $templates
 * @property \BearCMS\Data\Users $users
 */
class Data
{

    /**
     * Dependency Injection container
     * @var \BearFramework\App\ServiceContainer 
     */
    public $container = null;

    function __construct()
    {
        $this->container = new \BearFramework\App\Container();

        $this->container->set('addons', \BearCMS\Data\Addons::class);
        $this->container->set('blog', \BearCMS\Data\Blog::class);
        $this->container->set('pages', \BearCMS\Data\Pages::class);
        $this->container->set('settings', \BearCMS\Data\Settings::class);
        $this->container->set('templates', \BearCMS\Data\Templates::class);
        $this->container->set('users', \BearCMS\Data\Users::class);
    }

    /**
     * Returns an object from the dependency injection container
     * @param string $name The service name
     * @return object Object from the dependency injection container
     * @throws \Exception
     */
    public function __get($name)
    {
        if ($this->container->has($name)) {
            return $this->container->get($name);
        }
        throw new \Exception('Invalid property name');
    }

    /**
     * Returns information about whether the service is added in the dependency injection container
     * @param string $name The name of the service
     * @return boolen TRUE if services is added. FALSE otherwise.
     */
    public function __isset($name)
    {
        return $this->container->has($name);
    }

    /**
     * Converts data:, app:, addon:id: filenames to real filenames
     * @param string $filename
     * @return string
     */
    public function getRealFilename($filename)
    {
        $app = App::$instance;
        if (substr($filename, 0, 5) === 'data:') {
            $filename = $app->data->getFilename(substr($filename, 5));
        } elseif (substr($filename, 0, 4) === 'app:') {
            $filename = $app->config->appDir . DIRECTORY_SEPARATOR . substr($filename, 4);
        } elseif (substr($filename, 0, 6) === 'addon:') {
            $temp = explode(':', $filename, 3);
            if (sizeof($temp) === 3) {
                $addonDir = \BearFramework\Addons::getDir($temp[1]);
                $filename = $addonDir . DIRECTORY_SEPARATOR . $temp[2];
            }
        }
        return $filename;
    }

}
