<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Themes\Theme\Options;

/**
 * 
 */
class Group implements \BearCMS\Internal\ThemeOptionsGroupInterface
{

    use \BearCMS\Internal\ThemeOptionsGroupTrait;

    /**
     *
     * @var string 
     */
    public $name = '';

    /**
     *
     * @var string 
     */
    public $description = '';

    /**
     *
     * @var array
     */
    public $details = [];
}
