<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS;

/**
 * @property \BearCMS\Data\Addons $addons
 * @property \BearCMS\Data\BlogPosts $blogPosts
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
        $this->container->set('blogPosts', \BearCMS\Data\BlogPosts::class);
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

}
