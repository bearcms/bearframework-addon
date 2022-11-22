<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\ImportExport;

/**
 * @internal
 * @codeCoverageIgnore
 */
class ItemHandler
{

    /**
     * 
     * @var callable
     */
    private $export = null;

    /**
     * 
     * @var callable
     */
    private $import = null;

    /**
     * 
     * @param callable $export
     * @param callable $import
     */
    public function __construct(callable $export, callable $import)
    {
        $this->export = $export;
        $this->import = $import;
    }

    /**
     * 
     * @param array $args
     * @param callable $add Function to add an item to the exported file
     * @return void
     */
    public function export(array $args, callable $add): void
    {
        call_user_func($this->export, $args, $add);
    }

    /**
     * 
     * @param array $args
     * @param ImportContext $context
     * @param array $options
     * @return mixed
     */
    public function import(array $args, ImportContext $context, array $options = [])
    {
        return call_user_func($this->import, $args, $context, $options);
    }
}
