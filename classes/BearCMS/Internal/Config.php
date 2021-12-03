<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;

/**
 * @internal
 * @codeCoverageIgnore
 */
class Config
{

    static $initialized = false;
    static $serverUrl = null;
    static $appSecretKey = null;
    static $language = 'en';
    static $features = ['ALL'];
    static $cookiePrefix = null;
    static $logServerRequests = false;
    static $uiColor = null;
    static $uiTextColor = null;
    static $adminPagesPathPrefix = '/admin/';
    static $blogPagesPathPrefix = '/b/';
    static $autoCreateHomePage = true;
    static $maxUploadsSize = null;
    static $maxUploadSize = null;
    static $defaultThemeID = null;
    static $htmlSandboxUrl = null;
    static $useDefaultUserProfile = true;
    static $whitelabel = false;
    static $addonManager = null;
    static $configManager = null;
    static $addDefaultThemes = true;
    static $appSpecificServerData = [];
    static $videoPrivateEmbed = false;
    static $htmlAllowDefaultMode = true;
    static $allowRenderGlobalHTML = true;
    static $videoEmbedConfig = [];
    static $elementsLazyLoadingOffset = 70;

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

        if (isset($data['features']) && is_array($data['features']) && !empty($data['features'])) {
            self::$features = $data['features'];
        }

        self::$cookiePrefix = substr(md5(md5((string)$app->request->base) . md5((string)self::$serverUrl)), 0, 14) . '_bearcms_';

        self::$logServerRequests = isset($data['logServerRequests']) && $data['logServerRequests'] === true;

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

        if (isset($data['maxUploadsSize'])) {
            self::$maxUploadsSize = (int) $data['maxUploadsSize'];
        }

        if (isset($data['maxUploadSize'])) {
            self::$maxUploadSize = (int) $data['maxUploadSize'];
        } else {
            self::$maxUploadSize = function () {
                $sizeToBytes = function ($size) {
                    $suffix = strtolower(substr($size, -1));
                    if (!in_array($suffix, ['t', 'g', 'm', 'k'])) {
                        return (int) $size;
                    }
                    $value = (int) substr($size, 0, -1);
                    switch ($suffix) {
                        case 't':
                            return $value * 1024 * 1024 * 1024 * 1024;
                        case 'g':
                            return $value * 1024 * 1024 * 1024;
                        case 'm':
                            return $value * 1024 * 1024;
                        case 'k':
                            return $value * 1024;
                    }
                };
                $values = [];
                $value = $sizeToBytes(ini_get('post_max_size'));
                if ($value > 0) {
                    $values[] = $value;
                }
                $value = $sizeToBytes(ini_get('upload_max_filesize'));
                if ($value > 0) {
                    $values[] = $value;
                }
                if (!empty($values)) {
                    return min($values);
                }
                return null;
            };
        }
        if (isset($data['defaultThemeID'])) {
            self::$defaultThemeID = $data['defaultThemeID'];
        }
        if (isset($data['htmlSandboxUrl'])) {
            self::$htmlSandboxUrl = $data['htmlSandboxUrl'];
        }
        if (isset($data['useDefaultUserProfile'])) {
            self::$useDefaultUserProfile = (int) $data['useDefaultUserProfile'] > 0;
        }
        if (isset($data['whitelabel'])) {
            self::$whitelabel = (int) $data['whitelabel'] > 0;
        }
        if (isset($data['internalAddonManager'])) {
            self::$addonManager = $data['internalAddonManager'];
        } else {
            $index = array_search('ADDONS', self::$features);
            if ($index !== false) {
                unset(self::$features[$index]);
            }
        }
        if (isset($data['internalConfigManager'])) {
            self::$configManager = $data['internalConfigManager'];
        }
        if (isset($data['addDefaultThemes'])) {
            self::$addDefaultThemes = (int) $data['addDefaultThemes'];
        }
        if (isset($data['internalAppSpecificServerData'])) {
            if (!is_array($data['internalAppSpecificServerData'])) {
                throw new \Exception('The internalAppSpecificServerData value must be of type array!');
            }
            foreach ($data['internalAppSpecificServerData'] as $key => $value) {
                self::$appSpecificServerData[$key] = $value;
            }
        }
        if (isset($data['internalVideoPrivateEmbed'])) {
            self::$videoPrivateEmbed = (int) $data['internalVideoPrivateEmbed'];
        }
        if (isset($data['internalHTMLAllowDefaultMode'])) {
            self::$htmlAllowDefaultMode = (int) $data['internalHTMLAllowDefaultMode'];
        }
        if (isset($data['internalAllowRenderGlobalHTML'])) {
            self::$allowRenderGlobalHTML = (int) $data['internalAllowRenderGlobalHTML'];
        }
        if (isset($data['videoEmbedConfig'])) {
            self::$videoEmbedConfig = $data['videoEmbedConfig'];
        }
        if (isset($data['elementsLazyLoadingOffset'])) {
            self::$elementsLazyLoadingOffset = $data['elementsLazyLoadingOffset'];
        }
    }

    /**
     * 
     * @return bool
     */
    static function hasServer(): bool
    {
        return self::$serverUrl !== null;
    }

    /**
     * 
     * @param string $name
     * @return bool
     */
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

    /**
     * 
     * @return object|null
     */
    static function getAddonManager()
    {
        if (is_callable(self::$addonManager)) {
            $object = call_user_func(self::$addonManager);
            if (method_exists($object, 'addAddon') && method_exists($object, 'removeAddon')) {
                return $object;
            }
        }
        return null;
    }

    /**
     * 
     * @return object|null
     */
    static function getConfigManager()
    {
        if (is_callable(self::$configManager)) {
            $object = call_user_func(self::$configManager);
            if (method_exists($object, 'setConfigValue')) {
                return $object;
            }
        }
        return null;
    }

    /**
     * 
     * @return string|null
     */
    static function getHashedAppSecretKey(): ?string
    {
        if (self::$appSecretKey !== null && strlen(self::$appSecretKey) > 0) {
            $parts = explode('-', self::$appSecretKey, 2);
            if (sizeof($parts) === 2) {
                return strtoupper('sha256-' . $parts[0] . '-' . hash('sha256', $parts[1]));
            }
        }
        return null;
    }

    /**
     * 
     * @return string|null
     */
    static function getHTMLSandboxURL(): ?string
    {
        if (self::$htmlSandboxUrl !== null) {
            if (is_callable(self::$htmlSandboxUrl)) {
                return (string) call_user_func(self::$htmlSandboxUrl);
            }
            return (string) self::$htmlSandboxUrl;
        }
        return null;
    }
}
