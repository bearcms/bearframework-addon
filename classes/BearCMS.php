<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

/**
 * @property \BearCMS\Data $data
 * @property \BearCMS\CurrentTemplate $currentTemplate
 * @property \BearCMS\CurrentUser $currentUser
 */
class BearCMS
{

    const VERSION = '0.1.1-dev';

    /**
     * Dependency Injection container
     * @var \BearFramework\App\ServiceContainer 
     */
    public $container = null;

    function __construct()
    {
        $this->container = new \BearFramework\App\Container();

        $this->container->set('data', \BearCMS\Data::class);
        $this->container->set('currentTemplate', \BearCMS\CurrentTemplate::class);
        $this->container->set('currentUser', \BearCMS\CurrentUser::class);
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
