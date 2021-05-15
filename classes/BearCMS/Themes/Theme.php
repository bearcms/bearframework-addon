<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Themes;

/**
 * @property-read string $id The theme id.
 * @property string|null $version The theme version.
 * @property callable|null $initialize A function to be called to initialize the theme.
 * @property callable|null $apply A function to be called to apply the theme. A \BearFramework\App\Response object and a options object are passed.
 * @property callable|null $get A function to be called to retrieve the theme template.
 * @property callable|null $manifest A function to be called to retrieve the theme manifest (name, description, etc.).
 * @property callable|null $options A function to be called to retrieve the theme options.
 * @property callable|null $styles A function to be called to retrieve the theme styles.
 * @property bool $canStyleElements Enable/disable element styling.
 * @property bool $useDefaultElementsCombinations Enable/disable default elements combinations.
 */
class Theme
{

    use \IvoPetkov\DataObjectTrait;

    /**
     * 
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this
            ->defineProperty('id', [
                'type' => 'string',
                'get' => function () use ($id) {
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
            ->defineProperty('options', [
                'type' => '?callable'
            ])
            ->defineProperty('styles', [
                'type' => '?callable'
            ])
            ->defineProperty('canStyleElements', [
                'type' => 'bool',
                'init' => function () {
                    return false;
                }
            ])
            ->defineProperty('useDefaultElementsCombinations', [
                'type' => 'bool',
                'init' => function () {
                    return false;
                }
            ]);
    }

    /**
     * 
     * @return \BearCMS\Themes\Theme\Options
     */
    public function makeOptions(): \BearCMS\Themes\Theme\Options
    {
        return new \BearCMS\Themes\Theme\Options();
    }

    /**
     * 
     * @return \BearCMS\Themes\Theme\Style
     */
    public function makeStyle(): \BearCMS\Themes\Theme\Style
    {
        return new \BearCMS\Themes\Theme\Style();
    }

    /**
     * 
     * @return \BearCMS\Themes\Theme\Manifest
     */
    public function makeManifest(): \BearCMS\Themes\Theme\Manifest
    {
        return new \BearCMS\Themes\Theme\Manifest();
    }
}
