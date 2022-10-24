<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Themes\Theme;

use BearCMS\Internal2;
use BearCMS\Internal\Themes;
use BearCMS\Internal;

/**
 * 
 */
class Customizations
{

    /**
     * Options values
     * 
     * @var array 
     */
    private $values = [];

    /**
     *
     * @var string 
     */
    private $html = '';

    /**
     * 
     * @param array $values
     * @param string $html
     * @param array $details
     */
    private $details = [];

    /**
     * 
     * @param array $values
     * @param string $html
     * @param array $details
     */
    public function __construct(array $values = [], string $html = '', array $details = [])
    {
        $this->values = $values;
        $this->html = $html;
        $this->details = $details;
    }

    /**
     * 
     * @param string $name
     * @return mixed|null
     */
    public function getValue(string $name)
    {
        return isset($this->values[$name]) ? $this->values[$name] : null;
    }

    /**
     * 
     * @param string $name
     * @param array $details Available values: defaultValue, states, responsiveAttributes=>callback
     * @return array
     */
    public function getValueDetails(string $name, array $details = []): array
    {
        $valueDetails = Themes::getValueDetails($this->getValue($name));
        foreach ($details as $detailKey => $detail) {
            if ($detail === 'defaultValue') {
                $result[$detail] = $valueDetails['value'];
            } else if ($detail === 'states') {
                $states = [];
                foreach ($valueDetails['states'] as $stateData) {
                    $states[] = ['name' => $stateData[0], 'value' => $stateData[1]];
                }
                $result[$detail] = $states;
            } else if ($detailKey === 'responsiveAttributes') {
                $getValue = function ($attributesCallback) use ($valueDetails, $name): string {
                    if (isset($this->details['values'], $this->details['values'][$name], $this->details['values'][$name]['statesResponsiveAttributes'])) {
                        $statesResponsiveAttributes = $this->details['values'][$name]['statesResponsiveAttributes'];
                    } else {
                        return '';
                    }
                    $states = $valueDetails['states'];
                    $responiveAttributeValue = [];
                    $getAttributesToSet = function (string $value) use ($attributesCallback): array { // $value is array for css options ????
                        if (!isset($value[0])) {
                            return [];
                        }
                        $attributesToSet = $attributesCallback($value);
                        return is_array($attributesToSet) ? $attributesToSet : [];
                    };
                    $attributesToSet = $getAttributesToSet($valueDetails['value']);
                    foreach ($attributesToSet as $attributeName => $attributeValue) {
                        $responiveAttributeValue[] = '1=>' . $attributeName . '=' . $attributeValue;
                    }
                    foreach ($states as $stateIndex => $stateData) {
                        if (!isset($statesResponsiveAttributes[$stateIndex])) {
                            continue;
                        }
                        $stateResponsiveAttributes = $statesResponsiveAttributes[$stateIndex];
                        $attributesToSet = $getAttributesToSet((string)$stateData[1]);
                        foreach ($attributesToSet as $attributeName => $attributeValue) {
                            foreach ($stateResponsiveAttributes as $expression) {
                                $responiveAttributeValue[] = $expression . '=>' . $attributeName . '=' . $attributeValue;
                            }
                        }
                    }
                    return implode(',', $responiveAttributeValue);
                };
                $result[$detailKey] = $getValue($detail);
            }
        }
        return $result;
    }

    /**
     * 
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * 
     * @return string
     */
    public function getHTML(): string
    {
        return $this->html;
    }

    /**
     * 
     * @param string $key
     * @param array $details Available values: filename, width, height
     * @return array
     */
    public function getAssetDetails(string $key, array $details = []): array
    {
        $result = [];
        foreach ($details as $detail) {
            if ($detail === 'filename') {
                $result[$detail] = Internal\Data::getRealFilename($key);
            } else {
                $result[$detail] = isset($this->details['assets'], $this->details['assets'][$key], $this->details['assets'][$key][$detail]) ? $this->details['assets'][$key][$detail] : null;
            }
        }
        return $result;
    }
}
