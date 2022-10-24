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
class CanvasElementHelper
{

    /**
     * 
     * @param string $value
     * @param boolean $includeOptions
     * @return array
     */
    static function getFilesInValue(string $value, bool $includeOptions = false): array
    {
        $result = [];
        $parsedValue = json_decode($value, true);
        if (is_array($parsedValue)) {
            if (isset($parsedValue['elements']) && is_array($parsedValue['elements'])) {
                foreach ($parsedValue['elements'] as $elementData) {
                    if (is_array($elementData) && isset($elementData['type'], $elementData['style']) && is_string($elementData['type']) && is_array($elementData['style'])) {
                        $files = Themes::getFilesInValues($elementData['style'], $includeOptions);
                        $result = array_merge($result, $files);
                    }
                }
            }
            if (isset($parsedValue['background']) && is_array($parsedValue['background']) && isset($parsedValue['background']['style']) && is_array($parsedValue['background']['style'])) {
                $files = Themes::getFilesInValues($parsedValue['background']['style'], $includeOptions);
                $result = array_merge($result, $files);
            }
        }
        return $result;
    }

    /**
     * 
     * @param string $value
     * @param array $filesToUpdate
     * @return string
     */
    static function updateFilesInValue(string $value, array $filesToUpdate): string
    {
        $parsedValue = json_decode($value, true);
        if (is_array($parsedValue)) {
            if (isset($parsedValue['elements']) && is_array($parsedValue['elements'])) {
                foreach ($parsedValue['elements'] as $i => $elementData) {
                    if (is_array($elementData) && isset($elementData['type'], $elementData['style']) && is_string($elementData['type']) && is_array($elementData['style'])) {
                        $elementData['style'] = Themes::updateFilesInValues($elementData['style'], $filesToUpdate);
                        $parsedValue['elements'][$i] = $elementData;
                    }
                }
            }
            if (isset($parsedValue['background']) && is_array($parsedValue['background']) && isset($parsedValue['background']['style']) && is_array($parsedValue['background']['style'])) {
                $parsedValue['background']['style'] = Themes::updateFilesInValues($parsedValue['background']['style'], $filesToUpdate);
            }
            return json_encode($parsedValue, JSON_THROW_ON_ERROR);
        }
        return '';
    }
}
