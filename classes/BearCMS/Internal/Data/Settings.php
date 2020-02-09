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
class Settings
{

    static function getIconForSize($preferedSize = null): ?string
    {
        $app = App::get();
        $settings = $app->bearCMS->data->settings->get();
        if (!empty($settings->icons)) {
            $sizes = [];
            foreach ($settings->icons as $icon) {
                $filename = $icon['filename'];
                $details = $app->assets->getDetails($filename, ['width', 'height']);
                $sizes[$filename] = [$details['width'], $details['height']];
            }
            $list = [];
            foreach ($sizes as $filename => $size) {
                $list[$filename] = $size[0] > $size[1] ? $size[1] : $size[0];
            }
            asort($list);
            foreach ($list as $filename => $size) {
                if ($size >= $preferedSize) {
                    return $filename;
                }
            }
            end($list);
            return key($list);
        }
        return null;
    }
}
