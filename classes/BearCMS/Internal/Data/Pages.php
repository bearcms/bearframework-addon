<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearCMS\Internal;
use BearCMS\Internal\ElementsHelper;
use BearFramework\App;

/**
 * @internal
 * @codeCoverageIgnore
 */
class Pages
{

    /**
     * 
     * @param string $status all or published
     * @return array
     */
    static function getPathsList(string $status = 'all'): array
    {
        $list = Internal\Data::getList('bearcms/pages/page/');
        $result = [];
        foreach ($list as $value) {
            $pageData = json_decode($value, true);
            if (
                is_array($pageData) &&
                isset($pageData['id']) &&
                isset($pageData['path']) &&
                isset($pageData['status']) &&
                is_string($pageData['id']) &&
                is_string($pageData['path']) &&
                is_string($pageData['status'])
            ) {
                if ($status !== 'all' && $status !== $pageData['status']) {
                    continue;
                }
                $result[$pageData['id']] = $pageData['path'];
            }
        }
        return $result;
    }

    static function getDataKey(string $id)
    {
        return 'bearcms/pages/page/' . md5($id) . '.json';
    }

    static function getLastModifiedDetails(string $pageID)
    {
        $app = App::get();
        $details = ElementsHelper::getLastModifiedDetails('bearcms-page-' . $pageID);
        $details['dataKeys'][] = self::getDataKey($pageID);
        $details['dataKeys'][] = 'bearcms/settings.json';
        $page = $app->bearCMS->data->pages->get($pageID);
        if ($page !== null) {
            $details['dates'][] = $page->lastChangeTime;
        }
        return $details;
    }
}
