<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;

class Options
{

    static $serverUrl = null;
    static $language = 'en';
    static $features = ['all'];
    static $cookiePrefix = null;
    static $logServerRequestsData = false;

    /**
     * 
     * @param array $data
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    static function set($data)
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException('');
        }
        if (!isset($data['serverUrl'])) {
            throw new \Exception('serverUrl option is not set for bearcms/bearframework-addon');
        }

        $app = App::$instance;

        self::$serverUrl = $data['serverUrl'];

        if (isset($data['language'])) {
            self::$language = $data['language'];
        }

        if (isset($data['features'])) {
            $features = [];
            $walkFeatures = function($list, $prefix = '') use (&$walkFeatures, &$features) {
                if (is_array($list)) {
                    foreach ($list as $key => $value) {
                        if ($value === true) {
                            $features[] = strtolower($prefix . $key);
                            $features[] = strtolower($prefix . $key) . '.all';
                        } elseif (is_array($value)) {
                            $features[] = strtolower($prefix . $key);
                            $walkFeatures($value, $prefix . $key . '.');
                        }
                    }
                }
            };
            $walkFeatures($data['features']);
            if (!empty($features)) {
                self::$features = $features;
            }
        }

        self::$cookiePrefix = substr(md5(md5($app->request->base) . md5(self::$serverUrl)), 0, 14) . '_bearcms_';

        if (isset($data['logServerRequestsData']) && $data['logServerRequestsData']) {
            self::$logServerRequestsData = true;
        }
    }

    static function hasFeature($name)
    {
        return array_search($name, self::$features) !== false || (sizeof(self::$features) === 1 && self::$features[0] === 'all');
    }

}
