<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS;

/**
 * 
 * @property \BearCMS\Data\Settings $settings
 */
class Data
{

    use \IvoPetkov\DataObjectTrait;

    function __construct()
    {
        $this
                ->defineProperty('settings', [
                    'init' => function() {
                        return new \BearCMS\Data\Settings();
                    },
                    'readonly' => true
                ])
        ;
    }

}
