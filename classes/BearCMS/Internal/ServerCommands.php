<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;

final class ServerCommands
{

    /**
     * 
     * @return array
     */
    static function about()
    {
        $result = [];
        $result['siteID'] = Options::$siteID;
        $result['phpVersion'] = phpversion();
        $result['frameworkVersion'] = App::VERSION;
        $result['addonVersion'] = \BearCMS::VERSION;
        return $result;
    }

    /**
     * 
     * @return array
     */
    static function pages()
    {
        $app = App::get();
        $structure = $app->data->get(
                [
                    'key' => 'bearcms/pages/structure.json',
                    'result' => ['key', 'body']
                ]
        );
        $pages = $app->data->search(
                [
                    'where' => [
                        ['key', 'bearcms/pages/page/', 'startsWith']
                    ],
                    'result' => ['key', 'body']
                ]
        );
        $temp = [];
        $temp['structure'] = isset($structure['body']) ? json_decode($structure['body'], true) : [];
        $temp['pages'] = [];
        foreach ($pages as $page) {
            $temp['pages'][] = json_decode($page['body'], true);
        }
        return $temp;
    }

    /**
     * 
     * @return array
     */
    static function blogPosts()
    {
        $app = App::get();
        $result = $app->data->search(
                [
                    'where' => [
                        ['key', 'bearcms/blog/post/', 'startsWith']
                    ],
                    'result' => ['key', 'body']
                ]
        );
        $temp = [];
        foreach ($result as $item) {
            $temp[] = json_decode($item['body'], true);
        }
        return $temp;
    }

    /**
     * 
     * @return array
     */
    static function usersIDs()
    {
        $app = App::get();
        $result = $app->data->search(
                [
                    'where' => [
                        ['key', 'bearcms/users/user/', 'startsWith']
                    ],
                    'result' => ['key', 'body']
                ]
        );
        $temp = [];
        foreach ($result as $item) {
            $itemData = json_decode($item['body'], true);
            if (isset($itemData['id'])) {
                $temp[] = $itemData['id'];
            }
        }
        return $temp;
    }

    /**
     * 
     * @return array
     */
    static function usersInvitations()
    {
        $app = App::get();
        $result = $app->data->search(
                [
                    'where' => [
                        ['key', 'bearcms/users/invitation/', 'startsWith']
                    ],
                    'result' => ['key', 'body']
                ]
        );
        $temp = [];
        foreach ($result as $item) {
            $temp[] = json_decode($item['body'], true);
        }
        return $temp;
    }

    /**
     * 
     * @param array $data
     * @return array
     * @throws \Exception
     */
    static function userIDByEmail($data)
    {
        if (!isset($data['email'])) {
            throw new \Exception('');
        }
        $email = (string) $data['email'];
        $app = App::get();
        $users = $app->data->search(
                [
                    'where' => [
                        ['key', 'bearcms/users/user/', 'startsWith']
                    ],
                    'result' => ['key', 'body']
                ]
        );
        foreach ($users as $user) {
            $userData = json_decode($user['body'], true);
            if (isset($userData['emails'])) {
                foreach ($userData['emails'] as $userEmail) {
                    if ($userEmail === $email) {
                        return isset($userData['id']) ? $userData['id'] : null;
                    }
                }
            }
        }
        return null;
    }

    /**
     * 
     * @return array
     */
    static function themes()
    {
        $themes = \BearCMS\Internal\Data\Themes::getList();
        $result = [];
        foreach ($themes as $theme) {
            if (isset($theme['manifestFilename'])) {
                $manifestData = \BearCMS\Internal\Data\Themes::getManifestData($theme['manifestFilename'], $theme['dir']);
                $manifestData['id'] = $theme['id'];
                if (isset($manifestData['options'])) {
                    $manifestData['hasOptions'] = !empty($manifestData['options']);
                    unset($manifestData['options']);
                } else {
                    $manifestData['hasOptions'] = false;
                }
                $result[] = $manifestData;
            } elseif ($theme['id'] === 'none') {
                $result[] = [
                    'id' => 'none',
                    'hasOptions' => false
                ];
            }
        }
        return $result;
    }

