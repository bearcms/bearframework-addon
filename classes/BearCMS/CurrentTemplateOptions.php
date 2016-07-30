<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS;

class CurrentTemplateOptions implements \ArrayAccess, \Iterator
{

    private $data = array();

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function offsetSet($offset, $value)
    {
        throw new \Exception('Cannot modify template options');
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset)
    {
        throw new \Exception('Cannot modify template options');
    }

    public function offsetGet($offset)
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    function rewind()
    {
        reset($this->data);
    }

    function current()
    {
        return current($this->data);
    }

    function key()
    {
        return key($this->data);
    }

    function next()
    {
        next($this->data);
    }

    function valid()
    {
        return isset($this->data[key($this->data)]);
    }

}
