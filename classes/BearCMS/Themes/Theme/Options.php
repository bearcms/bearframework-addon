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
     * @param boolean $setDefaultValue
     * @return self
     */
    public function setValue(string $id, $value, bool $setDefaultValue = false): self
    {
        $this->setValues([$id => $value], $setDefaultValue);
        return $this;
    }

    /**
     * 
     * @param array $values
     * @param boolean $setDefaultValues
     * @return self
     */
    public function setValues(array $values, bool $setDefaultValues = false): self
    {
        $valuesSetCount = 0;
        $valuesCount = count($values);
        if ($valuesCount > 0) {
            $walkOptions = function ($options) use (&$walkOptions, &$valuesSetCount, $valuesCount, $values, $setDefaultValues) {
                foreach ($options as $option) {
                    if ($option instanceof \BearCMS\Themes\Theme\Options\Option) {
                        if (isset($values[$option->id])) {
                            $value = $values[$option->id];
                            $option->details[$setDefaultValues ? 'defaultValue' : 'value'] = $value;
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
        $walkOptions = function ($options) use (&$walkOptions, &$result, $useDefaultValues): void {
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
     * @param array $options
     * @return string
     */
    public function getHTML(array $options = []): string
    {
        $includeEditorData = array_search('internalIncludeEditorData', $options) !== false;
        return InternalThemes::processOptionsHTMLData(InternalThemes::getOptionsHTMLData($this->options, false, false, $includeEditorData));
    }
}
