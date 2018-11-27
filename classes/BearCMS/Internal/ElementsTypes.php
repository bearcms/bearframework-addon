<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;
use BearCMS\Internal;
use IvoPetkov\HTML5DOMDocument;

/**
 * @internal
 */
class ElementsTypes
{

    static private $contextDir = null;

    /**
     * 
     * @param string $typeCode
     * @param array $options
     * @return void
     */
    public static function add(string $typeCode, array $options = []): void
    {
        $app = App::get();
        if (self::$contextDir === null) {
            $context = $app->context->get(__FILE__);
            self::$contextDir = $context->dir;
        }
        $app->components->addAlias($options['componentSrc'], 'file:' . self::$contextDir . '/components/bearcmsElement.php');
        Internal\ElementsHelper::$elementsTypesCodes[$options['componentSrc']] = $typeCode;
        Internal\ElementsHelper::$elementsTypesFilenames[$options['componentSrc']] = $options['componentFilename'];
        Internal\ElementsHelper::$elementsTypesOptions[$options['componentSrc']] = $options;
    }

}
