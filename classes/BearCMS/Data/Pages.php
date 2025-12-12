<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data;

use BearCMS\Internal;
use BearCMS\Internal\Config;
use BearCMS\Internal\Data\Pages as InternalDataPages;

/**
 * 
 */
class Pages
{

    /**
     * 
     * @param string $id
     * @return \BearCMS\Data\Pages\Page|null
     */
    public function get(string $id): ?\BearCMS\Data\Pages\Page
    {
        $data = Internal\Data\Pages::get($id);
        if ($data !== null) {
            return \BearCMS\Data\Pages\Page::fromArray($data);
        }
        if ($id === 'home' && Config::$autoCreateHomePage) {
            return InternalDataPages::getDefaultHomePage();
        }
        return null;
    }

    /**
     * 
     * @param string $path
     * @return \BearCMS\Data\Pages\Page|null
     */
    public function getByPath(string $path): ?\BearCMS\Data\Pages\Page
    {
        $pathsList = Internal\Data\Pages::getPathsList();
        $pageID = array_search($path, $pathsList);
        if ($pageID !== false) {
            return $this->get($pageID);
        }
        if ($path === '/' && Config::$autoCreateHomePage) {
            return InternalDataPages::getDefaultHomePage();
        }
        return null;
    }

    /**
     * 
     * @return \BearFramework\Models\ModelsList
     */
    public function getList(): \BearFramework\Models\ModelsList
    {
        return new \BearFramework\Models\ModelsList(InternalDataPages::getRawPagesList());
    }
}
