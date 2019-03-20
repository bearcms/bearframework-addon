<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

/**
 * @internal
 * @codeCoverageIgnore
 */
class DataSchema
{

    /**
     * The data schema id
     * 
     * @var string 
     */
    public $id = null;

    /**
     * Contains information about the schema fields
     * 
     * @var array 
     */
    public $fields = [];

    /**
     * Creates a new data schema object
     * 
     * @param string $id The data schema id
     */
    function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Add field definition to the data schema
     * 
     * @param array $field
     */
    function addField(array $field)
    {
        $this->fields[] = $field;
    }

}
