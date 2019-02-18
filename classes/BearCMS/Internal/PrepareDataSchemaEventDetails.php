<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

/**
 * @property \BearCMS\Internal\DataSchema $dataSchema
 * @internal
 */
class PrepareDataSchemaEventDetails
{

    use \IvoPetkov\DataObjectTrait;

    /**
     * 
     * @param \BearCMS\Internal\DataSchema $dataSchema
     */
    public function __construct(\BearCMS\Internal\DataSchema $dataSchema)
    {
        $this
                ->defineProperty('dataSchema', [
                    'type' => '\BearCMS\Internal\DataSchema'
                ])
        ;
        $this->dataSchema = $dataSchema;
    }

}
