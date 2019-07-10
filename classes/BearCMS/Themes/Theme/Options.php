<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Themes\Theme;

use BearFramework\App;
use BearCMS\Internal\Themes as InternalThemes;

/**
 * 
 */
class Options
{

    use \BearCMS\Internal\ThemeOptionsGroupTrait;

    /**
     * 
     * @param string $id
     * @param mixed $value
     * @return self
     */
    public function setValue(string $id, $value): self
    {
        $this->setValues([$id => $value]);
        return $this;
    }

    /**
     * 
     * @param array $values
     * @return self
     */
    public function setValues(array $values): self
    {
        $valuesSetCount = 0;
        $valuesCount = sizeof($values);

        $updateAppDataKey = function ($filename) {
            if (substr($filename, 0, 5) === 'data:') {
                return 'appdata://' . substr($filename, 5);
            }
            return $filename;
        };

        $walkOptions = function ($options) use (&$walkOptions, &$valuesSetCount, $valuesCount, $values, $updateAppDataKey) {
            foreach ($options as $option) {
                if ($option instanceof \BearCMS\Themes\Theme\Options\Option) {
                    if (isset($values[$option->id])) {
                        $value = $values[$option->id];
                        $optionType = $option->type;
                        if ($optionType === 'image') {
                            $value = $updateAppDataKey($value);
                        } elseif ($optionType === 'css' || $optionType === 'cssBackground') {
                            if (strpos($value, 'url') !== false) {
                                $temp = json_decode($value, true);
                                if (is_array($temp)) {
                                    $hasChange = false;
                                    foreach ($temp as $_key => $_value) {
                                        $matches = [];
                                        preg_match_all('/url\((.*?)\)/', $_value, $matches);
                                        if (!empty($matches[1])) {
                                            $temp2 = array_unique($matches[1]);
                                            foreach ($temp2 as $_value2) {
                                                $updatedValue2 = $updateAppDataKey($_value2);
                                                $temp[$_key] = str_replace($_value2, $updatedValue2, $temp[$_key]);
                                            }
                                            $hasChange = true;
                                        }
                                    }
                                    if ($hasChange) {
                                        $value = json_encode($temp);
                                    }
                                }
                            }
                        }
                        $option->details['value'] = $value;
                        $valuesSetCount++;
                        if ($valuesSetCount === $valuesCount) {
                            return true;
                        }
                    }
                } elseif ($option instanceof \BearCMS\Themes\Theme\Options\Group) {
                    if ($walkOptions($option->getList())) {
                        return;
                    }
                }
            }
        };
        $walkOptions($this->options);
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getValues(): array
    {
        $result = [];
        $walkOptions = function ($options) use (&$walkOptions, &$result) {
            foreach ($options as $option) {
                if ($option instanceof \BearCMS\Themes\Theme\Options\Option) {
                    $result[$option->id] = isset($option->details['value']) ? $option->details['value'] : null;
                } elseif ($option instanceof \BearCMS\Themes\Theme\Options\Group) {
                    if ($walkOptions($option->getList())) {
                        return;
                    }
                }
            }
        };
        $walkOptions($this->options);
        return $result;
    }

    /**
     * 
     * @return string
     */
    public function getHTML(): string
    {
        return InternalThemes::processOptionsHTMLData(InternalThemes::getOptionsHTMLData($this->options));
    }
}
