<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data2;

use BearFramework\App;

/**
 * Information about the addons managed by Bear CMS
 */
class Addons
{

    private function makeAddonFromRawData($rawData): \BearCMS\Internal\Data2\Addon
    {
        return new \BearCMS\Internal\Data2\Addon(json_decode($rawData, true));
    }

    /**
     * Retrieves information about the addon specified
     * 
     * @param string $id The addon ID
     * @return \BearCMS\DataObject|null The addon data or null if addon not found
     * @throws \InvalidArgumentException
     */
    public function get(string $id)
    {
        $app = App::get();
        $data = $app->data->getValue('bearcms/addons/addon/' . md5($id) . '.json');
        if ($data !== null) {
            return $this->makeAddonFromRawData($data);
        }
        return null;
    }

    /**
     * Retrieves a list of all addons
     * 
     * @return \BearCMS\DataList List containing all addons data
     */
    public function getList()
    {
        $app = App::get();
        $list = $app->data->getList()
                ->filterBy('key', 'bearcms/addons/addon/', 'startWith');
        $result = [];
        foreach ($list as $item) {
            $result[] = $this->makeAddonFromRawData($item->value);
        }
        return new \BearCMS\DataList($result);
    }

}
