<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;
use BearCMS\Internal\ElementsHelper;

/**
 * Contains information about the available elements types
 */
class ElementsTypes
{

    private $contextDir = null;

    function __construct()
    {
        $app = App::get();
        $context = $app->context->get(__FILE__);
        $this->contextDir = $context->dir;
    }

    public function add(string $typeCode, array $options = []): self
    {
        $app = App::get();
        $app->components->addAlias($options['componentSrc'], 'file:' . $this->contextDir . '/components/bearcmsElement.php');
        ElementsHelper::$elementsTypesCodes[$options['componentSrc']] = $typeCode;
        ElementsHelper::$elementsTypesFilenames[$options['componentSrc']] = $options['componentFilename'];
        ElementsHelper::$elementsTypesOptions[$options['componentSrc']] = $options;
        return $this;
    }

}
