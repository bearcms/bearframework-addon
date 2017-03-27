<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;

class Themes
{

    static $list = [];

    static function add(string $id, callable $initializeCallback, callable $applyCallback, array $options = [])
    {
        self::$list[$id] = [$initializeCallback, $applyCallback, $options];
    }

    static function getActiveThemeID(): string
    {
        $app = App::get();
        $data = \BearCMS\Internal\Data::getValue('bearcms/themes/active.json');
        if ($data !== null) {
            $data = json_decode($data, true);
            if (isset($data['id'])) {
                return $data['id'];
            }
        }
        return 'none';
    }

    static function getList(): array
    {
        $list = ['none'];
        $list = array_merge($list, array_keys(\BearCMS\Internal\Themes::$list));
        return $list;
    }

    static function getManifest(string $id): array
    {
        if (isset(\BearCMS\Internal\Themes::$list[$id])) {
            $themeOptions = \BearCMS\Internal\Themes::$list[$id][2];
            if (isset($themeOptions['manifest'])) {
                if (is_array($themeOptions['manifest'])) {
                    return $themeOptions['manifest'];
                } elseif (is_callable($themeOptions['manifest'])) {
                    return call_user_func($themeOptions['manifest']);
                } else {
                    throw new \Exception('');
                }
            }
        }
        return [];
    }

    static function getOptions(string $id): array
    {
        if (isset(\BearCMS\Internal\Themes::$list[$id])) {
            $themeOptions = \BearCMS\Internal\Themes::$list[$id][2];
            if (isset($themeOptions['options'])) {
                if (is_array($themeOptions['options'])) {
                    return $themeOptions['options'];
                } elseif (is_callable($themeOptions['options'])) {
                    return call_user_func($themeOptions['options']);
                } else {
                    throw new \Exception('');
                }
            }
        }
        return [];
    }

}
