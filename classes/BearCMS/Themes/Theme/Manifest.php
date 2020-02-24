<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Themes\Theme;

/**
 * @property string|null $name
 * @property string|null $description
 * @property array $author
 * @property array $media
 */
class Manifest
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
            ->defineProperty('name', [
                'type' => '?string'
            ])
            ->defineProperty('description', [
                'type' => '?string'
            ])
            ->defineProperty('author', [
                'type' => 'array'
            ])
            ->defineProperty('media', [
                'type' => 'array'
            ]);
    }
}
