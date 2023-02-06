<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearCMS\Internal\Data as InternalData;
use BearFramework\App;

/**
 * @internal
 * @codeCoverageIgnore
 */
class ElementsSharedStyles
{

    /**
     * 
     * @param string $styleID
     * @return string
     */
    static private function getDataKey(string $styleID): string
    {
        return 'bearcms/elements/style/' . md5($styleID) . '.json';
    }

    /**
     * 
     * @param array $data
     * @return string
     */
    static function encodeData(array $data): string
    {
        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    /**
     * Returns an array (['id' => ..., 'type' => ..., 'style' => ...]) or null if data is invalid
     * @param string $data
     * @return array|null
     */
    static function decodeRawData(string $data): ?array
    {
        $data = json_decode($data, true);
        if (
            is_array($data) &&
            isset($data['id']) && is_string($data['id']) && strlen($data['id']) > 0 &&
            isset($data['type']) && is_string($data['type']) && strlen($data['type']) > 0 &&
            ((isset($data['style']) && is_array($data['style'])) || !isset($data['style']))
        ) {
            if (!isset($data['style'])) {
                $data['style'] = [];
            }
            return $data;
        }
        return null;
    }

    /**
     * 
     * @param string $styleID
     * @return string|null
     */
    static function getRawData(string $styleID): ?string
    {
        return InternalData::getValue(self::getDataKey($styleID));
    }

    /**
     * 
     * @return array
     */
    static function getList(): array
    {
        $result = [];
        $app = App::get();
        $list = $app->data->getList()
            ->filterBy('key', 'bearcms/elements/style/', 'startWith');
        foreach ($list as $item) {
            $result[] = self::decodeRawData($item->value);
        }
        return $result;
    }

    /**
     * 
     * @param string $styleID
     * @return array|null
     */
    static function get(string $styleID): ?array
    {
        $data = self::getRawData($styleID);
        return $data !== null ? self::decodeRawData($data) : null;
    }

    /**
     * 
     * @param string $styleID
     * @param array $data
     * @param string|null $containerID
     * @return void
     */
    static function set(string $styleID, array $data): void
    {
        $app = App::get();
        $app->data->setValue(self::getDataKey($styleID), self::encodeData($data));
    }

    /**
     * 
     * @param string $styleID
     * @return void
     */
    static function delete(string $styleID): void
    {
        $app = App::get();
        $app->data->delete(self::getDataKey($styleID));
    }
}
