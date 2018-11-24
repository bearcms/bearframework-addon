<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data2;

use BearFramework\App;

/**
 * Information about the site settings
 */
class Settings
{

    /**
     * Returns an array containing the site settings
     * 
     * @return \BearCMS\DataObject An object containing the site settings
     */
    public function get(): \BearCMS\DataObject
    {
        $app = App::get();
        $data = \BearCMS\Internal\Data::getValue('bearcms/settings.json');
        if ($data !== null) {
            $data = json_decode($data, true);
        } else {
            $data = [
                'title' => 'MY COMPANY',
                'description' => 'The slogan of my company',
                'language' => 'en',
                'allowSearchEngines' => true,
                'externalLinks' => true,
            ];
        }
        if (!isset($data['title'])) {
            $data['title'] = '';
        }
        if (!isset($data['description'])) {
            $data['description'] = '';
        }
        if (!isset($data['keywords'])) {
            $data['keywords'] = '';
        }
        if (!isset($data['language'])) {
            $data['language'] = 'en';
        }
        if (!isset($data['icon'])) {
            $data['icon'] = '';
        }
        if (!isset($data['externalLinks'])) {
            $data['externalLinks'] = false;
        }
        if (!isset($data['allowSearchEngines'])) {
            $data['allowSearchEngines'] = false;
        }
        if (!isset($data['disabled'])) {
            $data['disabled'] = false;
        }
        if (!isset($data['disabledText'])) {
            $data['disabledText'] = '';
        }
        if (!isset($data['enableRSS'])) {
            $data['enableRSS'] = true;
        }
        if (!isset($data['rssType'])) {
            $data['rssType'] = 'fullContent';
        }
        return new \BearCMS\DataObject($data);
    }

}