    /**
     * 
     * @param array $data
     * @return array
     * @throws \Exception
     */
    static function theme($data)
    {
        $app = App::get();
        if (!isset($data['id'])) {
            throw new \Exception('');
        }

        if ($data['id'] === 'none') {
            return ['id' => 'none'];
        }

        $includeOptions = isset($data['includeOptions']) && !empty($data['includeOptions']);
        $themes = \BearCMS\Internal\Data\Themes::getList();
        foreach ($themes as $theme) {
            if ($theme['id'] === $data['id']) {
                if (isset($theme['manifestFilename'])) {
                    $manifestData = \BearCMS\Internal\Data\Themes::getManifestData($theme['manifestFilename'], $theme['dir']);
                    $manifestData['id'] = $theme['id'];
                    if (isset($manifestData['options'])) {
                        $manifestData['hasOptions'] = !empty($manifestData['options']);
                    } else {
                        $manifestData['hasOptions'] = false;
                    }
                    if ($includeOptions) {
                        $manifestData['options'] = [
                            'definition' => isset($manifestData['options']) ? $manifestData['options'] : []
                        ];


                        $result = $app->data->get(
                                [
                                    'key' => 'bearcms/themes/theme/' . md5($theme['id']) . '.json',
                                    'result' => ['key', 'body']
                                ]
                        );
                        if (isset($result['body'])) {
                            $temp = json_decode($result['body'], true);
                            $optionsValues = isset($temp['options']) ? $temp['options'] : [];
                        } else {
                            $optionsValues = [];
                        }
                        $manifestData['options']['activeValues'] = $optionsValues;

                        $result = $app->data->get(
                                [
                                    'key' => '.temp/bearcms/userthemeoptions/' . md5($app->bearCMS->currentUser->getID()) . '/' . md5($data['id']) . '.json',
                                    'result' => ['key', 'body']
                                ]
                        );
                        if (isset($result['body'])) {
                            $temp = json_decode($result['body'], true);
                            $optionsValues = isset($temp['options']) ? $temp['options'] : [];
                        } else {
                            $optionsValues = null;
                        }
                        $manifestData['options']['currentUserValues'] = $optionsValues;
                    } else {
                        if (isset($manifestData['options'])) {
                            unset($manifestData['options']);
                        }
                    }
                    return $manifestData;
                }
            }
        }
        return null;
    }

    /**
     * 
     * @return array
     */
    static function addons()
    {
        $app = App::get();
        $result = $app->data->search(
                [
                    'where' => [
                        ['key', 'bearcms/addons/addon/', 'startsWith']
                    ],
                    'result' => ['key', 'body']
                ]
        );
        $temp = [];
        foreach ($result as $item) {
            $addonData = json_decode($item['body'], true);
            if (isset($addonData['id'])) {
                $addonManifestData = \BearCMS\Internal\Data\Addons::getManifestData($addonData['id']);
                if (is_array($addonManifestData)) {
                    $addonData['name'] = $addonManifestData['name'];
                    $addonData['hasOptions'] = isset($addonManifestData['options']) && !empty($addonManifestData['options']);
                } else {
                    $addonData['name'] = $addonData['id'];
                    $addonData['hasOptions'] = false;
                }
                if (isset($addonData['options'])) {
                    unset($addonData['options']);
                }
                $temp[] = $addonData;
            }
        }
        return $temp;
    }

