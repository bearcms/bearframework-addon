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
        's' => 'svgStroke',
        't' => 'rotate',
        'x' => 'crop',
    ];

    /**
     * 
     * @var array
     */
    static public array $supportedHTMLAttributes = [
        'cacheMaxAge' => ['asset-cache-max-age', 'int'],
        'quality' => ['asset-quality', 'int'],
        'svgFill' => ['asset-svg-fill', 'string'],
        'svgStroke' => ['asset-svg-stroke', 'string'],
        'rotate' => ['asset-rotate', 'int'],
        'cropX' => ['asset-crop-x', 'int'],
        'cropY' => ['asset-crop-y', 'int'],
        'cropWidth' => ['asset-crop-width', 'int'],
        'cropHeight' => ['asset-crop-height', 'int'],
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
                } elseif (array_search($assetOptionName, ['rotate']) !== false) {
                    $value = (int)$value;
                } elseif (array_search($assetOptionName, ['crop']) !== false) {
                    $cropParts = explode('x', $value);
                    $result['cropX'] = (int)$cropParts[0];
                    $result['cropY'] = (int)$cropParts[1];
                    $result['cropWidth'] = (int)$cropParts[2];
                    $result['cropHeight'] = (int)$cropParts[3];
                    continue;
                }
                $result[$assetOptionName] = $value;
            }
        }
        return $result;
    }

    /**
     * 
     * @param array $assetOptions
     * @return string
     */
    static function convertAssetOptionsToHTMLAttributes(array $assetOptions): string
    {
        $result = '';
        foreach ($assetOptions as $name => $value) {
            $result .= ' ' . self::$supportedHTMLAttributes[$name][0] . '="' . htmlentities((string)$value) . '"';
        }
        return $result;
    }

    /**
     * 
     * @param array $attributes
     * @param array $defaultValues
     * @return array
     */
    static function getAssetOptionsFromHTMLAttributes(array $attributes, array $defaultValues = []): array
    {
        $result = [];
        foreach (self::$supportedHTMLAttributes as $optionName => $attributeData) {
            $attributeName = $attributeData[0];
            $value = null;
            if (isset($attributes[$attributeName])) {
                $value = $attributes[$attributeName];
                if ($attributeData[1] === 'int') {
                    $value = (int)$value;
                }
            } elseif (isset($defaultValues[$optionName])) {
                $value = $defaultValues[$optionName];
            }
            if ($value !== null) {
                $result[$optionName] = $value;
            }
        }
        return $result;
    }
}
