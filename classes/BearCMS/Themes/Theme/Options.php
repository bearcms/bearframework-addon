<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Themes\Theme;

use BearCMS\Internal\Themes as InternalThemes;

/**
 * 
 */
class Options implements \BearCMS\Internal\ThemeOptionsGroupInterface
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
        if ($valuesCount > 0) {
            $walkOptions = function ($options) use (&$walkOptions, &$valuesSetCount, $valuesCount, $values) {
                foreach ($options as $option) {
                    if ($option instanceof \BearCMS\Themes\Theme\Options\Option) {
                        if (isset($values[$option->id])) {
                            $value = $values[$option->id];
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
        }
        return $this;
    }

    /**
     * 
     * @param boolean $useDefaultValues
     * @return array
     */
    public function getValues(bool $useDefaultValues = false): array
    {
        $result = [];
        $walkOptions = function ($options) use (&$walkOptions, &$result, $useDefaultValues) {
            foreach ($options as $option) {
                if ($option instanceof \BearCMS\Themes\Theme\Options\Option) {
                    $result[$option->id] = isset($option->details['value']) ? $option->details['value'] : ($useDefaultValues && isset($option->details['defaultValue']) ? $option->details['defaultValue'] : null);
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
