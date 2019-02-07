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
            $context = $app->contexts->get(__FILE__);
            self::$contextDir = $context->dir;
        }
        $name = $options['componentSrc'];
        $app->components->addAlias($name, 'file:' . self::$contextDir . '/components/bearcmsElement.php');
        $app->components->addTag($name, 'file:' . self::$contextDir . '/components/bearcmsElement.php');
        Internal\ElementsHelper::$elementsTypesCodes[$name] = $typeCode;
        Internal\ElementsHelper::$elementsTypesFilenames[$name] = $options['componentFilename'];
        Internal\ElementsHelper::$elementsTypesOptions[$name] = $options;
    }

}
