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
 * @codeCoverageIgnore
 */
trait ThemeOptionsGroupTrait
{

    /**
     *
     * @var array 
     */
    private $options = [];

    /**
     * 
     * @param \BearCMS\Themes\Theme\Options\Option|\BearCMS\Themes\Theme\Options\Group $optionOrGroup
     * @return self
     */
    public function add($optionOrGroup): self
    {
        $this->options[] = $optionOrGroup;
        return $this;
    }

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
        $option = new \BearCMS\Themes\Theme\Options\Option();
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
     * @return \BearCMS\Themes\Theme\Options\Group
     */
    public function addGroup(string $name, string $description = '', array $details = []): \BearCMS\Themes\Theme\Options\Group
    {
        $group = new \BearCMS\Themes\Theme\Options\Group();
        $group->name = $name;
        $group->description = $description;
        $group->details = $details;
        $this->options[] = $group;
        return $group;
    }

    /**
     * 
     * @param string $idPrefix
     * @param string $parentSelector
     * @return \BearCMS\Themes\Theme\Options\Group
     */
    public function addElementsGroup(string $idPrefix, string $parentSelector): \BearCMS\Themes\Theme\Options\Group
    {
        $group = $this->addGroup(__('bearcms.themes.options.Elements'));
        $group->addElements($idPrefix, $parentSelector);
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
        foreach (Internal\Themes::$elementsOptions as $type => $callable) {
            if ($type === 'columns' || $type === 'floatingBox' || $type === 'flexibleBox') {
                continue;
            }
            if (is_array($callable)) {
                $callable = $callable[1];
            }
            call_user_func($callable, $this, $idPrefix, $parentSelector, Internal\Themes::OPTIONS_CONTEXT_THEME);
        }
        return $this;
    }

    /**
     * 
     * @return \BearCMS\Themes\Theme\Options\Group
     */
    public function addPagesGroup(): \BearCMS\Themes\Theme\Options\Group
    {
        $group = $this->addGroup(__('bearcms.themes.options.Pages'));
        $group->addPages();
        return $group;
    }

    /**
     * 
     * @return self
     */
    public function addPages(): self
    {
        foreach (Internal\Themes::$pagesOptions as $callable) {
            if (is_array($callable)) {
                $callable = $callable[1];
            }
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
        return $this->options; // todo clone
    }
}
