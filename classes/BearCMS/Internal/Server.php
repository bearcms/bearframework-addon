<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;
use BearCMS\Internal\Cookies;
use BearCMS\Internal\Options;

class Server
{

    static function call($name, $arguments = null, $sendCookies = false)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('');
        }
        if ($arguments !== null && !is_array($arguments)) {
            throw new \InvalidArgumentException('');
        }
        if (!is_bool($sendCookies)) {
            throw new \InvalidArgumentException('');
        }
        $url = Options::$serverUrl . '?name=' . $name;
        $response = self::sendRequest($url, $arguments, $sendCookies);
        if ($sendCookies && self::isRetryResponse($response)) {
            $response = self::sendRequest($url, $arguments, $sendCookies);
        }
        return $response['body'];
    }

    static function proxyAjax()
    {
        $response = self::sendRequest(Options::$serverUrl . '-aj/', $_POST, true);
        if (self::isRetryResponse($response)) {
            return json_encode(array('js' => 'window.location.reload(true);'), JSON_UNESCAPED_UNICODE);
        }

        if (isset($response['bodyPrefix'])) {
            $response['body'] = json_encode(self::mergeAjaxResponses(json_decode($response['bodyPrefix'], true), json_decode($response['body'], true)));
        }
        return self::updateAssetsUrls($response['body'], true);
    }

    static function mergeAjaxResponses($response1, $response2)
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

    static function isRetryResponse($response)
    {
        $responseHeader = $response['header'];
        return strpos($responseHeader, 'X-App-Sr: qyi') > 0 ||
                strpos($responseHeader, 'X-App-Sr: pkr') > 0 ||
                strpos($responseHeader, 'X-App-Sr: jke') > 0 ||
                strpos($responseHeader, 'X-App-Sr: wpr') > 0;
    }

    static function getAssetsUrl($originalUrls)
    {
        $app = App::$instance;
        sort($originalUrls);
        $resultKey = '.temp/bearcms/assets/' . md5(serialize($originalUrls)) . '.js';
        $result = $app->data->get([
            'key' => $resultKey,
            'result' => ['key']
        ]);
        if (!isset($result['key'])) {
            foreach ($originalUrls as $originalUrl) {
                $key = '.temp/bearcms/assets/' . md5(serialize([$originalUrl])) . '.js';
                $result = $app->data->get([
                    'key' => $key,
                    'result' => ['key']
                ]);
                if (!isset($result['key'])) {
                    $app->data->set([
                        'key' => $key,
                        'body' => file_get_contents($originalUrl)
                    ]);
                    $app->data->makePublic([
                        'key' => $key
                    ]);
                }
            }
            if (sizeof($originalUrls) > 1) {
                $bundleContent = '';
                foreach ($originalUrls as $originalUrl) {
                    $key = '.temp/bearcms/assets/' . md5(serialize([$originalUrl])) . '.js';
                    $urlFilename = $app->data->getFilename($key);
                    $bundleContent .= file_get_contents($urlFilename);
                }
                $app->data->set([
                    'key' => $resultKey,
                    'body' => $bundleContent
                ]);
                $app->data->makePublic([
                    'key' => $resultKey
                ]);
            }
        }
        return $app->assets->getUrl($app->data->getFilename($resultKey));
    }

    static function updateAssetsUrls($content, $ajaxMode)
    {

        $serverUrl = \BearCMS\Internal\Options::$serverUrl;

        if ($ajaxMode) {
            $hasChange = false;
            $contentData = json_decode($content, true);
            if (isset($contentData['jsFiles'])) {
                $newJsFiles = [];
                foreach ($contentData['jsFiles'] as $src) {
                    if (isset($src{0}) && strpos($src, $serverUrl) === 0) {
                        $hasChange = true;
                        $scriptBundle[] = $src;
                    } else {
                        $newJsFiles[] = $src;
                    }
                }
                if (!empty($scriptBundle)) {
                    $newJsFiles[] = self::getAssetsUrl($scriptBundle);
                }
                $contentData['jsFiles'] = $newJsFiles;
            }
            if ($hasChange) {
                return json_encode($contentData);
            }
        } else {
            $hasChange = false;
            $dom = new \IvoPetkov\HTML5DOMDocument();
            $dom->loadHTML($content);
            $scripts = $dom->querySelectorAll('script');
            $scriptBundle = [];
            $scriptsToRemove = [];
            foreach ($scripts as $script) {
                $src = (string) $script->getAttribute('src');
                if (isset($src{0}) && strpos($src, $serverUrl) === 0) {
                    $hasChange = true;
                    if ($script->getAttribute('async') === 'async') {
                        $scriptsToRemove[] = $script;
                        $scriptBundle[] = $src;
                    } else {
                        $script->setAttribute('src', self::getAssetsUrl([$src]));
                    }
                }
            }
            foreach ($scriptsToRemove as $script) {
                $script->parentNode->removeChild($script);
            }
            if (!empty($scriptBundle)) {
                $script = $dom->createElement('script');
                $script->setAttribute('async', 'async');
                $script->setAttribute('src', self::getAssetsUrl($scriptBundle));
                $dom->querySelector('body')->appendChild($script);
            }
            if ($hasChange) {
                return $dom->saveHTML();
            }
        }
        return $content;
    }

    static function makeRequest($url, $data, $cookies)
    {
        $app = App::$instance;
        if (!is_string($url)) {
            throw new \InvalidArgumentException('');
        }
        if (!is_array($data)) {
            throw new \InvalidArgumentException('');
        }
        if (!is_array($cookies)) {
            throw new \InvalidArgumentException('');
        }

        if ($app->bearCMS->currentUser->exists()) {
            $currentUserData = $app->data->get([
                'key' => 'bearcms/users/user/' . md5($app->bearCMS->currentUser->getID()) . '.json',
                'result' => ['body']
            ]);
            $currentUserID = null;
            if (isset($currentUserData['body'])) {
                $currentUserData = json_decode($currentUserData['body'], true);
                $currentUserID = isset($currentUserData['id']) ? $currentUserData['id'] : null;
            }
            $data['currentUserID'] = $currentUserID;
        }

        $data['features'] = json_encode(Options::$features);
        $data['language'] = Options::$language;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'BearCMS Bear Framework Addon ' . \BearCMS::VERSION);
        if (!empty($cookies)) {
            $cookiesValues = [];
            foreach ($cookies as $key => $value) {
                $cookiesValues[] = $key . '=' . $value;
            }
            curl_setopt($ch, CURLOPT_COOKIE, implode(';', $cookiesValues));
        }
        $response = curl_exec($ch);
        $error = curl_error($ch);

        $responseHeaderSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $responseHeader = trim(substr($response, 0, $responseHeaderSize));
        $responseBody = substr($response, $responseHeaderSize);
        if (strpos($responseHeader, 'X-App-Bg: 1') !== false) {
            try {
                $responseBody = gzuncompress($responseBody);
            } catch (\Exception $e) {
                throw new \Exception('Invalid response');
            }
        }
        $log = "Bear CMS Server Request:\n\n";
        $log .= 'User: ' . $app->bearCMS->currentUser->getID() . "\n\n";
        $log .= 'Time: ' . curl_getinfo($ch, CURLINFO_TOTAL_TIME) . ' / dns: ' . curl_getinfo($ch, CURLINFO_NAMELOOKUP_TIME) . ', connect: ' . curl_getinfo($ch, CURLINFO_CONNECT_TIME) . ', download: ' . curl_getinfo($ch, CURLINFO_STARTTRANSFER_TIME) . "\n\n";
        $log .= 'Request header: ' . trim(curl_getinfo($ch, CURLINFO_HEADER_OUT)) . "\n\n";
        $log .= 'Request data: ' . "\n" . print_r($data, true) . "\n\n";
        curl_close($ch);
        foreach ($cookies as $key => $value) {
            $log = str_replace($value, '*' . strlen($value) . 'chars*', $log);
        }
        $log .= 'Response header: ' . $responseHeader . "\n\n";
        $newCookies = Cookies::parseServerCookies($responseHeader);
        foreach ($newCookies as $newCookie) {
            $log = str_replace($newCookie['value'], '*' . strlen($newCookie['value']) . 'chars*', $log);
        }
        //$log .= 'Response body: ' . $responseBody;
        $log .= 'Response body: ' . '*' . strlen($responseBody) . 'chars*';
        if (strlen($app->config->logsDir) > 0) {
            $app->logger->log('info', $log);
        }
        if (isset($error{0})) {
            throw new \Exception('Request curl error: ' . $error . ' (1027)');
        }
        return new \ArrayObject(['header' => $responseHeader, 'body' => $responseBody]);
    }

    static function sendRequest($url, $data = null, $sendCookies = false)
    {
        $app = App::$instance;
        if (!is_string($url)) {
            throw new \InvalidArgumentException('');
        }
        if ($data !== null && !is_array($data)) {
            throw new \InvalidArgumentException('');
        }
        if (!is_bool($sendCookies)) {
            throw new \InvalidArgumentException('');
        }
        if (!is_array($data)) {
            $data = [];
        }

        $data['auth'] = json_encode([1, 'todo-siteid', 'todo-hash', $app->request->base], JSON_UNESCAPED_UNICODE); //todo
        $data['responseType'] = 'jsongz';
        if (isset($data['_ajaxreferer'])) {
            $data['_ajaxreferer'] = str_replace($app->request->base . '/', Options::$serverUrl, $data['_ajaxreferer']);
        }

        $cookies = $sendCookies ? Cookies::getList(Cookies::TYPE_SERVER) : [];

        $send = function($requestData = [], $counter = 1) use(&$send, $app, $url, $data, $cookies) {
            if ($counter > 10) {
                throw new \Exception('Too much requests');
            }
            $response = self::makeRequest($url, array_merge($data, $requestData, ['requestNumber' => $counter]), $cookies);
            if (self::isRetryResponse($response)) {
                return $response;
            }
            $responseData = json_decode($response['body'], true);
            if (!is_array($responseData) || !array_key_exists('response', $responseData)) {
                throw new \Exception('Invalid response. Body: ' . $response['body']);
            }
            $responseData = $responseData['response'];
            $response['body'] = $responseData['body'];
            $responseMeta = $responseData['meta'];

            $app->logger->log('info', print_r($responseData, true));

            $resend = isset($responseMeta['resend']) && (int) $responseMeta['resend'] > 0;
            $resendRequestData = [];

            if (isset($responseMeta['commands']) && is_array($responseMeta['commands'])) {
                $commandsResults = [];
                foreach ($responseMeta['commands'] as $commandData) {
                    if (isset($commandData['name']) && isset($commandData['data'])) {
                        $commandResult = '';
                        $callback = ['\BearCMS\Internal\ServerCommands', $commandData['name']];
                        if (is_callable($callback)) {
                            $commandResult = call_user_func($callback, $commandData['data'], $response);
                        }
                        if (isset($commandData['key'])) {
                            $commandsResults[$commandData['key']] = $commandResult;
                        }
                    }
                }
                if ($resend) {
                    $resendRequestData['commandsResults'] = json_encode($commandsResults, JSON_UNESCAPED_UNICODE);
                }
            }
            if (isset($responseMeta['clientEvents'])) {
                $resendRequestData['clientEvents'] = $responseMeta['clientEvents'];
                $resend = true;
            }
            if (isset($responseMeta['currentUser'])) {
                $currentUserData = $responseMeta['currentUser'];
                $app->data->set([
                    'key' => '.temp/bearcms/userkeys/' . md5($currentUserData['key']),
                    'body' => $currentUserData['id']
                ]);
            }
            if (isset($responseMeta['clientEvents'])) {
                $responseBody = $response['body']; // Can be changed in a command
            }
            if ($resend) {
                $response = $send($resendRequestData, $counter + 1);
            }
            if (isset($responseMeta['clientEvents'])) {
                $response['bodyPrefix'] = $responseBody;
            }
            return $response;
        };
        $response = $send();
        if ($sendCookies) {
            Cookies::setList(Cookies::TYPE_SERVER, Cookies::parseServerCookies($response['header']));
        }
        return $response;
    }

}