    /**
     * 
     * @return array
     */
    static function files()
    {
        $app = App::get();
        $result = $app->data->search(
                [
                    'where' => [
                        ['key', 'bearcms/files/custom/', 'startsWith']
                    ],
                    'result' => ['key', 'metadata']
                ]
        );
        $temp = [];
        foreach ($result as $item) {
            $key = $item['key'];
            $temp[] = [
                'filename' => str_replace('bearcms/files/custom/', '', $key),
                'name' => (isset($item['metadata.name']) ? $item['metadata.name'] : str_replace('bearcms/files/custom/', '', $key)),
                'published' => (isset($item['metadata.published']) ? (int) $item['metadata.published'] : 0)
            ];
        }
        return $temp;
    }

    /**
     * 
     * @return array
     */
    static function comments($data)
    {
        $app = App::get();
        if (!isset($data['type'])) {
            throw new \Exception('');
        }
        if (!isset($data['page'])) {
            throw new \Exception('');
        }
        if (!isset($data['limit'])) {
            throw new \Exception('');
        }
        $result = $app->bearCMS->data->comments->getList();
        $result->sortBy('createdTime', 'desc');
        if ($data['type'] !== 'all') {
            $result->filterBy('status', $data['type']);
        }
        $result = $result->slice($data['limit'] * ($data['page'] - 1), $data['limit']);
        return $result->toArray();
    }

    /**
     * 
     * @return array
     */
    static function setCommentStatus($data)
    {
        $app = App::get();
        if (!isset($data['threadID'])) {
            throw new \Exception('');
        }
        if (!isset($data['commentID'])) {
            throw new \Exception('');
        }
        if (!isset($data['status'])) {
            throw new \Exception('');
        }
        \BearCMS\Internal\Data\Comments::setStatus($data['threadID'], $data['commentID'], $data['status']);
        return true;
    }

    /**
     * 
     * @return array
     */
    static function deleteCommentForever($data)
    {
        $app = App::get();
        if (!isset($data['threadID'])) {
            throw new \Exception('');
        }
        if (!isset($data['commentID'])) {
            throw new \Exception('');
        }
        \BearCMS\Internal\Data\Comments::deleteCommentForever($data['threadID'], $data['commentID']);
        return true;
    }

    /**
     * 
     * @param array $data
     * @return array
     * @throws \Exception
     */
    static function addon($data)
    {
        $app = App::get();
        if (!isset($data['id'])) {
            throw new \Exception('');
        }

        if (\BearFramework\Addons::exists($data['id'])) {
            $addonData = [];
            $addonData['id'] = $data['id'];

            $result = $app->data->get([
                'key' => 'bearcms/addons/addon/' . md5($data['id']) . '.json',
                'result' => ['key', 'body']
            ]);
            if (isset($result['body'])) {
                $temp = json_decode($result['body'], true);
                $addonData['enabled'] = isset($temp['enabled']) ? (int) $temp['enabled'] > 0 : false;
                $optionsValues = isset($temp['options']) ? $temp['options'] : [];
            } else {
                $addonData['enabled'] = false;
                $optionsValues = [];
            }

            $includeOptions = isset($data['includeOptions']) && !empty($data['includeOptions']);
            $addonManifestData = \BearCMS\Internal\Data\Addons::getManifestData($data['id']);
            if (is_array($addonManifestData)) {
                $addonData['hasOptions'] = isset($addonManifestData['options']) && !empty($addonManifestData['options']);
                if ($includeOptions) {
                    $addonData['options'] = [];
                    $addonData['options']['definition'] = isset($addonManifestData['options']) ? $addonManifestData['options'] : [];
                    $addonData['options']['values'] = $optionsValues;
                    $addonData['options']['valid'] = \BearCMS\Internal\Data\Addons::validateOptions($addonData['options']['definition'], $addonData['options']['values']);
                }
                unset($addonManifestData['options']);
                $addonData = array_merge($addonData, $addonManifestData);
            } else {
                $addonData['hasOptions'] = false;
                if ($includeOptions) {
                    $addonData['options'] = [];
                    $addonData['options']['definition'] = [];
                    $addonData['options']['values'] = [];
                    $addonData['options']['valid'] = true;
                }
            }
            return $addonData;
        }
        return null;
    }

