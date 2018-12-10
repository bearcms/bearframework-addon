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

    /**
     * 
     * @param string $name
     * @param string $description
     */
    public function __construct(string $name, string $description = '')
    {
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * 
     * @param string $id
     * @param string $type
     * @param string $name
     * @param array $options
     * @return self
     */
    public function addOption(string $id, string $type, string $name, array $options = []): self
    {
        $this->options[] = array_merge(['id' => $id, 'type' => $type, 'name' => $name], $options);
        return $this;
    }

    /**
     * 
     * @param string $name
     * @param string $description
     * @return \BearCMS\Themes\OptionsGroupSchema
     */
    public function addGroup(string $name, string $description = ''): \BearCMS\Themes\OptionsGroupSchema
    {
        $group = new \BearCMS\Themes\OptionsGroupSchema($name, $description);
        $this->options[] = $group;
        return $group;
    }

    /**
     * 
     * @param string $idPrefix
     * @param string $parentSelector
     * @return self
     */
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

    /**
     * 
     * @return self
     */
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

    /**
     * 
     * @param string $id
     * @return self
     */
    public function addCustomCSS(string $id = 'customCSS'): self
    {
        $this->options[] = [
            "id" => $id,
            "type" => "cssCode",
            "name" => __("bearcms.themes.options.Custom CSS")
        ];
        return $this;
    }

    /**
     * 
     * @return array
     */
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

}
