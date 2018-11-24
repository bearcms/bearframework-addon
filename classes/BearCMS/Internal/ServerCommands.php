<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;
use BearCMS\Internal;
use BearCMS\Internal\Config;
use BearCMS\Internal2;

class ServerCommands
{

    static function about()
    {
        $result = [];
        if (strlen(Config::$appSecretKey) > 0) {
            $temp = explode('-', Config::$appSecretKey);
            $result['appID'] = $temp[0];
        }
        $result['phpVersion'] = phpversion();
        return $result;
    }

    static function addonAdd(array $data)
    {
        try {
            Internal\Data\Addons::add($data['id']);
            if ($data['enabled'] !== null) {
                if ($data['enabled']) {
                    Internal\Data\Addons::enable($data['id']);
                } else {
                    Internal\Data\Addons::disable($data['id']);
                }
            }
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
        return null;
    }

    static function addonAssetUrl(array $data)
    {
        $app = App::get();
        if (!isset($data['key'])) {
            throw new Exception('');
        }
        if (!isset($data['options'])) {
            throw new Exception('');
        }
        if (!isset($data['addonID'])) {
            throw new Exception('');
        }
        $addonDir = \BearFramework\Addons::get($data['addonID'])->dir;
        return $app->assets->getUrl($addonDir . DIRECTORY_SEPARATOR . $data['key'], $data['options']);
    }

    static function addonDelete(array $data)
    {
        Internal\Data\Addons::delete($data['id']);
    }

    static function addonDisable(array $data)
    {
        Internal\Data\Addons::disable($data['id']);
    }

    static function addonEnable(array $data)
    {
        Internal\Data\Addons::enable($data['id']);
    }

    static function addonGet(array $data)
    {
        $addon = Internal\Data\Addons::get($data['id']);
        if ($addon !== null) {
            return $addon->toArray();
        }
        return null;
    }

    static function addonSetOptions(array $data)
    {
        Internal\Data\Addons::setOptions($data['id'], $data['options']);
        if ($data['enabled'] !== null) {
            if ($data['enabled']) {
                Internal\Data\Addons::enable($data['id']);
            } else {
                Internal\Data\Addons::disable($data['id']);
            }
        }
    }

    static function addonsList()
    {
        return Internal\Data\Addons::getList()->toArray();
    }

    static function appAssetUrl(array $data)
    {
        $app = App::get();
        if (!isset($data['key'])) {
            throw new Exception('');
        }
        if (!isset($data['options'])) {
            throw new Exception('');
        }
        return $app->assets->getUrl($app->config->appDir . DIRECTORY_SEPARATOR . $data['key'], $data['options']);
    }

    static function assetUrl(array $data)
    {
        $app = App::get();
        if (!isset($data['filename'])) {
            throw new Exception('');
        }
        if (!isset($data['options'])) {
            throw new Exception('');
        }
        return $app->assets->getUrl($data['filename'], $data['options']);
    }

    static function blogCategories()
    {
        $list = Internal\Data::getList('bearcms/blog/categories/category/');
        $structure = Internal\Data::getValue('bearcms/blog/categories/structure.json');
        $temp = [];
        $temp['structure'] = $structure !== null ? json_decode($structure, true) : [];
        $temp['categories'] = [];
        foreach ($list as $value) {
            $temp['categories'][] = json_decode($value, true);
        }
        return $temp;
    }

    static function blogPostsList()
    {
        $app = App::get();
        return Internal2::$data2->blogPosts->getList()->toArray();
    }

    static function checkpoint(array $data)
    {
        return $data;
    }

    static function commentDelete(array $data)
    {
        if (!isset($data['threadID'])) {
            throw new Exception('');
        }
        if (!isset($data['commentID'])) {
            throw new Exception('');
        }
        Internal\Data\Comments::deleteCommentForever($data['threadID'], $data['commentID']);
        return true;
    }

    static function commentSetStatus(array $data)
    {
        if (!isset($data['threadID'])) {
            throw new Exception('');
        }
        if (!isset($data['commentID'])) {
            throw new Exception('');
        }
        if (!isset($data['status'])) {
            throw new Exception('');
        }
        Internal\Data\Comments::setStatus($data['threadID'], $data['commentID'], $data['status']);
        return true;
    }

    static function commentsCount(array $data)
    {
        $app = App::get();
        if (!isset($data['type'])) {
            throw new Exception('');
        }
        $result = Internal2::$data2->comments->getList();
        if ($data['type'] !== 'all') {
            $result->filterBy('status', $data['type']);
        }
        return $result->length;
    }

    static function commentsList(array $data)
    {
        $app = App::get();
        if (!isset($data['type'])) {
            throw new Exception('');
        }
        if (!isset($data['page'])) {
            throw new Exception('');
        }
        if (!isset($data['limit'])) {
            throw new Exception('');
        }
        $result = Internal2::$data2->comments->getList();
        $result->sortBy('createdTime', 'desc');
        if ($data['type'] !== 'all') {
            $result->filterBy('status', $data['type']);
        }
        $result = $result->slice($data['limit'] * ($data['page'] - 1), $data['limit']);
        $locations = Internal\Data\Comments::getCommentsElementsLocations();
        foreach ($result as $i => $item) {
            if (isset($locations[$item->threadID])) {
                $result[$i]->location = $locations[$item->threadID];
            } else {
                $result[$i]->location = '';
            }
            $result[$i]->author = Internal\PublicProfile::getFromAuthor($item->author)->toArray();
        }
        return $result->toArray();
    }

    static function data(array $data)
    {
        $result = [];
        $app = App::get();

        $validateKey = function($key) {
            if (strpos($key, 'bearcms/') !== 0 && strpos($key, '.temp/bearcms/') !== 0 && strpos($key, '.recyclebin/bearcms/') !== 0) {
                throw new \Exception('The key ' . $key . ' is forbidden!');
            }
        };

        foreach ($data as $commandData) {
            $command = $commandData['command'];
            $commandResult = [];
            if ($command === 'get') {
                $validateKey($commandData['key']);
                $value = $app->data->getValue($commandData['key']);
                $commandResult['schemaVersion'] = 2;
                if ($value !== null) {
                    $commandResult['result'] = ['exists' => true, 'value' => $value];
                } else {
                    $commandResult['result'] = ['exists' => false];
                }
            } elseif ($command === 'set') {
                $validateKey($commandData['key']);
                $app->data->set($app->data->make($commandData['key'], $commandData['body']));
                Internal\Data::setChanged($commandData['key']);
            } elseif ($command === 'delete') {
                $validateKey($commandData['key']);
                if ($app->data->exists($commandData['key'])) {
                    $app->data->delete($commandData['key']);
                }
            } elseif ($command === 'rename') {
                $validateKey($commandData['sourceKey']);
                $validateKey($commandData['targetKey']);
                $app->data->rename($commandData['sourceKey'], $commandData['targetKey']);
            } elseif ($command === 'makePublic') {
                $validateKey($commandData['key']);
                $app->data->makePublic($commandData['key']);
            } elseif ($command === 'makePrivate') {
                $validateKey($commandData['key']);
                $app->data->makePrivate($commandData['key']);
            }
            $result[] = $commandResult;
        }
        return $result;
    }

    static function dataFileSize(array $data)
    {
        $app = App::get();
        $filename = $app->data->getFilename($data['key']);
        if (is_file($filename)) {
            return filesize($filename);
        }
        return 0;
    }

    static function dataSchema(array $data)
    {
        if (!isset($data['id'])) {
            return [];
        }
        $app = App::get();
        $dataSchema = new Internal\DataSchema($data['id']);
        $app->hooks->execute('bearCMSDataSchemaRequested', $dataSchema);
        return $dataSchema->fields;
    }

    static function dataUrl(array $data)
    {
        $app = App::get();
        if (!isset($data['key'])) {
            throw new Exception('');
        }
        if (!isset($data['options'])) {
            throw new Exception('');
        }
        return $app->assets->getUrl($app->data->getFilename($data['key']), $data['options']);
    }

    static function elementDelete(array $data)
    {
        $app = App::get();
        if (!isset($data['id'])) {
            throw new Exception('');
        }
        $elementID = $data['id'];
        $rawDataList = Internal\ElementsHelper::getElementsRawData([$elementID]);
        if ($rawDataList[$elementID] !== null) {
            $elementData = json_decode($rawDataList[$elementID], true);
            $app->data->delete('bearcms/elements/element/' . md5($elementID) . '.json');
            if (isset($elementData['type'])) {
                $componentName = array_search($elementData['type'], Internal\ElementsHelper::$elementsTypesCodes);
                $options = Internal\ElementsHelper::$elementsTypesOptions[$componentName];
                if (isset($options['onDelete']) && is_callable($options['onDelete'])) {
                    call_user_func($options['onDelete'], isset($elementData['data']) ? $elementData['data'] : []);
                }
            }
        }
    }

    static function elementsEditor(array $data, $response)
    {
        if (!empty(Internal\ElementsHelper::$editorData)) {
            $requestArguments = [];
            $requestArguments['data'] = json_encode(Internal\ElementsHelper::$editorData);
            $requestArguments['jsMode'] = 1;
            $elementsEditorData = Internal\Server::call('elementseditor', $requestArguments, true);
            if (is_array($elementsEditorData) && isset($elementsEditorData['result'], $elementsEditorData['result']['content'])) {
                $response['value'] = Internal\Server::mergeAjaxResponses($response['value'], json_decode($elementsEditorData['result']['content'], true));
                $response['value'] = Internal\Server::updateAssetsUrls($response['value'], true);
            } else {
                throw new Exception('');
            }
        }
    }

    static function evalHTML(array $data, App\Response $response)
    {
        $response1 = $response['value'];
        $response2 = ['js' => 'var e=document.querySelector(\'#' . $data['elementID'] . '\');if(e){html5DOMDocument.evalElement(e);}'];
        $response['value'] = Internal\Server::mergeAjaxResponses($response1, $response2);
    }

    static function file(array $data)
    {
        $app = App::get();
        if (!isset($data['filename'])) {
            throw new Exception('');
        }
        $item = $app->data->get('bearcms/files/custom/' . $data['filename']);
        if ($item !== null) {
            $key = $item->key;
            $fullFilename = $app->data->getFilename($key);
            $result = [
                'filename' => str_replace('bearcms/files/custom/', '', $key),
                'name' => (isset($item->metadata->name) ? $item->metadata->name : str_replace('bearcms/files/custom/', '', $key)),
                'published' => (isset($item->metadata->published) ? (int) $item->metadata->published : 0),
                'size' => filesize($fullFilename),
                'dateUploaded' => filemtime($fullFilename)
            ];
            return $result;
        }
        return null;
    }

    static function fileSet(array $data)
    {
        $app = App::get();
        if (!isset($data['filename'])) {
            throw new Exception('');
        }
        if (!isset($data['data'])) {
            throw new Exception('');
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
                        throw new Exception('Cannot find available filename for ' . $targetKey);
                    }
                }
                $app->data->rename($sourceKey, $targetKey);
                $data['filename'] = str_replace('bearcms/files/custom/', '', $targetKey);
            }
        }
        $key = 'bearcms/files/custom/' . $data['filename'];
        if (isset($fileData['name'])) {
            $app->data->setMetadata($key, 'name', (string) $fileData['name']);
        }
        if (isset($fileData['published'])) {
            $app->data->setMetadata($key, 'published', (string) $fileData['published']);
        }
    }

    static function files()
    {
        $app = App::get();
        $result = $app->data->getList()
                ->filterBy('key', 'bearcms/files/custom/', 'startWith');
        $temp = [];
        foreach ($result as $item) {
            $key = $item->key;
            $temp[] = [
                'filename' => str_replace('bearcms/files/custom/', '', $key),
                'name' => (isset($item->metadata->name) ? $item->metadata->name : str_replace('bearcms/files/custom/', '', $key)),
                'published' => (isset($item->metadata->published) ? (int) $item->metadata->published : 0)
            ];
        }
        return $temp;
    }

    static function forumCategories()
    {
        $list = Internal\Data::getList('bearcms/forums/categories/category/');
        $structure = Internal\Data::getValue('bearcms/forums/categories/structure.json');
        $temp = [];
        $temp['structure'] = $structure !== null ? json_decode($structure, true) : [];
        $temp['categories'] = [];
        foreach ($list as $value) {
            $temp['categories'][] = json_decode($value, true);
        }
        return $temp;
    }

    static function forumPostGet(array $data)
    {
        $app = App::get();
        if (!isset($data['forumPostID'])) {
            throw new Exception('');
        }
        $result = Internal2::$data2->forumPosts->get($data['forumPostID']);
        $result->author = Internal\PublicProfile::getFromAuthor($result->author)->toArray();
        $result->replies = new \BearCMS\DataList();
        return $result->toArray();
    }

    static function forumPostReplyDelete(array $data)
    {
        if (!isset($data['forumPostID'])) {
            throw new Exception('');
        }
        if (!isset($data['replyID'])) {
            throw new Exception('');
        }
        Internal\Data\ForumPostsReplies::deleteReplyForever($data['forumPostID'], $data['replyID']);
        return true;
    }

    static function forumPostReplySetStatus(array $data)
    {
        if (!isset($data['forumPostID'])) {
            throw new Exception('');
        }
        if (!isset($data['replyID'])) {
            throw new Exception('');
        }
        if (!isset($data['status'])) {
            throw new Exception('');
        }
        Internal\Data\ForumPostsReplies::setStatus($data['forumPostID'], $data['replyID'], $data['status']);
        return true;
    }

    static function forumPostSetStatus(array $data)
    {
        if (!isset($data['forumPostID'])) {
            throw new Exception('');
        }
        if (!isset($data['status'])) {
            throw new Exception('');
        }
        Internal\Data\ForumPosts::setStatus($data['forumPostID'], $data['status']);
        return true;
    }

    static function forumPostsCount(array $data)
    {
        $app = App::get();
        if (!isset($data['type'])) {
            throw new Exception('');
        }
        $result = Internal2::$data2->forumPosts->getList();
        if ($data['type'] !== 'all') {
            $result->filterBy('status', $data['type']);
        }
        return $result->length;
    }

    static function forumPostsList(array $data)
    {
        $app = App::get();
        if (!isset($data['type'])) {
            throw new Exception('');
        }
        if (!isset($data['page'])) {
            throw new Exception('');
        }
        if (!isset($data['limit'])) {
            throw new Exception('');
        }
        $result = Internal2::$data2->forumPosts->getList();
        $result->sortBy('createdTime', 'desc');
        if ($data['type'] !== 'all') {
            $result->filterBy('status', $data['type']);
        }
        $result = $result->slice($data['limit'] * ($data['page'] - 1), $data['limit']);
        foreach ($result as $i => $item) {
            $result[$i]->location = '';
            $result[$i]->author = Internal\PublicProfile::getFromAuthor($item->author)->toArray();
        }
        return $result->toArray();
    }

    static function forumPostsRepliesCount(array $data)
    {
        $app = App::get();
        if (!isset($data['type'])) {
            throw new Exception('');
        }
        $result = Internal2::$data2->forumPostsReplies->getList();
        if (isset($data['forumPostID']) && strlen($data['forumPostID']) > 0) {
            $result->filterBy('forumPostID', $data['forumPostID']);
        }
        if ($data['type'] !== 'all') {
            $result->filterBy('status', $data['type']);
        }
        return $result->length;
    }

    static function forumPostsRepliesList(array $data)
    {
        $app = App::get();
        if (!isset($data['type'])) {
            throw new Exception('');
        }
        if (!isset($data['page'])) {
            throw new Exception('');
        }
        if (!isset($data['limit'])) {
            throw new Exception('');
        }
        $result = Internal2::$data2->forumPostsReplies->getList();
        $result->sortBy('createdTime', 'desc');
        if (isset($data['forumPostID']) && strlen($data['forumPostID']) > 0) {
            $result->filterBy('forumPostID', $data['forumPostID']);
        }
        if ($data['type'] !== 'all') {
            $result->filterBy('status', $data['type']);
        }
        $result = $result->slice($data['limit'] * ($data['page'] - 1), $data['limit']);
        foreach ($result as $i => $item) {
            $result[$i]->location = '';
            $result[$i]->author = Internal\PublicProfile::getFromAuthor($item->author)->toArray();
        }
        return $result->toArray();
    }

    static function iconChanged()
    {
        Internal\Cookies::setList(Internal\Cookies::TYPE_CLIENT, [['name' => 'fc', 'value' => uniqid(), 'expire' => time() + 86400 + 1000]]);
    }

    static function mail(array $data)
    {
        $app = App::get();

        $defaultEmailSender = Config::$defaultEmailSender;
        if (!is_array($defaultEmailSender)) {
            throw new \Exception('The defaultEmailSender option is empty.');
        }
        $email = $app->emails->make();
        $email->sender->email = $defaultEmailSender['email'];
        $email->sender->name = $defaultEmailSender['name'];
        $email->subject = $data['subject'];
        $email->content->add($data['body']);
        $email->recipients->add($data['recipient']);
        $app->emails->send($email);
        return 1;
    }

    static function pagesList()
    {
        $list = Internal\Data::getList('bearcms/pages/page/');
        $structure = Internal\Data::getValue('bearcms/pages/structure.json');
        $temp = [];
        $temp['structure'] = $structure !== null ? json_decode($structure, true) : [];
        $temp['pages'] = [];
        foreach ($list as $value) {
            $temp['pages'][] = json_decode($value, true);
        }
        return $temp;
    }

    static function replaceContent($data, $response)
    {
        $app = App::get();
        $value = json_encode($response['value']);
        $content = $app->components->process($data['content']);
        $domDocument = new \IvoPetkov\HTML5DOMDocument();
        $domDocument->loadHTML($content);
        $bodyElement = $domDocument->querySelector('body');
        $content = $bodyElement->innerHTML;
        $bodyElement->parentNode->removeChild($bodyElement);
        $allButBody = $domDocument->saveHTML();
        $startPosition = strpos($value, '{bearcms-replace-content-' . $data['id'] . '-');
        if ($startPosition === false) {
            return;
        }

        $endPosition = strpos($value, '}', $startPosition);

        $modificationsString = substr($value, $startPosition + 58, $endPosition - $startPosition - 58);
        $parts = explode('\'', $modificationsString);
        $singleQuoteSlashesCount = strlen($parts[0]);
        $doubleQuoteSlashesCount = strlen($parts[1]) - 1;
        for ($i = 0; $i < $doubleQuoteSlashesCount; $i += 2) {
            $content = substr(json_encode($content), 1, -1);
        }
        for ($i = 0; $i < $singleQuoteSlashesCount; $i += 2) {
            $content = addslashes($content);
        }
        $value = str_replace(substr($value, $startPosition, $endPosition - $startPosition + 1), $content, $value);
        //todo optimize
        $response1 = ['js' => 'html5DOMDocument.insert(' . json_encode($allButBody, true) . ');'];
        $response2 = json_decode($value, true);
        $response['value'] = Internal\Server::mergeAjaxResponses($response1, $response2);
    }

    static function settingsGet()
    {
        $app = App::get();
        $result = Internal2::$data2->settings->get();
        return $result->toArray();
    }

    static function temporaryRedirect($data, $response)
    {
        $app = App::get();
        if (!isset($data['url'])) {
            throw new Exception('');
        }
        Internal\Cookies::setList(Internal\Cookies::TYPE_SERVER, Internal\Cookies::parseServerCookies($response['headers']));
        $response = new App\Response\TemporaryRedirect($data['url']);
        Internal\Cookies::apply($response);
        $app->respond($response);
        exit;
    }

    static function themeApplyUserValues(array $data)
    {
        $app = App::get();
        $themeID = $data['id'];
        $userID = $data['userID'];
        $app->bearCMS->themes->applyUserValues($themeID, $userID);
    }

    static function themeDiscardOptions(array $data)
    {
        $app = App::get();
        $themeID = $data['id'];
        Internal2::$data2->themes->discardOptions($themeID);
    }

    static function themeDiscardUserOptions(array $data)
    {
        $app = App::get();
        $themeID = $data['id'];
        $userID = $data['userID'];
        if (strlen($themeID) > 0 && strlen($userID) > 0) {
            Internal2::$data2->themes->discardUserOptions($themeID, $userID);
        }
    }

    static function themeExport(array $data)
    {
        $app = App::get();
        $themeID = $data['id'];
        $dataKey = $app->bearCMS->themes->export($themeID);
        $app->data->makePublic($dataKey);
        return ['downloadUrl' => $app->assets->getUrl($app->data->getFilename($dataKey), ['download' => true])];
    }

    static function themeGet(array $data)
    {
        $app = App::get();
        if (!isset($data['id'])) {
            throw new Exception('');
        }
        $themeID = $data['id'];

        $includeOptions = isset($data['includeOptions']) && !empty($data['includeOptions']);
        $themes = BearCMS\Internal\Themes::getList();
        foreach ($themes as $id) {
            if ($id === $themeID) {
                $options = BearCMS\Internal\Themes::getOptions($id);
                $themeManifest = BearCMS\Internal\Themes::getManifest($id);
                $themeData = $themeManifest;
                $themeData['id'] = $id;
                $themeData['hasOptions'] = sizeof($options) > 0;
                $themeData['hasStyles'] = sizeof(BearCMS\Internal\Themes::getStyles($id)) > 0;
                if ($includeOptions) {
                    $themeData['options'] = [
                        'definition' => $options
                    ];
                    $result = Internal\Data::getValue('bearcms/themes/theme/' . md5($id) . '.json');
                    if ($result !== null) {
                        $temp = json_decode($result, true);
                        $optionsValues = isset($temp['options']) ? $temp['options'] : [];
                    } else {
                        $optionsValues = [];
                    }
                    $themeData['options']['activeValues'] = $optionsValues;

                    $result = Internal\Data::getValue('.temp/bearcms/userthemeoptions/' . md5($app->bearCMS->currentUser->getID()) . '/' . md5($id) . '.json');
                    if ($result !== null) {
                        $temp = json_decode($result, true);
                        $optionsValues = isset($temp['options']) ? $temp['options'] : [];
                    } else {
                        $optionsValues = null;
                    }
                    $themeData['options']['currentUserValues'] = $optionsValues;
                }
                return $themeData;
            }
        }
        return null;
    }

    static function themeGetActive()
    {
        return \BearCMS\Internal\Themes::getActiveThemeID();
    }

    static function themeImport(array $data)
    {
        $app = App::get();
        $sourceDataKey = $data['sourceDataKey'];
        $themeID = $data['id'];
        $userID = $data['userID'];
        try {
            $app->bearCMS->themes->import($sourceDataKey, $themeID, $userID);
            return ['status' => 'ok'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'errorCode' => $e->getCode()];
        }
    }

    static function themeSetOptions(array $data)
    {
        $app = App::get();
        $themeID = $data['id'];
        $values = $data['values'];
        Internal2::$data2->themes->setOptions($themeID, $values);
    }

    static function themeSetUserOptions(array $data)
    {
        $app = App::get();
        $themeID = $data['id'];
        $userID = $data['userID'];
        $values = $data['values'];
        Internal2::$data2->themes->setUserOptions($themeID, $userID, $values);
    }

    static function themeStylesGet(array $data)
    {
        if (!isset($data['id'])) {
            throw new Exception('');
        }
        $themeID = $data['id'];

        $themes = BearCMS\Internal\Themes::getList();
        foreach ($themes as $id) {
            if ($id === $themeID) {
                $styles = BearCMS\Internal\Themes::getStyles($id, true);
                return $styles;
            }
        }
        return null;
    }

    static function themesList()
    {
        $themes = BearCMS\Internal\Themes::getList();
        $result = [];
        foreach ($themes as $id) {
            $themeManifest = BearCMS\Internal\Themes::getManifest($id);
            $themeData = $themeManifest;
            $themeData['id'] = $id;
            $themeData['hasOptions'] = sizeof(BearCMS\Internal\Themes::getOptions($id)) > 0;
            $result[] = $themeData;
        }
        return $result;
    }

    static function uploadsSizeAdd(array $data)
    {
        Internal\Data\UploadsSize::add($data['key'], $data['size']);
    }

    static function uploadsSizeRemove(array $data)
    {
        Internal\Data\UploadsSize::remove($data['key']);
    }

    static function userIDByEmail(array $data)
    {
        if (!isset($data['email'])) {
            throw new Exception('');
        }
        $email = (string) $data['email'];
        $app = App::get();
        $users = Internal2::$data2->users->getList();
        foreach ($users as $user) {
            if (array_search($email, $user->emails) !== false) {
                return $user->id;
            }
        }
        return null;
    }

    static function usersIDs()
    {
        $app = App::get();
        $users = Internal2::$data2->users->getList();
        $result = [];
        foreach ($users as $user) {
            $result[] = $user->id;
        }
        return $result;
    }

    static function usersInvitations()
    {
        $app = App::get();
        $userInvitations = Internal2::$data2->usersInvitations->getList();
        $result = [];
        foreach ($userInvitations as $userInvitation) {
            $result[] = $userInvitation->toArray();
        }
        return $result;
    }

}
