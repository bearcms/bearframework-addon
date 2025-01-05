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

    /**
     * 
     * @param integer $preferedSize
     * @return string|null
     */
    static function getIconForSize(?int $preferedSize = null): ?string
    {
        $app = App::get();
        $settings = $app->bearCMS->data->settings->get();
        if (!empty($settings->icons)) {
            $sizes = [];
            foreach ($settings->icons as $icon) {
                $filename = $icon['filename'];
                if (isset($icon['width'], $icon['height'])) {
                    $sizes[$filename] = [$icon['width'], $icon['height']];
                } else {
                    $details = $app->assets->getDetails($filename, ['width', 'height']);
                    $sizes[$filename] = [$details['width'], $details['height']];
                }
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

    /**
     * Improves performance if the details are saved in the settings
     *
     * @param boolean $preview
     * @return array
     */
    static function updateIconsDetails(bool $preview = false): array
    {
        $result = [];
        $app = App::get();
        $settings = $app->bearCMS->data->settings->get();
        $hasChange = false;
        if (!empty($settings->icons)) {
            $oldIcons = $settings->icons;
            foreach ($settings->icons as $index => $icon) {
                if (!isset($icon['width']) || !isset($icon['height'])) {
                    $details = $app->assets->getDetails($icon['filename'], ['width', 'height']);
                    $icon['width'] = $details['width'] !== null ? $details['width'] : 0;
                    $icon['height'] = $details['height'] !== null ? $details['height'] : 0;
                    $settings->icons[$index] = $icon;
                    $hasChange = true;
                }
            }
        }
        if ($hasChange) {
            $result['old'] = $oldIcons;
            $result['new'] = $settings->icons;
            if (!$preview) {
                $app->data->duplicate('bearcms/settings.json', '.recyclebin/bearcms/update-' . str_replace('.', '-', microtime(true)) . '-settings.json');
                $app->bearCMS->data->settings->set($settings);
            }
        }
        return $result;
    }

    /**
     * 
     * @param string $text
     * @param string $language
     * @return string
     */
    static function applyPageTitleFormat(string $text, string $language = ''): string
    {
        $app = App::get();
        $settings = $app->bearCMS->data->settings->get();
        $pageTitleFormat = (string)$settings->getPageTitleFormat($language);
        if (strlen($pageTitleFormat) > 0) {
            if (strpos($pageTitleFormat, '{title}') !== false) {
                return str_replace('{title}', $text, $pageTitleFormat);
            }
            return $text . ' | ' . $pageTitleFormat;
        }
        return $text;
    }
}
