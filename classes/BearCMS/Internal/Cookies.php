<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;

class Cookies
{

    const TYPE_SERVER = 1;
    const TYPE_CLIENT = 2;

    /**
     *
     * @var array 
     */
    private static $pendingUpdate = [];

    /**
     * 
     * @param int $type
     * @return array
     * @throws \Exception
     */
    static function getList($type)
    {
        $app = App::$instance;
        if ($type !== self::TYPE_SERVER && $type !== self::TYPE_CLIENT) {
            throw new \InvalidArgumentException('');
        }
        $result = [];
        $cookiePrefix = \BearCMS\Internal\Options::$cookiePrefix;
        $cookiePrefixLength = strlen($cookiePrefix);
        foreach ($_COOKIE as $key => $value) {
            if (substr($key, 0, $cookiePrefixLength) === $cookiePrefix) {
                $cookieTypePrefix = substr($key, 0, $cookiePrefixLength + 2);
                if (($type === self::TYPE_SERVER && $cookieTypePrefix === $cookiePrefix . 's_') || ($type === self::TYPE_CLIENT && $cookieTypePrefix === $cookiePrefix . 'c_' )) {
                    $result[substr($key, $cookiePrefixLength + 2)] = $value;
                }
            }
        }
        ksort($result);
        foreach (self::$pendingUpdate as $cookieData) {
            $cookieTypePrefix = substr($cookieData['name'], 0, $cookiePrefixLength + 2);
            $key = substr($cookieData['name'], $cookiePrefixLength + 2);
            if ($cookieData['expire'] > time()) {
                if (($type === self::TYPE_SERVER && $cookieTypePrefix === $cookiePrefix . 's_') || ($type === self::TYPE_CLIENT && $cookieTypePrefix === $cookiePrefix . 'c_' )) {
                    $result[$key] = $cookieData['value'];
                }
            } else {
                if ($cookieData['value'] === 'deleted' && array_key_exists($key, $result)) {
                    unset($result[$key]);
                }
            }
        }
        return $result;
    }

    /**
     * 
     * @param int $type
     * @param array $cookiesData
     * @throws \Exception
     */
    static function setList($type, $cookiesData)
    {
        $app = App::$instance;
        if ($type !== self::TYPE_SERVER && $type !== self::TYPE_CLIENT) {
            throw new \InvalidArgumentException('');
        }
        if (!is_array($cookiesData)) {
            throw new \InvalidArgumentException('');
        }
        $cookieTypePrefix = \BearCMS\Internal\Options::$cookiePrefix . ($type === self::TYPE_SERVER ? 's_' : 'c_');
        foreach ($cookiesData as $cookieData) {
            $cookieData['name'] = $cookieTypePrefix . $cookieData['name'];
            self::$pendingUpdate[$cookieData['name']] = $cookieData;
        }
    }

    /**
     * 
     */
    static function update()
    {
        if (!empty(self::$pendingUpdate)) {
            $app = App::$instance;
            $urlParts = parse_url($app->request->base);
            $cookiePath = isset($urlParts['path']) ? $urlParts['path'] : '';
            $cookieDomain = $urlParts['host'];
            foreach (self::$pendingUpdate as $cookieData) {
                $deleted = $cookieData['value'] === 'deleted' || $cookieData['expire'] === 0;
                setcookie($cookieData['name'], $deleted ? '' : $cookieData['value'], $deleted ? 0 : $cookieData['expire'], $cookiePath, $cookieDomain, $app->request->scheme === 'https', isset($cookieData['httponly']) ? $cookieData['httponly'] : true);
            }
            self::$pendingUpdate = [];
        }
    }

    /**
     * 
     * @param string $headers
     * @return array
     * @throws \Exception
     */
    static function parseServerCookies($headers)
    {
        $app = App::$instance;
        if (!is_string($headers)) {
            throw new \InvalidArgumentException('');
        }
        $result = array();
        $requestUrlParts = parse_url($app->request->base);
        $cookieMatches = [];
        preg_match_all('/Set-Cookie:(.*)/u', $headers, $cookieMatches);
        foreach ($cookieMatches[1] as $cookieMatch) {
            $cookieMatchData = explode(';', $cookieMatch);
            $cookieData = array('name' => '', 'value' => '', 'expire' => '', 'path' => '', 'domain' => '', 'secure' => false, 'httponly' => false);
            foreach ($cookieMatchData as $i => $value) {
                $valueParts = explode('=', $value, 2);
                $partName = trim($valueParts[0]);
                $partValue = isset($valueParts[1]) ? trim($valueParts[1]) : '';
                if ($i == 0) {
                    $cookieData['name'] = $partName;
                    $cookieData['value'] = $partValue;
                }
                if ($partName == 'path') {
                    if (isset($requestUrlParts['path']) && strlen(trim($requestUrlParts['path'], '/')) > 0) {
                        $cookieData['path'] = '/' . trim($requestUrlParts['path'], '/') . '/' . (strlen(trim($partValue, '/')) > 0 ? trim($partValue, '/') . '/' : '');
                    } else {
                        $cookieData['path'] = $partValue;
                    }
                }
                if ($partName == 'httponly') {
                    $cookieData['httponly'] = true;
                }
                if ($partName == 'expires') {
                    $cookieData['expire'] = strtotime($partValue);
                }
                if ($partName == 'secure') {
                    $cookieData['secure'] = $app->request->scheme === 'https';
                }
                if (isset($requestUrlParts['host'])) {
                    if ($partValue === trim(\BearCMS\Internal\Options::$serverUrl, 'htps:/')) { // todo trqbva da se vrusta bez tochkata // '.' . 
                        $cookieData['domain'] = $requestUrlParts['host'];
                    }
                }
            }
            $result[$cookieData['name']] = $cookieData;
        }
        return array_values($result);
    }

}
