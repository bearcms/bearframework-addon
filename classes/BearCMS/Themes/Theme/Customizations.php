<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Themes\Theme;

/**
 * 
 */
class Customizations
{

    /**
     * Options values
     * 
     * @var array 
     */
    private $values = [];

    /**
     *
     * @var string 
     */
    private $html = '';

    /**
     * 
     * @param array $values
     * @param string $html
     */
    public function __construct(array $values, string $html)
    {
        $this->values = $values;
        $this->html = $html;
    }

    /**
     * 
     * @param string $name
     * @return mixed|null
     */
    public function getValue(string $name)
    {
        return isset($this->values[$name]) ? $this->values[$name] : null;
    }

    /**
     * 
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * 
     * @return string
     */
    public function getHTML(): string
    {
        return $this->html;
    }

}
