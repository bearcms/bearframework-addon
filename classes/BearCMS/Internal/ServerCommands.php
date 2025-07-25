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
use BearCMS\Internal\Data\ElementsSharedStyles;
use BearCMS\Internal\Data\UploadsSize;
use IvoPetkov\DataList;

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
     * @param array $data
     * @return string
     */
    static function serverAssetUrl(array $data): string
    {
        $app = App::get();
        $context = $app->contexts->get(__DIR__);
        $filename = Internal\Server::getAssetFilename($data['url']);
        return $context->assets->getURL($filename, $data['options']);
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
        $app->addEventListener('sendResponse', function () use ($app, $blogPostID): void {
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
        ElementsDataHelper::deleteContainer('bearcms-blogpost-' . $blogPostID);
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

        $validateKey = function ($key): void {
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
     * @return string
     */
    static function elementAdd(array $data): string
    {
        return ElementsDataHelper::addElement($data['type'], $data['data'], $data['containerID'], $data['target']);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function elementDataSet(array $data): void
    {
        $elementID = $data['id'];
        $containerID = isset($data['containerID']) ? $data['containerID'] : null;
        $createIfNotFound = isset($data['createIfNotFound']) && (int)$data['createIfNotFound'] > 0;
        $elementData = ElementsDataHelper::getElement($elementID, $containerID);
        if ($elementData === null) {
            if ($createIfNotFound) {
                $elementData = [];
                $elementData['id'] = $elementID;
                $elementData['type'] = $data['type'];
            } else {
                throw new \Exception('Cannot find element to set ' . print_r($data, true));
            }
        }
        if ($elementData['id'] !== $elementID) {
            throw new \Exception('IDs do not match ' . print_r($data, true) . print_r($elementData, true));
        }
        $elementData['data'] = $data['data'];
        if (empty($elementData['data'])) {
            unset($elementData['data']);
        }
        ElementsDataHelper::setElement($elementData, $containerID);
    }

    /**
     * 
     * @param array $data
     * @return array|null
     */
    static function elementDataGet(array $data): ?array
    {
        $elementData = ElementsDataHelper::getElement($data['id'], isset($data['containerID']) ? $data['containerID'] : null);
        if ($elementData === null) {
            return null;
        }
        return [
            'id' => $elementData['id'],
            'type' => $elementData['type'],
            'data' => isset($elementData['data']) ? $elementData['data'] : []
        ];
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function elementTagsSet(array $data): void
    {
        $elementID = $data['id'];
        $containerID = isset($data['containerID']) ? $data['containerID'] : null;
        $tags = isset($data['tags']) ? $data['tags'] : [];
        $elementData = ElementsDataHelper::getElement($elementID, $containerID);
        if ($elementData === null) {
            throw new \Exception('Cannot find element to set tags ' . print_r($data, true));
        }
        if ($elementData['id'] !== $elementID) {
            throw new \Exception('IDs do not match ' . print_r($data, true) . print_r($elementData, true));
        }
        $elementData['tags'] = $tags;
        if (empty($elementData['tags'])) {
            unset($elementData['tags']);
        }
        ElementsDataHelper::setElement($elementData, $containerID);
    }

    /**
     * 
     * @param array $data
     * @return array|null
     */
    static function elementTagsGet(array $data): ?array
    {
        $elementData = ElementsDataHelper::getElement($data['id'], isset($data['containerID']) ? $data['containerID'] : null);
        if ($elementData === null) {
            return null;
        }
        return isset($elementData['tags']) ? $elementData['tags'] : [];
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
        $moveToBin = isset($data['moveToBin']) ? $data['moveToBin'] : false;
        if ($moveToBin) {
            ElementsDataHelper::moveElementToSet('bin', $elementID, $containerID);
        } else {
            ElementsDataHelper::deleteElement($elementID, $containerID);
        }
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function elementArchive(array $data): void
    {
        $elementID = $data['id'];
        $containerID = isset($data['containerID']) ? $data['containerID'] : null;
        ElementsDataHelper::moveElementToSet('archive', $elementID, $containerID);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function elementMove(array $data): void
    {
        $elementID = isset($data['elementID']) ? $data['elementID'] : null;
        $sourceContainerID = isset($data['sourceContainerID']) ? $data['sourceContainerID'] : null;
        $targetContainerID = isset($data['targetContainerID']) ? $data['targetContainerID'] : null;
        $target = isset($data['target']) ? $data['target'] : null;
        ElementsDataHelper::moveElement($elementID, $sourceContainerID, $targetContainerID, $target);
    }

    /**
     * 
     * @param array $data
     * @return string
     */
    static function elementDuplicate(array $data)
    {
        $elementID = $data['elementID'];
        $containerID = $data['containerID'];
        return ElementsDataHelper::duplicateElement($elementID, $containerID);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function elementCopy(array $data): void // DEPRECATED
    {
        $sourceElementID = $data['sourceID'];
        $targetElementID = $data['targetID'];
        $sourceContainerID = isset($data['sourceContainerID']) ? $data['sourceContainerID'] : null;
        $targetContainerID = isset($data['targetContainerID']) ? $data['targetContainerID'] : null;
        ElementsDataHelper::duplicateElement($sourceElementID, $targetElementID, $sourceContainerID, $targetContainerID);
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    static function elementExport(array $data): array
    {
        $app = App::get();
        $elementID = $data['elementID'];
        $containerID = $data['containerID'];
        $filename = ImportExport::export([['type' => 'element', 'args' => ['elementID' => $elementID, 'containerID' => $containerID]]]);
        return ['downloadURL' => $app->assets->getURL($filename, ['download' => true])];
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
        foreach ($elementsIDs as $elementIDData) {
            if (is_array($elementIDData)) {
                $result['size'] += ElementsDataHelper::getElementUploadsSize($elementIDData['elementID'], isset($elementIDData['containerID']) ? $elementIDData['containerID'] : null);
            } else {
                $result['size'] += ElementsDataHelper::getElementUploadsSize($elementIDData); // deprecated
            }
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
            $result['size'] += ElementsDataHelper::getContainerUploadsSize($containerID);
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
        ElementsDataHelper::deleteContainer($data['id']);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function elementsContainerCopy(array $data): void // DEPRECATED
    {
        self::elementsContainerDuplicate($data);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function elementsContainerDuplicate(array $data): void
    {
        ElementsDataHelper::duplicateContainer($data['sourceID'], $data['targetID']);
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    static function elementsSetElementsList(array $data): array
    {
        $setID = $data['setID'];
        $includeDetails = isset($data['includeDetails']) ? $data['includeDetails'] : false;
        $items = ElementsDataHelper::getElementsSetData($setID);
        $result = [];
        foreach ($items as $item) {
            $elementID = $item['id'];
            $resultItem = [];
            $resultItem['id'] = $elementID;
            if ($includeDetails) {
                $elementData = ElementsDataHelper::getElement($elementID);
                $resultItem['type'] = $elementData !== null ? $elementData['type'] : null;
            }
            $result[] = $resultItem;
        }
        return ['items' => $result];
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function elementsSetMoveElementFromSet(array $data): void
    {
        ElementsDataHelper::moveElementFromSet($data['setID'], $data['elementID'], $data['containerID'], $data['target']);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function elementsSetDeleteElement(array $data): void
    {
        ElementsDataHelper::deleteElementFromSet($data['setID'], $data['elementID']);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function elementsSetDeleteAllElements(array $data): void
    {
        ElementsDataHelper::deleteAllElementsFromSet($data['setID']);
    }

    /**
     * 
     * @return array
     */
    static function elementsSharedContentList(): array
    {
        $setID = 'sharedContent';
        $items = ElementsDataHelper::getContainersSetData($setID);
        $result = [];
        foreach ($items as $item) {
            $contentID = $item['id'];
            $resultItem = [];
            $resultItem['id'] = $contentID;
            $result[] = $resultItem;
        }
        return ['items' => $result];
    }

    /**
     * 
     * @return void
     */
    static function elementsSharedContentAdd(): void
    {
        $setID = 'sharedContent';
        $prefix = 'bearcms-shared-content-';
        $contentID = str_replace($prefix, '', ElementsDataHelper::generateContainerID($prefix));
        ElementsDataHelper::addContainerToSet($setID, $contentID);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function elementsSharedContentDelete(array $data): void
    {
        $setID = 'sharedContent';
        ElementsDataHelper::deleteContainerFromSet($setID, $data['contentID']);
    }

    /**
     * 
     * @return array
     */
    static function modalsList(): array
    {
        $list = ElementsDataHelper::getModalsList();
        return ['items' => $list];
    }

    /**
     * 
     * @param array $data
     * @return array|null
     */
    static function modalsGet(array $data): ?array
    {
        return ElementsDataHelper::getModalData($data['id']);
    }

    /**
     * 
     * @return void
     */
    static function modalsSet(array $data): void
    {
        ElementsDataHelper::setModalData($data['id'], $data['data']);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function modalsDelete(array $data): void
    {
        ElementsDataHelper::deleteModalData($data['id']);
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
            $requestArguments['data'] = json_encode(Internal\ElementsHelper::$editorData, JSON_THROW_ON_ERROR);
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
     * @return array|null
     */
    static function elementStylesGet(array $data): ?array
    {
        $app = App::get();
        $result = [];

        $containerID = isset($data['containerID']) ? $data['containerID'] : null;
        $elementID = isset($data['elementID']) ? $data['elementID'] : null;
        $elementType = isset($data['elementType']) ? $data['elementType'] : null;
        $defaultOptionsValues = isset($data['defaultOptionsValues']) ? $data['defaultOptionsValues'] : null;
        $elementData = ElementsDataHelper::getElement($elementID, $containerID);
        $hasElementData = $elementData !== null;
        if ($hasElementData && $elementData['type'] !== $elementType) {
            throw new \Exception('Element types do not match! (' . $elementType . ', ' . print_r($elementData, true) . ')');
        }

        $elementStyleID = $hasElementData && isset($elementData['styleID']) ? $elementData['styleID'] : null;
        $elementStyleValue = $hasElementData && isset($elementData['style']) ? $elementData['style'] : null;
        $elementRealStyleID = ElementStylesHelper::getElementRealStyleID($elementStyleID, $elementStyleValue, ElementsDataHelper::getDefaultElementStyle($elementType));

        $result['styleID'] = $elementRealStyleID;

        $getOutputHTML = function ($values, $selector) use ($app, $elementType): string {
            $outputHTML = ElementsHelper::getStyleHTML($elementType, $values, $selector);
            $outputHTML = $app->components->process($outputHTML);
            $outputHTML = $app->clientPackages->process($outputHTML);
            return $outputHTML;
        };

        $styles = [];
        $styles[] = [
            'id' => 'custom',
            'className' => ElementStylesHelper::getElementStyleClassName($elementID, 'custom'),
            'outputHTML' => $getOutputHTML($elementStyleValue !== null ? $elementStyleValue : $defaultOptionsValues, ElementStylesHelper::getElementStyleSelector($elementID, 'custom')),
            'hasValues' => $elementStyleValue !== null
        ];
        $sharedStyles = ElementStylesHelper::getSharedStylesList($elementType);
        foreach ($sharedStyles as $sharedStyle) {
            $sharedStyleID = $sharedStyle['id'];
            $styles[] = [
                'id' => $sharedStyleID,
                'name' => $sharedStyle['name'],
                'className' => ElementStylesHelper::getElementStyleClassName($elementID, $sharedStyleID),
                'outputHTML' => $getOutputHTML(isset($sharedStyle['style']) ? $sharedStyle['style'] : [], ElementStylesHelper::getElementStyleSelector($elementID, $sharedStyleID))
            ];
        }
        if ($elementRealStyleID !== 'default') {
            $selectedFound = false;
            foreach ($styles as $styleData) {
                if ($styleData['id'] === $elementRealStyleID) {
                    $selectedFound = true;
                }
            }
            if (!$selectedFound) {
                $styles[] = [
                    'id' => $elementRealStyleID,
                    'name' => __('bearcms.elementStyle.UnknownStyle'),
                    'type' => 'unknown',
                    'className' => ElementStylesHelper::getElementStyleClassName($elementID, $elementRealStyleID)
                ];
            }
        }
        $result['styles'] = $styles;

        return $result;
    }

    /**
     * 
     * @param array $data
     * @return array|null
     */
    static function elementStyleOptionsGet(array $data): ?array
    {
        $containerID = isset($data['containerID']) ? $data['containerID'] : null;
        $elementID = isset($data['elementID']) ? $data['elementID'] : null;
        $styleID = isset($data['styleID']) ? $data['styleID'] : null;
        $elementType = isset($data['elementType']) ? $data['elementType'] : null;
        $elementData = ElementsDataHelper::getElement($elementID, $containerID);
        $hasElementData = $elementData !== null;
        if ($hasElementData && $elementData['type'] !== $elementType) {
            throw new \Exception('Element types do not match! (' . $elementType . ', ' . print_r($elementData, true) . ')');
        }
        $styleValues = [];
        if ($styleID === 'custom') {
            $styleValues = isset($elementData['style']) ? $elementData['style'] : [];
        } else {
            $sharedStyleData = ElementsSharedStyles::get($styleID);
            if (is_array($sharedStyleData) && isset($sharedStyleData['style'])) {
                $styleValues = $sharedStyleData['style'];
            }
        }
        return ElementStylesHelper::getOptions($elementType, $styleValues, ElementStylesHelper::getElementStyleSelector($elementID, $styleID));
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function elementStyleSetID(array $data): void
    {
        $containerID = isset($data['containerID']) ? $data['containerID'] : null;
        $elementID = $data['elementID'];
        $styleID = $data['styleID'];
        $defaultOptionsValues = isset($data['defaultOptionsValues']) ? $data['defaultOptionsValues'] : null;
        ElementStylesHelper::setElementStyleID($elementID, $containerID, $styleID);
        if ($styleID === 'custom' && !empty($defaultOptionsValues)) {
            $elementData = ElementsDataHelper::getElement($elementID, $containerID);
            if ($elementData !== null) {
                if (!isset($elementData['style']) || empty($elementData['style'])) {
                    ElementStylesHelper::setElementStyleValues($elementID, $containerID, $styleID, $defaultOptionsValues);
                }
            }
        }
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function elementStyleSetValues(array $data): void
    {
        $elementID = $data['elementID'];
        $containerID = isset($data['containerID']) ? $data['containerID'] : null;
        $styleID = $data['styleID'];
        $values = $data['values'];
        ElementStylesHelper::setElementStyleValues($elementID, $containerID, $styleID, $values);
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    static function elementStyleUploadsSize(array $data): array
    {
        $elementID = $data['elementID'];
        $containerID = isset($data['containerID']) ? $data['containerID'] : null;
        $elementData = ElementsDataHelper::getElement($elementID, $containerID);
        if ($elementData === null) {
            return 0;
        }
        $result = [];
        $result['size'] = UploadsSize::getItemsSize(ElementsDataHelper::getElementDataStyleUploadsSizeItems($elementData));
        return $result;
    }

    /**
     * 
     * @param array $data
     * @return array|null
     */
    static function elementsSharedStylesGet(array $data): ?array
    {
        return ElementsSharedStyles::get($data['id']);
    }

    /**
     * 
     * @param array $data
     * @return string
     */
    static function elementsSharedStylesAdd(array $data): string
    {
        return ElementStylesHelper::addSharedStyle($data['type'], $data['name'], $data['values']);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function elementsSharedStylesSetName(array $data): void
    {
        ElementStylesHelper::setSharedStyleName($data['id'], $data['name']);
    }

    /**
     * 
     * @param array $data
     * @return string
     */
    static function elementsSharedStylesDuplicate(array $data): string
    {
        return ElementStylesHelper::duplicateSharedStyle($data['id']);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function elementsSharedStylesDelete(array $data): void
    {
        ElementStylesHelper::deleteSharedStyle($data['id']);
    }

    /**
     * 
     * @param array $data
     * @return string
     */
    static function elementsSharedStylesCreateFromCustom(array $data): string
    {
        $elementID = $data['elementID'];
        $containerID = isset($data['containerID']) ? $data['containerID'] : null;
        return ElementStylesHelper::createSharedStyleFromCustom($elementID, $containerID);
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    static function elementsSharedStylesUploadsSize(array $data): array
    {
        $result = [];
        $result['size'] = UploadsSize::getItemsSize(ElementStylesHelper::getSharedStyleUploadsSizeItems($data['id']));
        return $result;
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    static function elementGetImportFromFileUploadsSize(array $data): array
    {
        if (isset($data['filename'])) {
            $filename = $data['filename'];
        } elseif (isset($data['path'])) {
            $filename = Server::download($data['path'], true);
        } else {
            throw new \Exception('Not supported!');
        }
        try {
            $size = ElementsDataHelper::getImportElementFromFileUploadsSize($filename);
        } catch (\Exception $e) {
            return ['error' => 1];
        }
        return ['size' => $size];
    }

    /**
     * 
     * @param array $data
     * @return string|null|array
     */
    static function elementImportFromFile(array $data)
    {
        if (isset($data['filename'])) {
            $filename = $data['filename'];
        } elseif (isset($data['path'])) {
            $filename = Server::download($data['path'], true);
        } else {
            throw new \Exception('Not supported!');
        }
        try {
            return ElementsDataHelper::importElementFromFile($filename, $data['containerID'], $data['target']);
        } catch (\Exception $e) {
            return ['error' => 1];
        }
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    static function elementsContainerGetImportFromFileUploadsSize(array $data): array
    {
        if (isset($data['filename'])) {
            $filename = $data['filename'];
        } elseif (isset($data['path'])) {
            $filename = Server::download($data['path'], true);
        } else {
            throw new \Exception('Not supported!');
        }
        try {
            $size = ElementsDataHelper::getImportElementsContainerFromFileUploadsSize($filename);
        } catch (\Exception $e) {
            return ['error' => 1];
        }
        return ['size' => $size];
    }

    /**
     * 
     * @param array $data
     * @return string|null|array
     */
    static function elementsContainerImportFromFile(array $data)
    {
        if (isset($data['filename'])) {
            $filename = $data['filename'];
        } elseif (isset($data['path'])) {
            $filename = Server::download($data['path'], true);
        } else {
            throw new \Exception('Not supported!');
        }
        $containerID = $data['containerID'];
        try {
            if (isset($data['skipIfExists']) && $data['skipIfExists']) {
                if (InternalDataElements::getContainer($containerID) !== null) {
                    return ['exists' => 1];
                }
            }
            return ElementsDataHelper::importElementsContainerFromFile($filename, $containerID);
        } catch (\Exception $e) {
            return ['error' => 1];
        }
    }

    /**
     * 
     * @param array $data
     * @return array|null
     */
    static function elementTypeGet(array $data): ?array
    {
        if (isset($data['type'])) {
            $elementType = $data['type'];
            $componentName = array_search($elementType, ElementsHelper::$elementsTypeComponents);
            if ($componentName !== false) {
                $elementTypeDefinition = ElementsHelper::$elementsTypeDefinitions[$componentName];
                return [
                    'name' => $elementTypeDefinition->name,
                    'properties' => $elementTypeDefinition->properties
                ];
            }
        }
        return null;
    }

    /**
     * DEPRECATED
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
                'note' => (isset($item->metadata['note']) ? (string) $item->metadata['note'] : ''),
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
        if (isset($fileData['note'])) {
            $app->data->setMetadata($key, 'note', (string) $fileData['note']);
        }
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    static function files(array $data): array
    {
        $app = App::get();
        $prefix = 'bearcms/files/custom/';
        $list = $app->data->getList()
            ->filterBy('key', $prefix, 'startWith')
            ->sliceProperties(['key', 'metadata']);
        $temp = [];
        foreach ($list as $item) {
            $key = $item->key;
            $temp[] = [
                'filename' => str_replace($prefix, '', $key),
                'name' => (isset($item->metadata['name']) ? $item->metadata['name'] : str_replace($prefix, '', $key)),
                'published' => (isset($item->metadata['published']) ? (int) $item->metadata['published'] : 0),
                'dateCreated' => (isset($item->metadata['dateCreated']) ? (string) $item->metadata['dateCreated'] : ''),
                'note' => (isset($item->metadata['note']) ? (string) $item->metadata['note'] : ''),
            ];
        }
        $result = new DataList($temp);
        if (isset($data['modifications'])) {
            $result = self::applyListModifications($result, $data['modifications']);
        }
        return $result->toArray();
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
        $app->addEventListener('sendResponse', function () use ($app, $pageID): void {
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
        ElementsDataHelper::deleteContainer('bearcms-page-' . $pageID);
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
        $value = json_encode($response['value'], JSON_THROW_ON_ERROR);
        $content = $app->components->process($data['content']);
        $content = $app->clientPackages->process($content);
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
            $content = substr(json_encode($content, JSON_THROW_ON_ERROR), 1, -1);
        }
        for ($i = 0; $i < $singleQuoteSlashesCount; $i += 2) {
            $content = addslashes($content);
        }
        $value = str_replace(substr($value, $startPosition, $endPosition - $startPosition + 1), $content, $value);
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
        $filename = Internal\Themes::export($themeID);
        return ['downloadUrl' => $app->assets->getURL($filename, ['download' => true])];
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
        $app = App::get();
        $sourceDataKey = $data['sourceDataKey'];
        $themeID = $data['id'];
        $userID = $data['userID'];
        try {
            Internal\Themes::import($app->data->getFilename($sourceDataKey), $themeID, $userID);
            return ['status' => 'ok'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'errorCode' => $e->getCode()];
        }
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    static function themeExtractExport(array $data): array
    {
        $tempFilename = Server::download($data['path'], true);
        try {
            $data = Internal\Themes::extractExport($tempFilename);
            return ['status' => 'ok', 'result' => $data];
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
        $result['imageURL'] = $imageSize !== null ? $user->getImageURL($imageSize) : null;
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

    /**
     * 
     * @param array $data
     * @return array
     */
    static function usersGetList(array $data): array
    {
        $app = App::get();
        $list = $app->users->getList();
        $list = self::applyListModifications($list, $data['modifications']);
        return $list->toArray();
    }

    /**
     * 
     * @param array $data
     * @return integer
     */
    static function usersGetCount(array $data): int
    {
        $app = App::get();
        $list = $app->users->getList();
        $list = self::applyListModifications($list, $data['modifications']);
        return $list->count();
    }

    /**
     * 
     * @param array $data
     * @return array|null
     */
    static function usersGet(array $data): ?array
    {
        $app = App::get();
        $order = $app->users->get($data['id']); // TODO
        return $order !== null ? $order->toArray() : null;
    }

    /**
     * 
     * @param array $data
     * @return boolean
     */
    static function usersExists(array $data): bool
    {
        $app = App::get();
        return $app->users->userExists($data['providerID'], $data['id']);
    }

    /**
     * 
     * @param array $data
     * @return string|null
     */
    static function dataExportGetURL(array $data): ?string
    {
        $type = $data['type'];
        $handler = $data['handler'];
        $options = $data['options'];
        if (array_search($type, ['pdf', 'xls', 'csv']) !== false && is_string($handler) && is_array($options)) {
            $app = App::get();
            $generateID = function () {
                return base_convert(md5(uniqid('', true)), 16, 36);
            };
            $filePrefix = '.temp/bearcms/data-export/';
            $id = null;
            for ($i = 0; $i < 1000; $i++) {
                $_id = $generateID();
                if (!$app->data->exists($filePrefix . $_id)) {
                    $id = $_id;
                    break;
                }
            }
            if ($id === null) {
                throw new \Exception('Too many retries!');
            }
            $converter = Config::getVariable('internalDataExportConverter');
            if ($converter !== null) {
                if ($type === 'pdf') {
                    $from = 'html';
                } else {
                    $from = 'json';
                }
                $result = \BearCMS\Internal\DataExport::getResult($from, $handler, $options);
                if ($result !== null) {
                    $content = $converter($from, $type, $result['value']);
                    if ($content !== null) {
                        $filename = $id . '.' . $result['filename'] . '.' . $type;
                        $app->data->setValue($filePrefix . $filename, $content);
                        return $app->urls->get('/-de/' . $filename);
                    }
                }
            }
        }

        return null;
    }
}
