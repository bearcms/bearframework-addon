<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Themes;

class OptionsDefinition extends \BearCMS\Themes\OptionsDefinitionGroup
{

    public function __construct()
    {
        parent::__construct('', '');
    }

    public function toArray()
    {
        $result = parent::toArray();
        return isset($result['options']) ? $result['options'] : [];
    }

    public function setDefaultValue(string $id, $value)
    {
        $this->setDefaultValues([$id => $value]);
    }

    public function setDefaultValues(array $values)
    {
        $valuesSetCount = 0;
        $valuesCount = sizeof($values);
        $walkOptions = function(&$options) use (&$walkOptions, &$valuesSetCount, $valuesCount, $values) {
            foreach ($options as $i => $option) {
                if ($option instanceof \BearCMS\Themes\OptionsDefinitionGroup) {
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
    }

}
