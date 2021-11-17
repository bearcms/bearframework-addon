<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data;

use BearCMS\Internal;
use BearFramework\App;

/**
 * 
 */
class Settings
{

    /**
     * 
     * @return \BearCMS\Data\Settings\Settings
     */
    public function get(): \BearCMS\Data\Settings\Settings
    {
        $cacheKey = 'settings';
        if (!isset(Internal\Data::$cache[$cacheKey])) {
            $data = Internal\Data::getValue('bearcms/settings.json');
            if ($data !== null) {
                $data = json_decode($data, true);
            } else {
                $data = [
                    'title' => 'MY COMPANY',
                    'description' => 'The slogan of my company',
                    'languages' => ['en'],
                    'allowSearchEngines' => true,
                    'allowCommentsInBlogPosts' => true,
                    'showRelatedBlogPosts' => true,
                    'externalLinks' => true
                ];
            }
            Internal\Data::$cache[$cacheKey] = \BearCMS\Data\Settings\Settings::fromArray($data);
        }
        return Internal\Data::$cache[$cacheKey];
    }

    /**
     * 
     * @param \BearCMS\Data\Settings\Settings $settings
     * @return void
     */
    public function set(\BearCMS\Data\Settings\Settings $settings): void
    {
        $app = App::get();
        $app->data->setValue('bearcms/settings.json', json_encode($settings->toArray()));
        $cacheKey = 'settings';
        if (isset(Internal\Data::$cache[$cacheKey])) {
            unset(Internal\Data::$cache[$cacheKey]);
        }
    }
}
