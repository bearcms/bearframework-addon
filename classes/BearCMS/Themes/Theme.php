<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Themes;

/**
 * @property-read string $id The id.
 * @property string|null $version The theme version.
 * @property callable|null $initialize A function to be called to initialize the theme.
 * @property callable|null $apply A function to be called to apply the theme. A \BearFramework\App\Response object and a options object are passed.
 * @property callable|null $get A function to be called to retrieve the theme template.
 * @property callable|null $manifest A function to be called to retrieve the theme manifest (name, description, etc.).
 * @property callable|null $optionsSchema A function to be called to retrieve the theme optionsSchema.
 * @property callable|null $styles A function to be called to retrieve the theme styles.
 */
class Theme
{

    use \IvoPetkov\DataObjectTrait;

    public function __construct(string $id)
    {
        $this
                ->defineProperty('id', [
                    'type' => 'string',
                    'get' => function() use ($id) {
                        return $id;
                    },
                    'readonly' => true
                ])
                ->defineProperty('version', [
                    'type' => '?string'
                ])
                ->defineProperty('initialize', [
                    'type' => '?callable'
                ])
                ->defineProperty('apply', [
                    'type' => '?callable'
                ])
                ->defineProperty('get', [
                    'type' => '?callable'
                ])
                ->defineProperty('manifest', [
                    'type' => '?callable'
                ])
                ->defineProperty('optionsSchema', [
                    'type' => '?callable'
                ])
                ->defineProperty('styles', [
                    'type' => '?callable'
                ])
        ;
    }

}
