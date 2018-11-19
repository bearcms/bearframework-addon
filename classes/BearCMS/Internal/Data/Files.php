<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearFramework\App;

final class Files
{

    /**
     * 
     * @param string $filename
     * @return array
     */
    static function getFileData(string $filename): ?array
    {
        $app = App::get();
        $item = $app->data->get('bearcms/files/custom/' . $filename);
        if ($item !== null) {
            $result = [
                'filename' => $filename,
                'published' => (isset($item->metadata->published) ? (int) $item->metadata->published : 0),
                'name' => (isset($item->metadata->name) && strlen($item->metadata->name) > 0 ? $item->metadata->name : $filename)
            ];
            return $result;
        }
        return null;
    }

}
