<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Themes\Theme;

/**
 * @property string|null $id
 * @property string|null $name
 * @property array $media
 * @property array $values
 */
class Style
{

    use \IvoPetkov\DataObjectTrait;
    use \IvoPetkov\DataObjectToArrayTrait;
    use \IvoPetkov\DataObjectToJSONTrait;

    /**
     * 
     */
    public function __construct()
    {
        $this
                ->defineProperty('id', [
                    'type' => '?string'
                ])
                ->defineProperty('name', [
                    'type' => '?string'
                ])
                ->defineProperty('media', [
                    'type' => 'array'
                ])
                ->defineProperty('values', [
                    'type' => 'array'
                ])
        ;
    }

}
