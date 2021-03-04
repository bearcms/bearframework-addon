<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearFramework\App;
use BearCMS\Internal;
use BearCMS\Internal\Config;

/**
 * @internal
 * @codeCoverageIgnore
 */
class Comments
{

    /**
     * 
     * @param string $threadID
     * @param array $author
     * @param string $text
     * @param string $status
     * @return void
     */
    static function add(string $threadID, array $author, string $text, string $status): void
    {
        $app = App::get();
        $dataKey = 'bearcms/comments/thread/' . md5($threadID) . '.json';
        $data = $app->data->getValue($dataKey);
        $data = $data !== null ? json_decode($data, true) : [];
        if (empty($data['id'])) {
            $data['id'] = $threadID;
        }
        if (empty($data['comments'])) {
            $data['comments'] = [];
        }
        $commentID = md5(uniqid());
        $data['comments'][] = [
            'id' => $commentID,
            'status' => $status,
            'author' => $author,
            'text' => $text,
            'createdTime' => time()
        ];
        $app->data->set($app->data->make($dataKey, json_encode($data)));

        if (Config::hasFeature('NOTIFICATIONS')) {
            if (!$app->tasks->exists('bearcms-send-new-comment-notification')) {
                $app->tasks->add('bearcms-send-new-comment-notification', [
                    'threadID' => $threadID,
                    'commentID' => $commentID
                ], ['id' => 'bearcms-send-new-comment-notification']);
            }
        }
        $eventDetails = new \BearCMS\Internal\AddCommentEventDetails($threadID, $commentID);
        $app->bearCMS->dispatchEvent('internalAddComment', $eventDetails);
    }

    /**
     * 
     * @param string $threadID
     * @param string $commentID
     * @param string $status
     * @return void
     */
    static function setStatus(string $threadID, string $commentID, string $status): void
    {
        $app = App::get();
        $dataKey = 'bearcms/comments/thread/' . md5($threadID) . '.json';
        $data = $app->data->getValue($dataKey);
        $hasChange = false;
        if ($data !== null) {
            $threadData = json_decode($data, true);
            if (is_array($threadData['comments']) && isset($threadData['comments'])) {
                foreach ($threadData['comments'] as $i => $comment) {
                    if (isset($comment['id']) && $comment['id'] === $commentID) {
                        if (isset($comment['status']) && $comment['status'] === $status) {
                            break;
                        }
                        $comment['status'] = $status;
                        $threadData['comments'][$i] = $comment;
                        $hasChange = true;
                        break;
                    }
                }
            }
        }
        if ($hasChange) {
            $app->data->set($app->data->make($dataKey, json_encode($threadData)));
        }
    }

    /**
     * 
     * @param string $threadID
     * @param string $commentID
     */
    static function deleteCommentForever(string $threadID, string $commentID)
    {
        $app = App::get();
        $dataKey = 'bearcms/comments/thread/' . md5($threadID) . '.json';
        $data = $app->data->getValue($dataKey);
        $hasChange = false;
        if ($data !== null) {
            $threadData = json_decode($data, true);
            if (is_array($threadData['comments']) && isset($threadData['comments'])) {
                foreach ($threadData['comments'] as $i => $comment) {
                    if (isset($comment['id']) && $comment['id'] === $commentID) {
                        unset($threadData['comments'][$i]);
                        $hasChange = true;
                        break;
                    }
                }
            }
        }
        if ($hasChange) {
            $threadData['comments'] = array_values($threadData['comments']);
            $app->data->set($app->data->make($dataKey, json_encode($threadData)));
        }
    }

    /**
     * 
     * @param array $rawCommentsData
     * @param string $threadID
     * @return \IvoPetkov\DataList
     */
    static function createCommentsCollection(array $rawCommentsData, string $threadID): \IvoPetkov\DataList
    {
        $dataList = new \IvoPetkov\DataList();
        foreach ($rawCommentsData as $rawCommentData) {
            $comment = new \BearCMS\Internal\Data2\Comment();
            $properties = ['id', 'status', 'author', 'text', 'createdTime'];
            foreach ($properties as $property) {
                if (array_key_exists($property, $rawCommentData)) {
                    $comment->$property = $rawCommentData[$property];
                }
            }
            $comment->threadID = $threadID;
            $dataList[] = $comment;
        }
        return $dataList;
    }

    /**
     * 
     * @return array
     */
    static function getCommentsElementsLocations(): array
    {
        $app = App::get();
        $tempDataKey = '.temp/bearcms/comments-elements-locations';
        $result = $app->data->getValue($tempDataKey);
        if ($result !== null) {
            $result = json_decode($result, true);
        }
        if (!is_array($result)) {
            $result = [];

            $pages = $app->bearCMS->data->pages->getList();
            $walkPageElements = function ($pageID, $path) use ($app, &$result) {
                $url = null;
                $containerElementIDs = Internal\ElementsHelper::getContainerElementsIDs('bearcms-page-' . $pageID);
                $elementsRawData = Internal\ElementsHelper::getElementsRawData($containerElementIDs);
                foreach ($elementsRawData as $elementRawData) {
                    if ($elementRawData === null) {
                        continue;
                    }
                    $elementData = Internal\ElementsHelper::decodeElementRawData($elementRawData);
                    if (is_array($elementData) && $elementData['type'] === 'comments') {
                        if (isset($elementData['data']['threadID'])) {
                            if ($url === null) {
                                $url = $app->urls->get($path);
                            }
                            $result[$elementData['data']['threadID']] = $url;
                        }
                    }
                }
            };
            foreach ($pages as $page) {
                $walkPageElements($page->id, $page->path);
            }
            $blogPosts = $app->bearCMS->data->blogPosts->getList();
            foreach ($blogPosts as $blogPost) {
                $url = $app->urls->get(Config::$blogPagesPathPrefix . (strlen($blogPost->slug) === 0 ? '-' . $blogPost->id : $blogPost->slug) . '/');
                $threadID = 'bearcms-blogpost-' . $blogPost->id;
                $result[$threadID] = $url;
            }
            $app->data->setValue($tempDataKey, json_encode($result));
        }
        return $result;
    }

    static function generateNewThreadID()
    {
        $app = App::get();
        for ($i = 0; $i < 100; $i++) {
            $threadID = base_convert(md5(uniqid()), 16, 36);
            $dataKey = 'bearcms/comments/thread/' . md5($threadID) . '.json';
            if (!$app->data->exists($dataKey)) {
                return $threadID;
            }
        }
        throw new \Exception('Too many retries');
    }

    static function copyThread(string $sourceThreadID, string $targetThreadID)
    {
        $app = App::get();
        $dataKey = 'bearcms/comments/thread/' . md5($sourceThreadID) . '.json';
        $newDataKey = 'bearcms/comments/thread/' . md5($targetThreadID) . '.json';
        $data = $app->data->getValue($dataKey);
        $data = $data !== null ? json_decode($data, true) : [];
        $newData = $data;
        $newData['id'] = $targetThreadID;
        if (empty($newData['comments'])) {
            $newData['comments'] = [];
        }
        foreach ($newData['comments'] as $index => $comment) {
            $newData['comments'][$index]['id'] = base_convert(md5(uniqid()), 16, 36) . 'cc';
        }
        $app->data->setValue($newDataKey, json_encode($newData));
    }
}
