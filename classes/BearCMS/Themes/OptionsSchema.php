<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Themes;

/**
 * 
 */
class OptionsSchema extends \BearCMS\Themes\OptionsGroupSchema
{

    /**
     * 
     */
    public function __construct()
    {
        parent::__construct('', '');
    }

    /**
     * 
     * @return array
     */
    public function toArray(): array
    {
        $result = parent::toArray();
        return isset($result['options']) ? $result['options'] : [];
    }

    /**
     * 
     * @param string $id
     * @param mixed $value
     * @return self
     */
    public function setDefaultValue(string $id, $value): self
    {
        $this->setDefaultValues([$id => $value]);
        return $this;
    }

    /**
     * 
     * @param array $values
     * @return self
     */
    public function setDefaultValues(array $values): self
    {
        $valuesSetCount = 0;
        $valuesCount = sizeof($values);
        $walkOptions = function(&$options) use (&$walkOptions, &$valuesSetCount, $valuesCount, $values) {
            foreach ($options as $i => $option) {
                if ($option instanceof \BearCMS\Themes\OptionsGroupSchema) {
                    if ($walkOptions($option->options)) {
                        return;
                    }
                } elseif (is_array($option) && isset($option['type'], $option['options']) && $option['type'] === 'group') {
                    if ($walkOptions($options[$i]['options'])) {
                        return;
                    }
                } elseif (is_array($option) && isset($option['id']) && isset($values[$option['id']])) {
                    $options[$i]['defaultValue'] = $values[$option['id']];
                    $valuesSetCount++;
                    if ($valuesSetCount === $valuesCount) {
                        return true;
                    }
                }
            }
        };
        $walkOptions($this->options);
        return $this;
    }

}
