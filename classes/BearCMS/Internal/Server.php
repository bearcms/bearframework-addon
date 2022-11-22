<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearCMS\Internal;
use BearCMS\Internal\Config;
use BearFramework\App;
use IvoPetkov\HTML5DOMDocument;

/**
 * @internal
 * @codeCoverageIgnore
 */
class Server
{

    /**
     *
     * @param string $name
     * @param array $arguments
     * @param bool $sendCookies
     * @param string $cacheKey
     * @return type
     */
    public static function call(string $name, array $arguments = [], bool $sendCookies = false, string $cacheKey = null)
    {
        $app = App::get();
        $send = function () use ($name, $arguments, $sendCookies) {
            $url = Config::$serverUrl . '?name=' . $name;
            $response = self::sendRequest($url, $arguments, $sendCookies);
            return $response;
        };
        if ($cacheKey !== null) {
            $cacheKey = md5($cacheKey) . md5($name) . md5(json_encode($arguments, JSON_THROW_ON_ERROR));
            $data = $app->cache->getValue($cacheKey);
            if (is_array($data)) {
                return $data['value'];
            } elseif ($data === 'invalid') {
                return [];
            } else {
                $data = $send();
                if (is_array($data) && isset($data['value'])) {
                    if (isset($data['cache']) && (int) $data['cache'] === 1) {
                        $cacheItem = $app->cache->make($cacheKey, $data);
                        $cacheItem->ttl = isset($data['cacheTTL']) ? (int) $data['cacheTTL'] : 10;
                        $app->cache->set($cacheItem);
                    }
                    return $data['value'];
                } else {
                    $cacheItem = $app->cache->make($cacheKey, 'invalid');
                    $cacheItem->ttl = 10;
                    $app->cache->set($cacheItem);
                    return [];
                }
            }
        }
        return $send()['value'];
    }

