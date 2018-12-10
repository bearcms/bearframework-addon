<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearCMS\Internal;

/**
 * @internal
 */
trait ThemesOptionsGroupSchemaTrait
{

    /**
     *
     * @var array 
     */
    private $options = [];

    /**
     * 
     * @param string $id
     * @param string $type
     * @param string $name
     * @param array $details
     * @return self
     */
    public function addOption(string $id, string $type, string $name, array $details = []): self
    {
        $option = new \BearCMS\Themes\Options\OptionSchema();
        $option->id = $id;
        $option->type = $type;
        $option->name = $name;
        $option->details = $details;
        $this->options[] = $option;
        return $this;
    }

    /**
     * 
     * @param string $name
     * @param string $description
     * @return \BearCMS\Themes\Options\GroupSchema
     */
    public function addGroup(string $name, string $description = ''): \BearCMS\Themes\Options\GroupSchema
    {
        $group = new \BearCMS\Themes\Options\GroupSchema();
        $group->name = $name;
        $group->description = $description;
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
            call_user_func($callable, $this, $idPrefix, $parentSelector);
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
            call_user_func($callable, $this);
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
        $this->addOption($id, "cssCode", __("bearcms.themes.options.Custom CSS"));
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getList(): array
    {
        return $this->options;
    }

}
