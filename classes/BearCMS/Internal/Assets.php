<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

/**
 * @internal
 * @codeCoverageIgnore
 */
class Assets
{

    /**
     * 
     * @var array
     */
    static public array $supportedFileOptions = [
        'f' => 'svgFill',
        's' => 'svgStroke'
    ];

    /**
     * 
     * @param array $fileOptions
     * @return array
     */
    static function convertFileOptionsToAssetOptions(array $fileOptions): array
    {
        $result = [];
        foreach ($fileOptions as $name => $value) {
            if (isset(self::$supportedFileOptions[$name])) {
                $assetOptionName = self::$supportedFileOptions[$name];
                if (array_search($assetOptionName, ['svgFill', 'svgStroke']) !== false) {
                    $value = '#' . $value;
                }
                $result[$assetOptionName] = $value;
            }
        }
        return $result;
    }
}
