<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearFramework\App;

class Files
{

    /**
     * 
     * @param string $filename
     * @return array
     */
    static function getFileData($filename)
    {
        $app = App::$instance;
        $item = $app->data->get(
                [
                    'key' => 'bearcms/files/custom/' . $filename,
                    'result' => ['key', 'metadata']
                ]
        );
        if (isset($item['key'])) {
            $result = [
                'filename' => $filename,
                'published' => (isset($item['metadata.published']) ? (int) $item['metadata.published'] : 0)
            ];
            return $result;
        }
        return false;
    }

}