    static function addAddon($data)
    {
        $app = App::get();
        if (isset($data['type']) && isset($data['value'])) {
            $filenameOrUrl = null;
            if ($data['type'] === 'url') {
                $filenameOrUrl = $data['value'];
            } elseif ($data['type'] === 'file') {
                $filename = $app->data->getFilename('.temp/bearcms/files/' . $data['value']);
                if (is_file($filename)) {
                    $filenameOrUrl = $filename;
                }
            }
            try {
                $id = $app->maintenance->addons->getID($filenameOrUrl);
            } catch (\Exception $e) {
                return ['error' => 'invalidValue'];
            }
            if (\BearFramework\Addons::exists($id)) {
                $result = $app->data->get([
                    'key' => 'bearcms/addons/addon/' . md5($id) . '.json',
                    'result' => ['key']
                ]);
                if (!isset($result['key'])) { // Not managed by Bear CMS
                    return ['error' => 'notManagedByBearCMS'];
                }
            }
            try {
                $context = $app->getContext(__FILE__);
                $id = $app->maintenance->addons->install($context->options['addonsDir'], $filenameOrUrl);
                return $id;
            } catch (\Exception $e) {
                return ['error' => 'invalidValue'];
            }
        }
        return null;
    }

    static function deleteAddon($data)
    {
        $app = App::get();
        if (!isset($data['id'])) {
            throw new \Exception('');
        }
        $context = $app->getContext(__FILE__);
        $app->maintenance->addons->delete($context->options['addonsDir'], $data['id']);
    }

    static function mail($data)
    {
        $app = App::get();
        try {
            $result = mail($data['recipient'], $data['subject'], $data['body']);
        } catch (\Exception $e) {
            $result = false;
        }
        $app->logger->log('info', json_encode(['message' => $data, 'result' => (int) $result]));
        return $result;
    }

    static function iconChanged()
    {
        Cookies::setList(Cookies::TYPE_CLIENT, [['name' => 'fc', 'value' => uniqid(), 'expire' => time() + 86400 + 1000]]);
    }

    /**
     * 
     * @param array $data
     * @throws \Exception
     */
    static function publishData($data)
    {
        $app = App::get();
        if (!isset($data['key'])) {
            throw new \Exception('');
        }
        $app->data->makePublic(['key' => $data['key']]);
    }

    /**
     * 
     * @param array $data
     * @throws \Exception
     */
    static function fileSet($data)
    {
        $app = App::get();
        if (!isset($data['filename'])) {
            throw new \Exception('');
        }
        if (!isset($data['data'])) {
            throw new \Exception('');
        }
        $fileData = $data['data'];
        $currentFileData = self::file(['filename' => $data['filename']]);
        if (isset($fileData['name']) && $currentFileData['name'] !== $fileData['name']) {
            $updateKey = function($key) {
                $originalKey = $key;
                $key = preg_replace('/[^a-z0-9\.\-\_]+/u', '-', strtolower($key));
                while (strpos($key, '--') !== false) {
                    $key = str_replace('--', '-', $key);
                }
                $key = trim($key, '-');
                $info = pathinfo($key);
                $info['filename'] = trim($info['filename'], '-');
                if (strlen($info['filename']) === 0) {
                    $info['filename'] = md5($originalKey);
                }
                if (strlen($key) > 80) {
                    $info['filename'] = substr($info['filename'], 0, 80);
                }
                $key = $info['filename'] . (isset($info['extension']) ? '.' . $info['extension'] : '');
                return $key;
            };
            $sourceKey = 'bearcms/files/custom/' . $updateKey($data['filename']);
            $targetKey = 'bearcms/files/custom/' . $updateKey($fileData['name']);
            if ($sourceKey !== $targetKey && is_file($app->data->getFilename($sourceKey))) {
                if (is_file($app->data->getFilename($targetKey))) {
                    $info = pathinfo($targetKey);
                    if (isset($info['extension'])) {
                        $targetKeyPrefix = substr($targetKey, 0, strlen($targetKey) - strlen($info['extension']) - 1);
                    } else {
                        $targetKeyPrefix = $targetKey;
                    }
                    $done = false;
                    for ($i = 1; $i < 9999999; $i++) {
                        $tempTargetKey = $targetKeyPrefix . '_' . $i . (isset($info['extension']) ? '.' . $info['extension'] : '');
                        if (!is_file($app->data->getFilename($tempTargetKey))) {
                            $targetKey = $tempTargetKey;
                            $done = true;
                            break;
                        }
                    }
                    if (!$done) {
                        throw new \Exception('Cannot find available filename for ' . $targetKey);
                    }
                }
                $app->data->rename([
                    'sourceKey' => $sourceKey,
                    'targetKey' => $targetKey
                ]);
                $data['filename'] = str_replace('bearcms/files/custom/', '', $targetKey);
            }
        }
        $setData = [
            'key' => 'bearcms/files/custom/' . $data['filename']
        ];
        if (isset($fileData['name'])) {
            $setData['metadata.name'] = (string) $fileData['name'];
        }
        if (isset($fileData['published'])) {
            $setData['metadata.published'] = (string) $fileData['published'];
        }
        $app->data->set($setData);
    }

