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
class ElementType
{
    /**
     * 
     * @var string
     */
    public $type = null;

    /**
     * 
     * @var string
     */
    public $name = null;

    /**
     * 
     * @var string
     */
    public $componentName = null;

    /**
     * 
     * @var string
     */
    public $componentFilename = null;

    /**
     * 
     * @var array [['id'=>'...', 'type'=>'bool|int|float|string',]]
     */
    public $properties = [];

    /**
     * 
     * @var boolean
     */
    public $canStyle = false;

    /**
     * 
     * @var boolean
     */
    public $canImportExport = false;

    /**
     * 
     * @var callable function (array $data): void {}
     */
    public $onDelete = null;

    /**
     * 
     * @var callable function (array $data): array {}
     */
    public $onDuplicate = null;

    /**
     * 
     * @var callable function (array $data, callable $add): array {}
     */
    public $onExport = null;

    /**
     * 
     * @var callable function (array $data, ImportContext $context): array {}
     */
    public $onImport = null;

    /**
     * 
     * @var callable function (array $data): array {}
     */
    public $getUploadsSizeItems = null;

    /**
     * 
     * @var callable function (array $data): ?array {}
     */
    public $optimizeData = null;

    /**
     * 
     * @var callable function ($component, array $data) {}
     */
    public $updateComponentFromData = null;

    /**
     * 
     * @var callable function ($component, array $data): array {}
     */
    public $updateDataFromComponent = null;

    /**
     * 
     * @param string $type
     * @param string $componentName
     * @param string $componentFilename
     */
    public function __construct(string $type, string $componentName, string $componentFilename)
    {
        $this->type = $type;
        $this->componentName = $componentName;
        $this->componentFilename = $componentFilename;
    }
}
