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
class Options
{

    /**
     * Options data
     * 
     * @var array 
     */
    private $data = [];

    /**
     *
     * @var string 
     */
    private $html = '';

    /**
     * 
     * @param array $data
     * @param string $html
     */
    public function __construct(array $data, string $html)
    {
        $this->data = $data;
        $this->html = $html;
    }

    /**
     * 
     * @param string $name
     * @return mixed|null
     */
    public function getValue(string $name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * 
     * @return array
     */
    function toArray(): array
    {
        return $this->data;
    }

    /**
     * 
     * @return string
     */
    function toHTML(): string
    {
        return $this->html;
    }

}
