<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

/**
 * Contains references to all Bear CMS related objects.
 * 
 * @property \BearCMS\Data $data A reference to the data related objects
 * @property \BearCMS\CurrentTemplate $currentTemplate Information about the current template
 * @property \BearCMS\CurrentUser $currentUser Information about the current loggedin user
 */
class BearCMS
{

    /**
     * Addon version
     */
    const VERSION = '0.2.2';

    /**
     * Dependency Injection container
     * 
     * @var \BearFramework\App\ServiceContainer 
     */
    public $container = null;

    /**
     * The constructor
     */
    function __construct()
    {
        $this->container = new \BearFramework\App\Container();

        $this->container->set('data', \BearCMS\Data::class);
        $this->container->set('currentTemplate', \BearCMS\CurrentTemplate::class);
        $this->container->set('currentUser', \BearCMS\CurrentUser::class);
    }

    /**
     * Returns an object from the dependency injection container
     * 
     * @param string $name The service name
     * @return object Object from the dependency injection container
     * @throws \Exception
     */
    public function __get($name)
    {
        if ($this->container->exists($name)) {
            return $this->container->get($name);
        }
        throw new \Exception('Invalid property name');
    }

    /**
     * Returns information about whether the service is added in the dependency injection container
     * 
     * @param string $name The name of the service
     * @return boolen TRUE if services is added. FALSE otherwise.
     */
    public function __isset($name)
    {
        return $this->container->exists($name);
    }

}
