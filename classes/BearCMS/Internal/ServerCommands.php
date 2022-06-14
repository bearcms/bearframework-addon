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
use BearCMS\Internal\Data\BlogPosts as InternalDataBlogPosts;
use BearCMS\Internal\Data\Elements as InternalDataElements;
use BearCMS\Internal\Data\Pages as InternalDataPages;
use BearCMS\Internal\Data\Comments as InternalDataComments;
use BearCMS\Internal\Data\Settings as InternalDataSettings;
use BearCMS\Internal\Pages as InternalPages;
use BearCMS\Internal\Blog as InternalBlog;
use BearCMS\Internal\Data\UploadsSize;

/**
 * @internal
 * @codeCoverageIgnore
 */
class ServerCommands
{

    static $external = [];
    static $cache = [];

    /**
     * 
     * @param string $name
     * @param callable $callable
     * @return void
     */
    static function add(string $name, callable $callable): void
    {
        self::$external[$name] = $callable;
    }

    /**
     * 
     * @return array
     */
    static function about(): array
    {
        $result = [];
        if (Config::$appSecretKey !== null && strlen(Config::$appSecretKey) > 0) {
            $temp = explode('-', Config::$appSecretKey);
            $result['appID'] = $temp[0];
        }
        $result['phpVersion'] = phpversion();
        return $result;
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    static function addonAdd(array $data): array
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
            return [];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * 
     * @param array $data
     * @return string
     */
    static function addonAssetUrl(array $data): string
    {
        $app = App::get();
        $addonDir = \BearFramework\Addons::get($data['addonID'])->dir;
        return $app->assets->getURL($addonDir . '/' . $data['key'], $data['options']);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function addonDelete(array $data): void
    {
        Internal\Data\Addons::delete($data['id']);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function addonDisable(array $data): void
    {
        Internal\Data\Addons::disable($data['id']);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function addonEnable(array $data): void
    {
        Internal\Data\Addons::enable($data['id']);
    }

    /**
     * 
     * @param array $data
     * @return array|null
     */
    static function addonGet(array $data): ?array
    {
        $addon = Internal\Data\Addons::get($data['id']);
        if ($addon !== null) {
            return $addon->toArray();
        }
        return null;
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function addonSetOptions(array $data): void
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

    /**
     * 
     * @return array
     */
    static function addonsList(): array
    {
        return Internal\Data\Addons::getList()->toArray();
    }

    /**
     * 
     * @param array $data
     * @return string
     */
    static function assetUrl(array $data): string
    {
        $app = App::get();
        return $app->assets->getURL($data['filename'], $data['options']);
    }

    /**
     * 
     * @return array
     */
    static function blogCategories(): array
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

    static function blogPostsGet(array $data): ?array
    {
        $app = App::get();
        $blogPost = $app->bearCMS->data->blogPosts->get($data['id']);
        if ($blogPost !== null) {
            return $blogPost->toArray();
        }
        return null;
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function blogPostsSet(array $data): void
    {
        $app = App::get();
        $blogPostID = $data['id'];
        InternalDataBlogPosts::set($blogPostID, $data['data']);
        $app->addEventListener('sendResponse', function () use ($app, $blogPostID) {
            $blogPost = $app->bearCMS->data->blogPosts->get($blogPostID);
            if ($blogPost !== null) {
                Sitemap::addUpdateDateTask($blogPost->getURLPath());
            }
            if (Config::hasFeature('COMMENTS')) {
                InternalBlog::addUpdateCommentsLocationsTask($blogPostID);
            }
        });
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function blogPostsDelete(array $data): void
    {
        $blogPostID = $data['id'];
        InternalDataBlogPosts::deleteImage($blogPostID, false);
        InternalDataBlogPosts::delete($blogPostID);
        ElementsHelper::deleteContainer('bearcms-blogpost-' . $blogPostID);
    }

    /**
     * 
     * @return array
     */
    static function blogPostsList(): array
    {
        $app = App::get();
        return $app->bearCMS->data->blogPosts->getList()->toArray();
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    static function checkpoint(array $data): array
    {
        return $data;
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function commentDelete(array $data): void
    {
        InternalDataComments::deleteComment($data['threadID'], $data['commentID']);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function commentSetStatus(array $data): void
    {
        InternalDataComments::setStatus($data['threadID'], $data['commentID'], $data['status']);
    }

    /**
     * 
     * @param array $data
     * @return int
     */
    static function commentsCount(array $data): int
    {
        $result = InternalDataComments::getList();
        if ($data['type'] !== 'all') {
            $result->filterBy('status', $data['type']);
        }
        return $result->count();
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    static function commentsList(array $data): array
    {
        $result = InternalDataComments::getList();
        $result->sortBy('createdTime', 'desc');
        if ($data['type'] !== 'all') {
            $result->filterBy('status', $data['type']);
        }
        $result = $result->slice($data['limit'] * ($data['page'] - 1), $data['limit']);
        foreach ($result as $i => $comment) {
            $comment = InternalDataComments::setCommentLocation($comment);
            $comment = InternalDataComments::updateCommentAuthor($comment);
            $result[$i] = $comment;
        }
        return $result->toArray();
    }

    /**
     * 
     * @param array $data
     * @return array|null
     */
    static function commentsGet(array $data): ?array
    {
        $comment = Internal2::$data2->comments->get($data['threadID'], $data['commentID']);
        if ($comment !== null) {
            $comment = InternalDataComments::setCommentLocation($comment);
            $comment = InternalDataComments::updateCommentAuthor($comment);
            return $comment->toArray();
        }
        return null;
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    static function data(array $data): array
    {
        $result = [];
        $app = App::get();

        $validateKey = function ($key) {
            if (strpos($key, 'bearcms/') !== 0 && strpos($key, '.temp/bearcms/') !== 0 && strpos($key, '.recyclebin/bearcms/') !== 0 && strpos($key, 'bearcms-store/') !== 0 && strpos($key, '.temp/bearcms-store/') !== 0 && strpos($key, '.recyclebin/bearcms-store/') !== 0) {
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
            } elseif ($command === 'delete') {
                $validateKey($commandData['key']);
                if ($app->data->exists($commandData['key'])) {
                    $app->data->delete($commandData['key']);
                }
            } elseif ($command === 'rename') {
                $validateKey($commandData['sourceKey']);
                $validateKey($commandData['targetKey']);
                $silent = isset($commandData['silent']) ? (int) $commandData['silent'] > 0 : false;
                $updateUploadsSize = isset($commandData['updateUploadsSize']) ? (int) $commandData['updateUploadsSize'] > 0 : false;
                $removeUploadsSize = isset($commandData['removeUploadsSize']) ? (int) $commandData['removeUploadsSize'] > 0 : false;
                try {
                    $app->data->rename($commandData['sourceKey'], $commandData['targetKey']);
                    if ($updateUploadsSize || $removeUploadsSize) {
                        if ($updateUploadsSize) {
                            $size = (int)UploadsSize::getItemSize($commandData['sourceKey']);
                            if ($size > 0) {
                                UploadsSize::add($commandData['targetKey'], $size);
                            }
                        }
                        UploadsSize::remove($commandData['sourceKey']);
                    }
                } catch (\Exception $e) {
                    if (!$silent) {
                        throw $e;
                    }
                }
            } elseif ($command === 'duplicate') {
                $validateKey($commandData['sourceKey']);
                $validateKey($commandData['targetKey']);
                $silent = isset($commandData['silent']) ? (int) $commandData['silent'] > 0 : false;
                $updateUploadsSize = isset($commandData['updateUploadsSize']) ? (int) $commandData['updateUploadsSize'] > 0 : false;
                try {
                    $app->data->duplicate($commandData['sourceKey'], $commandData['targetKey']);
                    if ($updateUploadsSize) {
                        $size = (int)UploadsSize::getItemSize($commandData['sourceKey']);
                        if ($size > 0) {
                            UploadsSize::add($commandData['targetKey'], $size);
                        }
                    }
                } catch (\Exception $e) {
                    if (!$silent) {
                        throw $e;
                    }
                }
            } elseif ($command === 'makePublic') {
            } elseif ($command === 'makePrivate') {
            }
            $result[] = $commandResult;
        }
        return $result;
    }

    /**
     * 
     * @param array $data
     * @return int
     */
    static function dataFileSize(array $data): int
    {
        $app = App::get();
        $filename = $app->data->getFilename($data['key']);
        if (is_file($filename)) {
            return filesize($filename);
        }
        return 0;
    }

    /**
     * 
     * @param array $data
     * @return string
     */
    static function dataUrl(array $data): string
    {
        $app = App::get();
        return $app->assets->getURL($app->data->getFilename($data['key']), $data['options']);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function elementSet(array $data): void
    {
        $elementID = $data['id'];
        $containerID = isset($data['containerID']) ? $data['containerID'] : null;
        InternalDataElements::setElement($elementID, $data['data'], $containerID);
        InternalDataElements::optimizeElement($elementID, $containerID);
        InternalDataElements::dispatchElementChangeEvent($elementID, $containerID);
    }

    /**
     * 
     * @param array $data
     * @return array|null
     */
    static function elementGet(array $data): ?array
    {
        //$containerID = isset($data['containerID']) ? $data['containerID'] : null;
        return InternalDataElements::getElement($data['id']);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function elementDelete(array $data): void
    {
        $elementID = $data['id'];
        $containerID = isset($data['containerID']) ? $data['containerID'] : null;
        ElementsHelper::deleteElement($elementID, $containerID);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function elementCopy(array $data): void
    {
        $sourceElementID = $data['sourceID'];
        $targetElementID = $data['targetID'];
        $sourceContainerID = isset($data['sourceContainerID']) ? $data['sourceContainerID'] : null;
        $targetContainerID = isset($data['targetContainerID']) ? $data['targetContainerID'] : null;
        ElementsHelper::copyElement($sourceElementID, $targetElementID, $sourceContainerID, $targetContainerID);
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    static function elementsUploadsSize(array $data): array
    {
        $result = [];
        $result['size'] = 0;
        $elementsIDs = $data['ids'];
        foreach ($elementsIDs as $elementID) {
            $result['size'] += ElementsHelper::getElementUploadsSize($elementID);
        }
        return $result;
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    static function elementsContainersUploadsSize(array $data): array
    {
        $result = [];
        $result['size'] = 0;
        $containersIDs = $data['ids'];
        foreach ($containersIDs as $containerID) {
            $result['size'] += ElementsHelper::getContainerUploadsSize($containerID);
        }
        return $result;
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function elementsContainerSet(array $data): void
    {
        $containerID = $data['id'];
        InternalDataElements::setContainer($containerID, $data['data']);
        InternalDataElements::dispatchContainerChangeEvent($containerID);
    }

    /**
     * 
     * @param array $data
     * @return array|null
     */
    static function elementsContainerGet(array $data): ?array
    {
        return InternalDataElements::getContainer($data['id']);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function elementsContainerDelete(array $data): void
    {
        ElementsHelper::deleteContainer($data['id']);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function elementsContainerCopy(array $data): void
    {
        ElementsHelper::copyContainer($data['sourceID'], $data['targetID']);
    }

    /**
     * 
     * @param array $data
     * @param \ArrayObject $response
     * @return void
     * @throws \Exception
     */
    static function elementsEditor(array $data, \ArrayObject $response): void
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
                throw new \Exception('');
            }
        }
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    static function elementStyleGet(array $data): array
    {
        $result = [];
        $containerID = isset($data['containerID']) ? $data['containerID'] : null;
        $elementID = isset($data['elementID']) ? $data['elementID'] : null;
        $styleOptions = ElementsHelper::getElementStyleOptions($containerID, $elementID);
        if ($styleOptions !== null) {
            list($options, $values, $themeID, $themeOptionsSelectors, $elementType) = $styleOptions;
            $result['options'] = [];
            $result['options']['definition'] = Internal\Themes::optionsToArray($options);
            $result['options']['values'] = $values;
            $result['options']['themeID'] = $themeID;
            $result['options']['themeOptionsSelectors'] = $themeOptionsSelectors;
            $result['options']['elementType'] = $elementType;
        }
        return $result;
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function elementStyleSet(array $data): void
    {
        $containerID = isset($data['containerID']) ? $data['containerID'] : null;
        $elementID = isset($data['elementID']) ? $data['elementID'] : null;
        $value = isset($data['value']) ? $data['value'] : null;
        ElementsHelper::setElementStyleValues($containerID, $elementID, $value);
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    static function elementsCombinationsGetList(array $data): array
    {
        return ElementsCombinations::getList();
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    static function elementsCombinationGet(array $data): array
    {
        $combinationData = ElementsCombinations::get($data['id']);
        $result = $combinationData;
        return $result;
    }

    /**
     * 
     * @param array $data
     * @param \ArrayObject $response
     * @return void
     */
    static function evalHTML(array $data, \ArrayObject $response): void
    {
        $response1 = $response['value'];
        $response2 = ['js' => 'var e=document.querySelector(\'#' . $data['elementID'] . '\');if(e){clientPackages.get(\'html5DOMDocument\').then(function(html5DOMDocument){html5DOMDocument.evalElement(e);});}'];
        $response['value'] = Internal\Server::mergeAjaxResponses($response1, $response2);
    }

    /**
     * 
     * @param array $data
     * @return array|null
     */
    static function file(array $data): ?array
    {
        $app = App::get();
        $prefix = 'bearcms/files/custom/';
        $item = $app->data->get($prefix . $data['filename']);
        if ($item !== null) {
            $key = $item->key;
            $fullFilename = $app->data->getFilename($key);
            $result = [
                'filename' => str_replace($prefix, '', $key),
                'name' => (isset($item->metadata['name']) ? $item->metadata['name'] : str_replace($prefix, '', $key)),
                'published' => (isset($item->metadata['published']) ? (int) $item->metadata['published'] : 0),
                'size' => filesize($fullFilename),
                'dateCreated' => (isset($item->metadata['dateCreated']) ? (string) $item->metadata['dateCreated'] : ''),
            ];
            return $result;
        }
        return null;
    }

    /**
     * 
     * @param array $data
     * @return void
     * @throws \Exception
     */
    static function fileSet(array $data): void
    {
        $app = App::get();
        $fileData = $data['data'];
        $key = 'bearcms/files/custom/' . $data['filename'];
        if (isset($fileData['name'])) {
            $app->data->setMetadata($key, 'name', (string) $fileData['name']);
        }
        if (isset($fileData['published'])) {
            $app->data->setMetadata($key, 'published', (string) $fileData['published']);
        }
        if (isset($fileData['dateCreated'])) {
            $app->data->setMetadata($key, 'dateCreated', (string) $fileData['dateCreated']);
        }
    }

    /**
     * 
     * @return array
     */
    static function files(): array
    {
        $app = App::get();
        $prefix = 'bearcms/files/custom/';
        $result = $app->data->getList()
            ->filterBy('key', $prefix, 'startWith');
        $temp = [];
        foreach ($result as $item) {
            $key = $item->key;
            $temp[] = [
                'filename' => str_replace($prefix, '', $key),
                'name' => (isset($item->metadata['name']) ? $item->metadata['name'] : str_replace($prefix, '', $key)),
                'published' => (isset($item->metadata['published']) ? (int) $item->metadata['published'] : 0)
            ];
        }
        return $temp;
    }

    /**
     * 
     * @return void
     */
    static function iconChanged(): void
    {
        Internal\Cookies::setList(Internal\Cookies::TYPE_CLIENT, [['name' => 'fc', 'value' => uniqid(), 'expire' => time() + 86400 + 1000]]);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function pagesSet(array $data): void
    {
        $app = App::get();
        $pageID = $data['id'];
        InternalDataPages::set($pageID, $data['data']);
        if (isset($data['isNew']) && (int)$data['isNew'] > 0) {
            InternalPages::createNewPageHeadingElement($pageID);
        }
        $app->addEventListener('sendResponse', function () use ($app, $pageID) {
            $page = $app->bearCMS->data->pages->get($pageID);
            if ($page !== null) {
                Sitemap::addUpdateDateTask($page->path);
            }
            if (Config::hasFeature('COMMENTS')) {
                InternalPages::addUpdateCommentsLocationsTask($pageID);
            }
        });
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function pagesDelete(array $data): void
    {
        $pageID = $data['id'];
        InternalDataPages::deleteImage($pageID, false);
        InternalDataPages::delete($pageID);
        ElementsHelper::deleteContainer('bearcms-page-' . $pageID);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function pagesSetStructure(array $data): void
    {
        InternalDataPages::setStructure($data['data']);
    }

    /**
     * 
     * @return array
     */
    static function pagesList(): array
    {
        $list = Internal\Data::getList('bearcms/pages/page/');
        $structure = Internal\Data::getValue('bearcms/pages/structure.json');
        $temp = [];
        $temp['structure'] = $structure !== null ? json_decode($structure, true) : [];
        $temp['pages'] = [];
        $homeIsFound = false;
        foreach ($list as $value) {
            $pageData = json_decode($value, true);
            if (isset($pageData['id']) && $pageData['id'] === 'home') {
                $homeIsFound = true;
            }
            $temp['pages'][] = $pageData;
        }
        if (!$homeIsFound) {
            if (Config::$autoCreateHomePage) {
                $defaultHomePage = InternalDataPages::getDefaultHomePage();
                $temp['pages'][] = $defaultHomePage->toArray();
            }
        }
        return $temp;
    }

    /**
     * 
     * @param array $data
     * @param \ArrayObject $response
     * @return void
     */
    static function replaceContent(array $data, \ArrayObject $response): void
    {
        $app = App::get();
        $value = json_encode($response['value']);
        $content = $app->components->process($data['content']);
        $content = $app->clientPackages->process($content);
        // $domDocument = new HTML5DOMDocument();
        // $domDocument->loadHTML($content, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
        // $bodyElement = $domDocument->querySelector('body');
        // $content = $bodyElement->innerHTML;
        // $bodyElement->parentNode->removeChild($bodyElement);
        // $allButBody = $app->clientPackages->process($domDocument->saveHTML());
        $prefix = '{bearcms-replace-content-' . $data['id'] . '-';
        $startPosition = strpos($value, $prefix);
        if ($startPosition === false) {
            return;
        }
        $prefixLength = strlen($prefix);
        $endPosition = strpos($value, '}', $startPosition);

        $modificationsString = substr($value, $startPosition + $prefixLength, $endPosition - $startPosition - $prefixLength);
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
        // $response1 = ['js' => 'clientPackages.get(\'html5DOMDocument\').then(function(html5DOMDocument){html5DOMDocument.insert(' . json_encode($allButBody, true) . ');});'];
        // $response2 = json_decode($value, true);
        // $response['value'] = Internal\Server::mergeAjaxResponses($response1, $response2);
        $response['value'] = json_decode($value, true);
    }

    /**
     * 
     * @return array
     */
    static function settingsGet(): array
    {
        $app = App::get();
        return $app->bearCMS->data->settings->get()->toArray();
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function settingsSet(array $data): void
    {
        $app = App::get();
        $settings = \BearCMS\Data\Settings\Settings::fromArray($data['data']);
        $app->bearCMS->data->settings->set($settings);
        InternalDataSettings::updateIconsDetails();
    }

    /**
     * 
     * @param array $data
     * @param \ArrayObject $response
     */
    static function temporaryRedirect(array $data, \ArrayObject $response)
    {
        $app = App::get();
        Internal\Cookies::setList(Internal\Cookies::TYPE_SERVER, Internal\Cookies::parseServerCookies($response['headers']));
        $response = new App\Response\TemporaryRedirect($data['url']);
        Internal\Cookies::apply($response);
        $app->send($response);
        exit;
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function themeApplyUserValues(array $data): void
    {
        $themeID = $data['id'];
        $userID = $data['userID'];
        Internal\Themes::applyUserValues($themeID, $userID);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function themeDiscardUserOptions(array $data): void
    {
        $themeID = $data['id'];
        $userID = $data['userID'];
        if (strlen($themeID) > 0 && strlen($userID) > 0) {
            Internal2::$data2->themes->discardUserOptions($themeID, $userID);
        }
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    static function themeExport(array $data): array
    {
        $app = App::get();
        $themeID = $data['id'];
        $dataKey = Internal\Themes::export($themeID);
        //$app->data->makePublic($dataKey);
        return ['downloadUrl' => $app->assets->getURL($app->data->getFilename($dataKey), ['download' => true])];
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    static function themeGet(array $data): ?array
    {
        $app = App::get();
        $themeID = $data['id'];
        $includeOptions = isset($data['includeOptions']) && !empty($data['includeOptions']);
        $themes = Internal\Themes::getIDs();
        foreach ($themes as $id) {
            if ($id === $themeID) {
                Localization::setAdminLocale();
                $optionsAsArray = Internal\Themes::getOptionsAsArray($id);
                $themeManifest = Internal\Themes::getManifest($id);
                $themeData = $themeManifest;
                $themeData['id'] = $id;
                $themeData['hasOptions'] = !empty($optionsAsArray);
                $themeData['stylesCount'] = sizeof(Internal\Themes::getStyles($id));
                if ($includeOptions) {
                    $themeData['options'] = [
                        'definition' => $optionsAsArray
                    ];
                    $themeData['options']['activeValues'] = Internal2::$data2->themes->getValues($id);
                    $themeData['options']['currentUserValues'] = Internal2::$data2->themes->getUserOptions($id, $app->bearCMS->currentUser->getID());
                }
                Localization::restoreLocale();
                return $themeData;
            }
        }
        return null;
    }

    /**
     * 
     * @return string
     */
    static function themeGetActive(): string
    {
        return Internal\Themes::getActiveThemeID();
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function themeSetActive(array $data): void
    {
        Internal\Themes::setActiveThemeID($data['id']);
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    static function themeImport(array $data): array
    {
        $sourceDataKey = $data['sourceDataKey'];
        $themeID = $data['id'];
        $userID = $data['userID'];
        try {
            Internal\Themes::import($sourceDataKey, $themeID, $userID);
            return ['status' => 'ok'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'errorCode' => $e->getCode()];
        }
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function themeSetUserOptions(array $data): void
    {
        $themeID = $data['id'];
        $userID = $data['userID'];
        $values = $data['values'];
        Internal2::$data2->themes->setUserOptions($themeID, $userID, $values);
    }

    /**
     * 
     * @param array $data
     * @return array|null
     */
    static function themeStylesGet(array $data): ?array
    {
        $themeID = $data['id'];
        return Internal\Themes::getStyles($themeID, true);
    }

    /**
     * 
     * @return array
     */
    static function themesList(): array
    {
        Localization::setAdminLocale();
        $themes = Internal\Themes::getIDs();
        $result = [];
        foreach ($themes as $id) {
            $themeManifest = Internal\Themes::getManifest($id);
            $themeData = $themeManifest;
            $themeData['id'] = $id;
            $themeData['hasOptions'] = Internal\Themes::getOptions($id) !== null;
            $themeData['stylesCount'] = sizeof(Internal\Themes::getStyles($id));
            $result[] = $themeData;
        }
        Localization::restoreLocale();
        return $result;
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function uploadsSizeAdd(array $data): void
    {
        UploadsSize::add($data['key'], (int) $data['size']);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function uploadsSizeRemove(array $data): void
    {
        UploadsSize::remove($data['key']);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function uploadsSizeGet(array $data): ?int
    {
        return UploadsSize::getItemSize($data['key']);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function fileCopy(array $data): void
    {
        $source = $data['source'];
        $target = $data['target'];
        if (strpos($target, 'appdata://') !== 0) {
            throw new \Exception('Cannot copy to file outside the data directory! (' . $target . ')'); // security purposes
        }
        if (is_file($target)) {
            throw new \Exception('Target file already exists! (' . $target . ')');
        }
        if (is_file($source)) {
            copy($source, $target);
        } else {
            throw new \Exception('Source file not found! (' . $source . ')');
        }
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    static function filesSize(array $data): array
    {
        $result = [];
        $result['size'] = 0;
        $result['files'] = [];
        $files = $data['files'];
        foreach ($files as $filename) {
            $size = filesize($filename);
            $result['size'] += $size;
            $result['files'][$filename] = $size;
        }
        return $result;
    }

    /**
     * 
     * @param array $data
     * @return string|null
     */
    static function userIDByEmail(array $data): ?string
    {
        $app = App::get();
        $email = (string) $data['email'];
        $users = $app->bearCMS->data->users->getList();
        foreach ($users as $user) {
            if (array_search($email, $user->emails) !== false) {
                return $user->id;
            }
        }
        return null;
    }

    /**
     * 
     * @return array
     */
    static function usersIDs(): array
    {
        $app = App::get();
        $users = $app->bearCMS->data->users->getList();
        $result = [];
        foreach ($users as $user) {
            $result[] = $user->id;
        }
        return $result;
    }

    /**
     * 
     * @return array
     */
    static function usersInvitations(): array
    {
        $userInvitations = Internal2::$data2->usersInvitations->getList();
        $result = [];
        foreach ($userInvitations as $userInvitation) {
            $result[] = $userInvitation->toArray();
        }
        return $result;
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function updateConfig(array $data): void
    {
        $manager = Config::getConfigManager();
        foreach ($data as $name => $value) {
            if (array_search($name, ['whitelabel']) !== false) {
                $manager->setConfigValue($name, $value);
            }
        }
    }

    /**
     * 
     * @param array $data
     * @return string
     */
    static function getGoogleFontURL(array $data): string
    {
        $app = App::get();
        return $app->googleFontsEmbed->getURL($data['name']);
    }

    /**
     * 
     * @param object $list
     * @param array $modifications
     * @return void
     */
    static function applyListModifications($list, array $modifications)
    {
        $result = $list;
        foreach ($modifications as $modification) {
            if ($modification[0] === 'filterBy') {
                $result = $result->filterBy($modification[1], $modification[2], isset($modification[3]) ? $modification[3] : 'equal');
            } elseif ($modification[0] === 'sortBy') {
                $result = $result->sortBy($modification[1], $modification[2]);
            } elseif ($modification[0] === 'sliceProperties') {
                $result = $result->sliceProperties($modification[1]);
            } elseif ($modification[0] === 'slice') {
                $result = $result->slice($modification[1], $modification[2]);
            }
        }
        return $result;
    }

    /**
     * 
     * @param array $data
     * @return array|null
     */
    static function userProfileGetData(array $data): ?array
    {
        $app = App::get();
        $providerID = $data['providerID'];
        $userID = $data['id'];
        $imageSize = isset($data['imageSize']) && is_numeric($data['imageSize']) ? (int)$data['imageSize'] : null;
        $user = $app->users->getUser($providerID, $userID);
        $result = [];
        $result['data'] = $app->users->getUserData($providerID, $userID);
        $result['name'] = $user->name;
        $result['imageURL'] = $imageSize !== null ? $user->getImageUrl($imageSize) : null;
        return $result;
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function userProfileSetData(array $data): void
    {
        $app = App::get();
        $providerID = $data['providerID'];
        $userID = $data['id'];
        $userData = $app->users->getUserData($providerID, $userID);
        if (empty($userData)) {
            $userData = [];
        }
        foreach ($data['data'] as $key => $value) {
            $userData[$key] = $value;
        }
        $app->users->saveUserData($providerID, $userID, $userData);
    }

    /**
     * 
     * @param array $data
     * @return string
     */
    static function userProfileUserFileSet(array $data): string
    {
        $app = App::get();
        $providerID = $data['providerID'];
        $sourceFileName = $data['source'];
        $extension = strtolower(pathinfo($sourceFileName, PATHINFO_EXTENSION));
        return $app->users->saveUserFile($providerID, $sourceFileName, $extension);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function userProfileUserFileDelete(array $data): void
    {
        $app = App::get();
        $providerID = $data['providerID'];
        $fileKey = $data['key'];
        $app->users->deleteUserFile($providerID, $fileKey);
    }
}
