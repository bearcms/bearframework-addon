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
class ComponentUtilities
{

    static function createComponentFragment(string $id, string $content): string
    {
        return '<component-fragment id="' . $id . '" src="data:base64,' . base64_encode($content) . '" />';
    }
}