    /**
     *
     * @return string
     */
    public static function proxyAjax(): string
    {
        $app = App::get();
        $formDataList = $app->request->formData->getList();
        $temp = [];
        foreach ($formDataList as $formDataItem) {
            $temp[$formDataItem->name] = $formDataItem->value;
        }
        $response = self::sendRequest(Config::$serverUrl . '-aj/', $temp, true);

        if (is_array($response['value']) && isset($response['value']['error'])) {
            return json_encode(['js' => 'alert("' . (isset($response['value']['errorMessage']) ? $response['value']['errorMessage'] : 'An error occurred! Please, try again later and contact the administrator if the problem persists!') . '");'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        }

        if (isset($response['previousValues'])) {
            foreach ($response['previousValues'] as $previousValue) {
                if (is_array($previousValue)) {
                    $response['value'] = self::mergeAjaxResponses($previousValue, $response['value']);
                }
            }
        }
        $response['value'] = self::updateAssetsUrls($response['value'], true);
        return json_encode($response['value'], JSON_THROW_ON_ERROR);
    }

    /**
     *
     * @param array $response1
     * @param array $response2
     * @return array
     */
    public static function mergeAjaxResponses(array $response1, array $response2): array
    {
        foreach ($response2 as $key => $data) {
            if (!isset($response1[$key])) {
                $response1[$key] = is_array($data) ? [] : '';
            }
            if (is_array($data)) {
                $response1[$key] = array_merge($response1[$key], $data);
            } else {
                $response1[$key] .= $data;
            }
        }
        return $response1;
    }

    /**
     * Returns the proxy URL for the server resource.
     * 
     * @param string $url
     * @return string|null
     */
    static function getAssetFilename(string $url): ?string
    {
        $serverUrl = Config::$serverUrl;
        if (isset($url[0]) && strpos($url, $serverUrl) === 0) {
            if (strpos($url, '?') !== false) {
                $url = explode('?', $url)[0];
            }
            return 'assets/s/' . str_replace($serverUrl, '', $url);
        }
        return null;
    }

    /**
     *
     * @param mixed $content
     * @param bool $ajaxMode
     * @return mixed
     */
    public static function updateAssetsUrls($content, bool $ajaxMode)
    {
        $app = App::get();
        $context = $app->contexts->get(__DIR__);

        $updateAssetURL = function (string $url) use ($context): string {
            $filename = self::getAssetFilename($url);
            if ($filename !== null) {
                return $context->assets->getURL($filename, ['cacheMaxAge' => 999999999, 'version' => 1]);
            }
            return $url;
        };

        if ($ajaxMode) {
            $hasChange = false;
            $contentData = $content;
            if (isset($contentData['jsFiles'])) {
                foreach ($contentData['jsFiles'] as $i => $url) {
                    $updatedURL = $updateAssetURL($url);
                    if ($url !== $updatedURL) {
                        $contentData['jsFiles'][$i] = $updatedURL;
                        $hasChange = true;
                    }
                }
            }
            if ($hasChange) {
                return $contentData;
            }
        } else {
            $hasChange = false;
            $dom = new HTML5DOMDocument();
            $dom->loadHTML($content, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
            $scripts = $dom->querySelectorAll('script');
            foreach ($scripts as $script) {
                $url = (string) $script->getAttribute('src');
                $updatedURL = $updateAssetURL($url);
                if ($url !== $updatedURL) {
                    $script->setAttribute('src', $updatedURL);
                    $script->setAttribute('id', md5($url)); // Is this needed ???
                    $hasChange = true;
                }
            }
            if ($hasChange) {
                return $dom->saveHTML();
            }
        }
        return $content;
    }

    /**
     *
     * @param string $url
     * @param array $data
     * @param array $cookies
     * @param bool $includeLogData
     * @return array Returns an array in the following format: ['headers' => ..., 'body' => ..., 'logData' => ...]
     * @throws \Exception
     */
    public static function makeRequest(string $url, array $data, array $cookies, bool $includeLogData = false): array
    {
        $app = App::get();

        $clientData = [];
        $clientData['appSecretKey'] = Config::getHashedAppSecretKey();
        $clientData['whitelabel'] = (int) Config::$whitelabel;
        $clientData['requestBase'] = $app->request->base;
        $clientData['cookiePrefix'] = Config::$cookiePrefix;
        if ($app->bearCMS->currentUser->exists()) {
            $currentUserData = Internal\Data::getValue('bearcms/users/user/' . md5($app->bearCMS->currentUser->getID()) . '.json');
            $currentUserID = null;
            if ($currentUserData !== null) {
                $currentUserData = json_decode($currentUserData, true);
                $currentUserID = isset($currentUserData['id']) ? $currentUserData['id'] : null;
            }
            $clientData['currentUserID'] = $currentUserID;
            $clientData['currentUserProfileID'] = [$app->currentUser->provider, $app->currentUser->id];
        }

        $clientData['features'] = Config::$features;
        $clientData['language'] = Config::$language;
        $clientData['uiColor'] = Config::getVariable('uiColor');
        $clientData['uiTextColor'] = Config::getVariable('uiTextColor');
        $clientData['adminPagesPathPrefix'] = Config::$adminPagesPathPrefix;
        $clientData['blogPagesPathPrefix'] = Config::$blogPagesPathPrefix;
        $clientData['elementsTypes'] = array_values(Internal\ElementsHelper::$elementsTypesCodes);
        $maxUploadsSize = Config::getVariable('maxUploadsSize');
        if ($maxUploadsSize !== null) {
            $clientData['maxUploadsSize'] = (int)$maxUploadsSize;
            $clientData['uploadsSize'] = Internal\Data\UploadsSize::getSize();
        }
        $maxUploadSize = Config::getVariable('maxUploadSize');
        if ($maxUploadSize === null) {
            $getSystemMaxUploadSize = function () { // todo move to other class and cache result
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
            $maxUploadSize = $getSystemMaxUploadSize();
            $clientData['maxUploadSize'] = $maxUploadSize;
        }
        $clientData['assetsFileOptions'] = Internal\Assets::$supportedFileOptions;
        $clientData['appSpecific'] = Config::$appSpecificServerData;
        $clientData['flags'] = [
            'sbpc', // allow comments in blog posts
            'gl3a', // has files support
            'jzk3ns', // has google fonts embed support,
            'lan3k', // has page duplicate support,
            'kan4', // has multilanguage support,
            'j93a', // has related posts support
            'k931', // has support for new pages and blogs statuses (public,secret,private and published,draft,private)
            'm3a1', // has image property for pages and blog posts
            '7a2f', // new structural elements data format (no data key), new flexible box element and elements combinations,
            'n4aj', // new data access api (specific server commands instead of direct data access)
            'bz49', // URL redirects support
            'm4a9', // has commentsGet server command
            '78va', // new elements commands
        ];
        $settings = $app->bearCMS->data->settings->get();
        $clientData['contentLanguages'] = $settings->languages;
        $data['clientData'] = $clientData;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, gzcompress(json_encode($data, JSON_THROW_ON_ERROR)));
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Bear CMS Bear Framework Addon');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: bearcms/jsongz', 'Content-Type: bearcms/jsongz']);
        if (!empty($cookies)) {
            $cookiesValues = [];
            foreach ($cookies as $key => $value) {
                $cookiesValues[] = $key . '=' . $value;
            }
            curl_setopt($ch, CURLOPT_COOKIE, implode(';', $cookiesValues));
        }
        $response = curl_exec($ch);
        $error = curl_error($ch);

        $responseHeadersSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $responseHeaders = trim(substr($response, 0, $responseHeadersSize));
        $responseBody = substr($response, $responseHeadersSize);
        if (strpos(strtolower($responseHeaders), 'content-type: bearcms/jsongz') !== false) {
            try {
                $responseBody = gzuncompress($responseBody);
            } catch (\Exception $e) {
                throw new \Exception('Invalid response!');
            }
        }

        $logData = null;
        if ($includeLogData) {
            $logData = [];
            $logData['userID'] = $app->bearCMS->currentUser->getID();
            $logData['timing'] = [
                'total' => curl_getinfo($ch, CURLINFO_TOTAL_TIME),
                'dns' => curl_getinfo($ch, CURLINFO_NAMELOOKUP_TIME),
                'connect' => curl_getinfo($ch, CURLINFO_CONNECT_TIME),
                'waiting' => curl_getinfo($ch, CURLINFO_STARTTRANSFER_TIME),
            ];
            $logData['request'] = [
                'url' => $url,
                'headers' => trim(curl_getinfo($ch, CURLINFO_HEADER_OUT)),
                'data' => $data,
            ];
            $logData['response'] = [
                'headers' => $responseHeaders,
                'body' => $responseBody,
            ];
        }
        curl_close($ch);
        if (isset($error[0])) {
            throw new \Exception('Request curl error: ' . $error . ' (1027)');
        }
        $result = [
            'headers' => $responseHeaders,
            'body' => $responseBody,
        ];
        if ($logData !== null) {
            $result['logData'] = $logData;
        }
        return $result;
    }

    /**
     *
     * @param string $url
     * @param array $data
     * @param bool $sendCookies
     * @return array Returns an array in the following format: ['headers' => ..., 'value' => ..., 'cache' => ..., 'cacheTTL' => ...]
     */
    public static function sendRequest(string $url, array $data = [], bool $sendCookies = false): array
    {
        $app = App::get();
        if (!is_array($data)) {
            $data = [];
        }

        $cookies = $sendCookies ? Internal\Cookies::getList(Internal\Cookies::TYPE_SERVER) : [];

        $commandsResultsCache = [];

        $send = function ($requestData = [], $counter = 1) use (&$send, $app, $url, $data, $cookies, &$commandsResultsCache) {
            if ($counter > 10) {
                throw new \Exception('Too much requests');
            }
            $logServerRequests = Config::getVariable('logServerRequests') === true;
            $requestResponse = self::makeRequest($url, array_merge($data, $requestData, ['requestNumber' => $counter]), $cookies, $logServerRequests);
            $requestResponseBody = json_decode($requestResponse['body'], true);
            if (!is_array($requestResponseBody) || !array_key_exists('response', $requestResponseBody)) {
                throw new \Exception('Invalid response. Body: ' . $requestResponse['body']);
            }
            $requestResponseData = $requestResponseBody['response'];

            $response = new \ArrayObject([ // Must be ArrayObject so it can be passed by reference to the internal commands
                'headers' => $requestResponse['headers'],
                'value' => isset($requestResponseData['value']) ? $requestResponseData['value'] : '',
                'cache' => isset($requestResponseData['cache']) ? (int) $requestResponseData['cache'] > 0 : false,
                'cacheTTL' => isset($requestResponseData['cacheTTL']) ? (int) $requestResponseData['cacheTTL'] : 0,
            ]);

            $requestResponseMeta = isset($requestResponseData['meta']) ? $requestResponseData['meta'] : [];

            $resend = isset($requestResponseMeta['resend']) && (int) $requestResponseMeta['resend'] > 0;
            $resendData = [];
            if ($logServerRequests) {
                $timing = [];
            }

            $exceptionToThrow = null;
            try {
                if (isset($requestResponseMeta['commands']) && is_array($requestResponseMeta['commands'])) {
                    $commandsResults = [];
                    foreach ($requestResponseMeta['commands'] as $commandData) {
                        if (isset($commandData['name']) && isset($commandData['data'])) {
                            $key = isset($commandData['key']) ? $commandData['key'] : null;
                            $useCache = $key && isset($commandData['cache']) && (int)$commandData['cache'] === 1;
                            if ($logServerRequests) {
                                $executeStartTime = microtime(true);
                                $resultIsCached = false;
                            }
                            if ($useCache && array_key_exists($key, $commandsResultsCache)) {
                                $commandResult = $commandsResultsCache[$key];
                                if ($logServerRequests) {
                                    $resultIsCached = true;
                                }
                            } else {
                                $commmandName = $commandData['name'];
                                $commandResult = '';
                                $callable = ['\BearCMS\Internal\ServerCommands', $commmandName];
                                if (is_callable($callable)) {
                                    $commandResult = call_user_func($callable, $commandData['data'], $response);
                                } else if (isset(\BearCMS\Internal\ServerCommands::$external[$commmandName])) {
                                    $callable = \BearCMS\Internal\ServerCommands::$external[$commmandName];
                                    if (is_callable($callable)) {
                                        $commandResult = call_user_func($callable, $commandData['data'], $response);
                                    }
                                }
                                if ($useCache) {
                                    $commandsResultsCache[$key] = $commandResult;
                                }
                            }
                            if ($logServerRequests) {
                                $executeTotalTime = microtime(true) - $executeStartTime;
                                $timing[] = ['command' => $commandData, 'total' => $executeTotalTime, 'cached' => $resultIsCached];
                            }
                            if ($key !== null) {
                                $commandsResults[$key] = $commandResult;
                            }
                        }
                    }
                    if ($resend) {
                        $resendData['commandsResults'] = $commandsResults;
                    }
                }
            } catch (\Exception $e) {
                $exceptionToThrow = $e;
            }

            if ($logServerRequests) {
                $logData = $requestResponse['logData'];
                $logData['response']['data'] = [
                    'value' => $response['value'],
                    'meta' => $requestResponseMeta,
                    'cache' => $response['cache'],
                    'cacheTTL' => $response['cacheTTL'],
                    'timing' => $timing
                ];
                if ($exceptionToThrow !== null) {
                    $logData['exception'] = $exceptionToThrow->getMessage();
                }
                $app->logs->log('bearcms-server-requests', print_r($logData, true));
            }

            if ($exceptionToThrow !== null) {
                throw $exceptionToThrow;
            }

            if (isset($requestResponseMeta['clientEvents'])) {
                $resendData['clientEvents'] = $requestResponseMeta['clientEvents'];
                $resend = true;
            }

            if (isset($requestResponseMeta['currentUser'])) {
                for ($i = 1; $i <= 3; $i++) {
                    try {
                        $currentUserData = $requestResponseMeta['currentUser'];
                        if ($currentUserData['key'] !== null) {
                            $dataKey = '.temp/bearcms/userkeys/' . md5($currentUserData['key']);
                            $userID = (string) $currentUserData['id'];
                            if ($app->data->getValue($dataKey) !== $userID) {
                                $app->data->set($app->data->make($dataKey, $userID));
                            }
                        }
                        break;
                    } catch (\BearFramework\App\Data\DataLockedException $e) {
                    }
                    if ($i === 3) {
                        throw $e;
                    } else {
                        sleep(1);
                    }
                }
            }

            $previousValues = [];
            if (isset($requestResponseMeta['clientEvents'])) {
                $previousValues[] = $response['value']; // Can be changed in a command so use from the response object
            }
            if ($resend) {
                $response = $send($resendData, $counter + 1);
            }
            if (!empty($previousValues)) {
                $response['previousValues'] = $previousValues;
            }
            return $response;
        };
        $response = $send();
        if ($sendCookies) {
            Internal\Cookies::setList(Internal\Cookies::TYPE_SERVER, Internal\Cookies::parseServerCookies($response['headers']));
        }
        return (array) $response;
    }

    /**
     * Downlaods a server file and returns a temp filename
     *
     * @param string $path
     * @param bool $useCached
     * @return string
     */
    static function download(string $path, bool $useCached = false): string
    {
        $url =  Config::$serverUrl . ltrim($path, '/');
        return Internal\Downloads::download($url, $useCached);
    }
}
