<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Themes;

use BearCMS\Internal;

/**
 * 
 */
class OptionsGroupSchema
{

    protected $name = '';
    protected $description = '';
    protected $options = [];

    public function __construct(string $name, string $description = '')
    {
        $this->name = $name;
        $this->description = $description;
    }

    public function addOption(string $id, string $type, string $name, array $options = []): self
    {
        $this->options[] = array_merge(['id' => $id, 'type' => $type, 'name' => $name], $options);
        return $this;
    }

    public function addGroup(string $name, string $description = ''): self
    {
        $group = new \BearCMS\Themes\OptionsGroupSchema($name, $description);
        $this->options[] = $group;
        return $group;
    }

    public function addElements(string $idPrefix, string $parentSelector): self
    {
        foreach (Internal\Themes::$elementsOptions as $callable) {
            $schema = call_user_func($callable, $idPrefix, $parentSelector);
            if (is_array($schema)) {
                $this->options[] = $schema;
            }
        }
        return $this;
    }

    public function addPages(): self
    {
        foreach (Internal\Themes::$pagesOptions as $callable) {
            $schema = call_user_func($callable);
            if (is_array($schema)) {
                $this->options[] = $schema;
            }
        }
        return $this;
    }

    public function addCustomCSS(string $id = 'customCSS'): self
    {
        $this->options[] = [
            "id" => $id,
            "type" => "cssCode",
            "name" => __("bearcms.themes.options.Custom CSS")
        ];
        return $this;
    }

    public function toArray(): array
    {
        $result = [
            "type" => "group",
            "name" => $this->name
        ];
        if (strlen($this->description) > 0) {
            $result['description'] = $this->description;
        }
        $result['options'] = [];
        foreach ($this->options as $option) {
            $result['options'][] = is_object($option) && method_exists($option, 'toArray') ? $option->toArray() : (is_array($option) ? $option : (array) $option);
        }
        return $result;
    }

    public function getCSSRules()
    {
        $cssRules = [];
        $walkOptions = function($options) use (&$cssRules, &$walkOptions) {
            foreach ($options as $option) {
                if (isset($option['id'])) {
                    if (isset($option['cssOutput'])) {
                        foreach ($option['cssOutput'] as $outputDefinition) {
                            if (is_array($outputDefinition)) {
                                if (isset($outputDefinition[0], $outputDefinition[1], $outputDefinition[2]) && $outputDefinition[0] === 'rule') {
                                    $selector = $outputDefinition[1];
                                    if (!isset($cssRules[$selector])) {
                                        $cssRules[$selector] = '';
                                    }
                                    $cssRules[$selector] .= $outputDefinition[2];
                                }
                            }
                        }
                    }
                }
                if (isset($option['options'])) {
                    $walkOptions($option['options']);
                }
            }
        };
        $walkOptions($this->options);
        return $cssRules;
    }

}
