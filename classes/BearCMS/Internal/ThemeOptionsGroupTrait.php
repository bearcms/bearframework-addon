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
     * @param array $details Available values: value, defaultValue (used when the value is null, in OPTIONS_CONTEXT_ELEMENT must match the default css code and will not be applied), cssTypes, cssOptions, cssOutput
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
     * @param array $details
     * @return \BearCMS\Themes\Theme\Options\Group
     */
    public function addElementsGroup(string $idPrefix, string $parentSelector, array $details = []): \BearCMS\Themes\Theme\Options\Group
    {
        $group = $this->addGroup(__('bearcms.themes.options.Elements'));
        $group->addElements($idPrefix, $parentSelector, $details);
        return $group;
    }

    /**
     * 
     * @param string $idPrefix
     * @param string $parentSelector
     * @param array $details
     * @return self
     */
    public function addElements(string $idPrefix, string $parentSelector, array $details = []): self
    {
        foreach (Internal\Themes::$elementsOptions as $type => $callable) {
            if (array_search($type, ['columns', 'floatingBox', 'flexibleBox']) !== false) {
                continue;
            }
            if (is_array($callable)) {
                $callable = $callable[1];
            }
            call_user_func($callable, $this, $idPrefix, $parentSelector, Internal\Themes::OPTIONS_CONTEXT_THEME, $details);
            $lastOption = $this->options[count($this->options) - 1];
            if ($lastOption instanceof \BearCMS\Themes\Theme\Options\Group) { // expect the last one to be the element's group
                $lastOption->details['internalElementType'] = $type;
            }
        }
        return $this;
    }

    /**
     * 
     * @param array $details
     * @return \BearCMS\Themes\Theme\Options\Group
     */
    public function addPagesGroup(array $details = []): \BearCMS\Themes\Theme\Options\Group
    {
        $group = $this->addGroup(__('bearcms.themes.options.Pages'));
        $group->addPages($details);
        return $group;
    }

    /**
     * 
     * @param array $details
     * @return self
     */
    public function addPages(array $details = []): self
    {
        foreach (Internal\Themes::$pagesOptions as $callable) {
            if (is_array($callable)) {
                $callable = $callable[1];
            }
            call_user_func($callable, $this, $details);
        }
        return $this;
    }

    /**
     * 
     * @param string $id
     * @return self
     */
    public function addVisibility(string $id, string $cssSelector, array $details = []): self
    {
        $states = isset($details['states']) ? $details['states'] : [
            ["type" => "size"],
            ["type" => "screenSize"],
            ["type" => "pageType"],
            ["type" => "tags"],
        ];
        $this->addOption($id, "visibility", '', [
            "states" => $states,
            "cssOutput" => [
                ["selector", $cssSelector, '--css-to-attribute-data-bearcms-visibility:{cssPropertyValue(type)};z-index:{cssPropertyValue(layer)};top:{cssPropertyValue(top)};left:{cssPropertyValue(left)};bottom:{cssPropertyValue(bottom)};right:{cssPropertyValue(right)};transition:{cssPropertyTransition(top,left,bottom,right)};'],
            ],
            "onHighlight" => [['cssSelector', $cssSelector]],
            "defaultValue" => (isset($details['defaultValue']) ? $details['defaultValue'] : '')
        ]);
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