    /**
     * 
     * @param array $data
     * @throws \Exception
     */
    static function file($data)
    {
        $app = App::get();
        if (!isset($data['filename'])) {
            throw new \Exception('');
        }
        $item = $app->data->get(
                [
                    'key' => 'bearcms/files/custom/' . $data['filename'],
                    'result' => ['key', 'metadata']
                ]
        );
        if (isset($item['key'])) {
            $key = $item['key'];
            $fullFilename = $app->data->getFilename($key);
            $result = [
                'filename' => str_replace('bearcms/files/custom/', '', $key),
                'name' => (isset($item['metadata.name']) ? $item['metadata.name'] : str_replace('bearcms/files/custom/', '', $key)),
                'published' => (isset($item['metadata.published']) ? (int) $item['metadata.published'] : 0),
                'size' => filesize($fullFilename),
                'dateUploaded' => filemtime($fullFilename)
            ];
            return $result;
        }
        return null;
    }

    /**
     * 
     * @param array $data
     * @return string
     * @throws \Exception
     */
    static function dataUrl($data)
    {
        $app = App::get();
        if (!isset($data['key'])) {
            throw new \Exception('');
        }
        if (!isset($data['options'])) {
            throw new \Exception('');
        }
        return $app->assets->getUrl($app->data->getFilename($data['key']), $data['options']);
    }

    /**
     * 
     * @param array $data
     * @return string
     * @throws \Exception
     */
    static function appAssetUrl($data)
    {
        $app = App::get();
        if (!isset($data['key'])) {
            throw new \Exception('');
        }
        if (!isset($data['options'])) {
            throw new \Exception('');
        }
        return $app->assets->getUrl($app->config->appDir . DIRECTORY_SEPARATOR . $data['key'], $data['options']);
    }

    /**
     * 
     * @param array $data
     * @return string
     * @throws \Exception
     */
    static function addonAssetUrl($data)
    {
        $app = App::get();
        if (!isset($data['key'])) {
            throw new \Exception('');
        }
        if (!isset($data['options'])) {
            throw new \Exception('');
        }
        if (!isset($data['addonID'])) {
            throw new \Exception('');
        }
        $addonDir = \BearFramework\Addons::get($data['addonID'])['dir'];
        return $app->assets->getUrl($addonDir . DIRECTORY_SEPARATOR . $data['key'], $data['options']);
    }

