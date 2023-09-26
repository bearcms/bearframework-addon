<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearCMS\Internal\Config;
use BearFramework\App;

/**
 * @internal
 * @codeCoverageIgnore
 */
class Uploads
{

    /**
     * 
     * @var array
     */
    static private $cache = [];

    /**
     * 
     * @return integer|null
     */
    static function getMaxUploadsSize(): ?int
    {
        $maxUploadsSize = Config::getVariable('maxUploadsSize');
        if ($maxUploadsSize !== null) {
            return (int)$maxUploadsSize;
        }
        return null;
    }

    /**
     * 
     * @return integer|null
     */
    static function getMaxUploadSize(): ?int
    {
        $maxUploadSize = Config::getVariable('maxUploadSize');
        if ($maxUploadSize !== null) {
            return (int)$maxUploadSize;
        }
        if (array_key_exists('systemMax', self::$cache) === false) {
            $getSystemMaxUploadSize = function () {
                $app = App::get();
                $values = [];
                $value = (int)$app->localization->formatBytes(ini_get('post_max_size'), ['bytes']);
                if ($value > 0) {
                    $values[] = $value;
                }
                $value = (int)$app->localization->formatBytes(ini_get('upload_max_filesize'), ['bytes']);
                if ($value > 0) {
                    $values[] = $value;
                }
                if (!empty($values)) {
                    return min($values);
                }
                return null;
            };
            self::$cache['systemMax'] = $getSystemMaxUploadSize();
        }
        return self::$cache['systemMax'];
    }

    /**
     * 
     * @return integer
     */
    static function getUploadsFreeSpace(): int
    {
        $app = App::get();
        $freeSpace = [];
        $maxUploadsSize = self::getMaxUploadsSize();
        if ($maxUploadsSize !== null) {
            $freeSpace[] = $maxUploadsSize - UploadsSize::getSize();
        }
        $freeSpace[] = $app->data->getFreeSpace();
        return min($freeSpace);
    }
}
