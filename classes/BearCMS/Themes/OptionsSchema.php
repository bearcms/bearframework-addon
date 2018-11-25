<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Themes;

class OptionsSchema extends \BearCMS\Themes\OptionsGroupSchema
{

    public function __construct()
    {
        parent::__construct('', '');
    }

    public function toArray(): array
    {
        $result = parent::toArray();
        return isset($result['options']) ? $result['options'] : [];
    }

    public function setDefaultValue(string $id, $value): void
    {
        $this->setDefaultValues([$id => $value]);
    }

    public function setDefaultValues(array $values): void
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
    }

}
