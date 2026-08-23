<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearFramework\App;

/**
 * @internal
 * @codeCoverageIgnore
 */
class Files
{

    /**
     * 
     * @param string $filename
     * @return array
     */
    static function getFileData(string $filename): ?array
    {
        $app = App::get();
        $dataKey = 'bearcms/files/custom/' . $filename;
        if ($app->data->validate($dataKey)) {
            $item = $app->data->get($dataKey);
            if ($item !== null) {
                $fullFilename = $app->data->getFilename($dataKey);
                $metaData = $item->metadata;
                $result = [
                    'filename' => $filename,
                    'published' => isset($metaData['published']) ? (int) $metaData['published'] > 0 : false,
                    'name' => isset($metaData['name']) && strlen($metaData['name']) > 0 ? $metaData['name'] : $filename,
                    'size' => filesize($fullFilename),
                    'dateCreated' => isset($metaData['dateCreated']) ? (string) $metaData['dateCreated'] : '',
                    'note' => isset($metaData['note']) ? (string) $metaData['note'] : '',
                    'categories' => isset($metaData['categories']) ? json_decode($metaData['categories'], true) : [],
                ];
                return $result;
            }
        }
        return null;
    }

    /**
     * 
     * @param string $filename
     * @param string $name
     * @return void
     */
    static function setFileName(string $filename, string $name)
    {
        $app = App::get();
        $app->data->setMetadata('bearcms/files/custom/' . $filename, 'name', $name);
    }

    /**
     * 
     * @param string $filename
     * @param boolean $published
     * @return void
     */
    static function setFilePublished(string $filename, bool $published)
    {
        $app = App::get();
        $app->data->setMetadata('bearcms/files/custom/' . $filename, 'published', $published ? '1' : '0');
    }

    /**
     * 
     * @param string $filename
     * @param string $dateCreated
     * @return void
     */
    static function setFileDateCreated(string $filename, string $dateCreated)
    {
        $app = App::get();
        $app->data->setMetadata('bearcms/files/custom/' . $filename, 'dateCreated', $dateCreated);
    }

    /**
     * 
     * @param string $filename
     * @param string $note
     * @return void
     */
    static function setFileNote(string $filename, string $note)
    {
        $app = App::get();
        $app->data->setMetadata('bearcms/files/custom/' . $filename, 'note', $note);
    }

    /**
     * 
     * @param string $filename
     * @param array $categories
     * @return void
     */
    static function setFileCategories(string $filename, array $categories)
    {
        $app = App::get();
        $app->data->setMetadata('bearcms/files/custom/' . $filename, 'categories', json_encode($categories));
    }

    /**
     * 
     * @return array
     */
    static function getList(): array
    {
        $app = App::get();
        $prefix = 'bearcms/files/custom/';
        $list = $app->data->getList()
            ->filterBy('key', $prefix, 'startWith')
            ->sliceProperties(['key', 'metadata']);
        $result = [];
        foreach ($list as $item) {
            $key = $item->key;
            $filename = str_replace($prefix, '', $key);
            $result[] = self::getFileData($filename);
        }
        return $result;
    }
}