    /**
     * 
     * @param array $data
     * @return string
     * @throws \Exception
     */
    static function assetUrl($data)
    {
        $app = App::get();
        if (!isset($data['filename'])) {
            throw new \Exception('');
        }
        if (!isset($data['options'])) {
            throw new \Exception('');
        }
        return $app->assets->getUrl($data['filename'], $data['options']);
    }

    /**
     * 
     * @param array $data
     * @param array $response
     * @throws \Exception
     */
    static function temporaryRedirect($data, $response)
    {
        $app = App::get();
        if (!isset($data['url'])) {
            throw new \Exception('');
        }
        Cookies::setList(Cookies::TYPE_SERVER, Cookies::parseServerCookies($response['header']));
        $response = new App\Response\TemporaryRedirect($data['url']);
        Cookies::update($response);
        $app->respond($response);
        exit;
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    static function data($data)
    {
        $app = App::get();
        return $app->data->execute($data);
    }

    static function replaceContent($data, $response)
    {
        $app = App::get();
        $body = $response['body'];
        $content = $app->components->process($data['content']);
        $domDocument = new \IvoPetkov\HTML5DOMDocument();
        $domDocument->loadHTML($content);
        $bodyElement = $domDocument->querySelector('body');
        $content = $bodyElement->innerHTML;
        $bodyElement->parentNode->removeChild($bodyElement);
        $allButBody = $domDocument->saveHTML();
        $startPosition = strpos($body, '{bearcms-replace-content-' . $data['id'] . '-');
        if ($startPosition === false) {
            return;
        }

        $endPosition = strpos($body, '}', $startPosition);

        $modificationsString = substr($body, $startPosition + 58, $endPosition - $startPosition - 58);
        $parts = explode('\'', $modificationsString);
        $singleQuoteSlashesCount = strlen($parts[0]);
        $doubleQuoteSlashesCount = strlen($parts[1]) - 1;
        for ($i = 0; $i < $doubleQuoteSlashesCount; $i+=2) {
            $content = substr(json_encode($content), 1, -1);
        }
        for ($i = 0; $i < $singleQuoteSlashesCount; $i+=2) {
            $content = addslashes($content);
        }
        $body = str_replace(substr($body, $startPosition, $endPosition - $startPosition + 1), $content, $body);
        //todo optimize
        $response1 = ['js' => 'html5DOMDocument.insert(' . json_encode($allButBody, true) . ');'];
        $response2 = json_decode($body, true);
        $response['body'] = json_encode(Server::mergeAjaxResponses($response1, $response2));
    }

    static function evalHTML($data, $response)
    {
        $response1 = json_decode($response['body'], true);
        $response2 = ['js' => 'var e=document.querySelector(\'#' . $data['elementID'] . '\');if(e){html5DOMDocument.evalElement(e);}'];
        $response['body'] = json_encode(Server::mergeAjaxResponses($response1, $response2));
    }

    static function elementsEditor($data, $response)
    {
        if (!empty(ElementsHelper::$editorData)) {
            $requestArguments = [];
            $requestArguments['data'] = json_encode(ElementsHelper::$editorData);
            $requestArguments['jsMode'] = 1;
            $elementsEditorData = Server::call('elementseditor', $requestArguments, true);
            if (is_array($elementsEditorData) && isset($elementsEditorData['result'], $elementsEditorData['result']['content'])) {
                $response['body'] = json_encode(Server::mergeAjaxResponses(json_decode($response['body'], true), json_decode($elementsEditorData['result']['content'], true)));
            } else {
                throw new \Exception('');
            }
        }
    }

    static function dataSchema($data)
    {
        if (!isset($data['id'])) {
            return [];
        }
        $app = App::get();
        $dataSchema = new \BearCMS\DataSchema($data['id']);
        $app->hooks->execute('bearCMSDataSchemaRequested', $dataSchema);
        return $dataSchema->fields;
    }

    /**
     * 
     * @param array $data
     * @param array $response
     * @throws \Exception
     */
    static function checkpoint($data)
    {
        return $data;
    }

}
