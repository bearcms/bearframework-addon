<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;

final class Options
{

    static $serverUrl = null;
    static $appSecretKey = null;
    static $language = 'en';
    static $features = ['ALL'];
    static $cookiePrefix = null;
    static $logServerRequests = false;
    static $addonsDir = false;
    static $uiColor = null;
    static $uiTextColor = '#ffffff';
    static $adminPagesPathPrefix = '/admin/';
    static $blogPagesPathPrefix = '/b/';
    static $autoCreateHomePage = true;
    static $defaultEmailSender = null;
    static $maxUploadsSize = null;
    static $useDataCache = false;
    static $dataCachePrefix = null;
    static $defaultThemeID = null;
    static $htmlSandboxUrl = '';
    static $useDefaultUserProfile = true;
    static $whitelabel = false;

    /**
     * 
     * @param array $data
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    static function set(array $data): void
    {

        if (isset($data['serverUrl']) && strlen($data['serverUrl']) > 0) {
            if (isset($data['appSecretKey']) && strlen($data['appSecretKey']) > 0) {
                self::$appSecretKey = $data['appSecretKey'];
            } else {
                throw new \Exception('The appSecretKey is required for bearcms/bearframework-addon');
            }
            self::$serverUrl = $data['serverUrl'];
        }

        $app = App::get();

        if (isset($data['language'])) {
            self::$language = $data['language'];
        }

        if (isset($data['addonsDir'])) {
            $addonsDir = realpath($data['addonsDir']);
            if ($addonsDir === false) {
                throw new \Exception('addonsDir option is not value for bearcms/bearframework-addon');
            }
            self::$addonsDir = $addonsDir;
        }

        if (isset($data['features']) && is_array($data['features']) && !empty($data['features'])) {
            self::$features = $data['features'];
        }

        self::$cookiePrefix = substr(md5(md5($app->request->base) . md5(self::$serverUrl)), 0, 14) . '_bearcms_';

        if (isset($data['logServerRequests']) && $data['logServerRequests'] === true) {
            self::$logServerRequests = true;
        }

        if (isset($data['uiColor'])) {
            self::$uiColor = $data['uiColor'];
        }

        if (isset($data['uiTextColor'])) {
            self::$uiTextColor = $data['uiTextColor'];
        }

        if (isset($data['adminPagesPathPrefix'])) {
            self::$adminPagesPathPrefix = $data['adminPagesPathPrefix'];
        }

        if (isset($data['blogPagesPathPrefix'])) {
            self::$blogPagesPathPrefix = $data['blogPagesPathPrefix'];
        }

        if (isset($data['autoCreateHomePage'])) {
            self::$autoCreateHomePage = $data['autoCreateHomePage'];
        }
        if (isset($data['defaultEmailSender'])) {
            if (is_array($data['defaultEmailSender']) && isset($data['defaultEmailSender']['email'], $data['defaultEmailSender']['name'])) {
                self::$defaultEmailSender = $data['defaultEmailSender'];
            } else {
                throw new \Exception('defaultEmailSender option must be an array containg keys named \'email\' and \'name\' of the sender.');
            }
        }
        if (isset($data['maxUploadsSize'])) {
            self::$maxUploadsSize = (int) $data['maxUploadsSize'];
        }
        if (isset($data['useDataCache'])) {
            self::$useDataCache = (int) $data['useDataCache'] > 0;
        }
        if (isset($data['dataCachePrefix'])) {
            self::$dataCachePrefix = (string) $data['dataCachePrefix'];
        }
        if (isset($data['defaultThemeID'])) {
            self::$defaultThemeID = $data['defaultThemeID'];
        }
        if (isset($data['htmlSandboxUrl'])) {
            self::$htmlSandboxUrl = (string) $data['htmlSandboxUrl'];
        }
        if (isset($data['useDefaultUserProfile'])) {
            self::$useDefaultUserProfile = (int) $data['useDefaultUserProfile'] > 0;
        }
        if (isset($data['whitelabel'])) {
            self::$whitelabel = (int) $data['whitelabel'] > 0;
        }
    }

    static function hasServer(): bool
    {
        return self::$serverUrl !== null;
    }

    static function hasFeature(string $name): bool
    {
        if (substr($name, -1) === '*') {
            $prefix = substr($name, 0, -1);
            foreach (self::$features as $feature) {
                if (strpos($feature, $prefix) === 0) {
                    return true;
                }
            }
        }
        return array_search($name, self::$features) !== false || (sizeof(self::$features) === 1 && self::$features[0] === 'ALL');
    }

}
