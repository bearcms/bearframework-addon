<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data;

use BearCMS\Internal;

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
        return \BearCMS\Data\Settings\Settings::fromArray($data);
    }
}
