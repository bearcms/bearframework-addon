<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearFramework\App;
use BearCMS\Internal\CommentsLocations as InternalCommentsLocations;
use BearCMS\Internal\PublicProfile as InternalPublicProfile;
use BearCMS\Internal\Config;
use BearCMS\Internal2;

/**
 * @internal
 * @codeCoverageIgnore
 */
class Comments
{

    static private $commentsListCache = null;
    static private $commentsLocationsCache = null;

    /**
     * 
     * @param string $threadID
     * @param array $author
     * @param string $text
     * @param string $status
     * @param array $files [['name'=>'', 'filename'=>''], ...]
     * @return void
     */
    static function add(string $threadID, array $author, string $text, string $status, array $files = []): void
    {
        $app = App::get();
        $dataKey = self::getThreadDataKey($threadID);
        $data = $app->data->getValue($dataKey);
        $data = $data !== null ? json_decode($data, true) : [];
        if (empty($data['id'])) {
            $data['id'] = $threadID;
        }
        if (empty($data['comments'])) {
            $data['comments'] = [];
        }
        $commentID = base_convert(md5(uniqid() . serialize($data['comments'])), 16, 36);
        $commendData = [
            'id' => $commentID,
            'status' => $status,
            'author' => $author,
            'text' => $text,
            'createdTime' => time()
        ];

        if (!empty($files)) {
            $temp = [];
            foreach ($files as $file) {
                if (is_file($file['filename'])) {
                    $fileDataKey = null;
                    $fileID = null;
                    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    for ($j = 0; $j < 1000; $j++) {
                        $_fileID = base_convert(md5(uniqid()), 16, 36) . (strlen($fileExtension) > 0 ? '.' . $fileExtension : '');
                        $_fileDataKey = self::getFileDataKey($threadID, $_fileID);
                        if (!$app->data->exists($_fileDataKey)) {
                            $fileDataKey = $_fileDataKey;
                            $fileID = $_fileID;
                            break;
                        }
                    }
                    if ($fileDataKey === null) {
                        throw new \Exception('Too many tries to generate filename!');
                    }
                    rename($file['filename'], $app->data->getFilename($fileDataKey));
                    $temp[] = ['id' => $fileID, 'name' => $file['name']];
                }
            }
            $commendData['files'] = $temp;
        }

        $data['comments'][] = $commendData;
        $app->data->set($app->data->make($dataKey, json_encode($data, JSON_THROW_ON_ERROR)));

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
        self::$commentsListCache = null;
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
        $dataKey = self::getThreadDataKey($threadID);
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
            $app->data->set($app->data->make($dataKey, json_encode($threadData, JSON_THROW_ON_ERROR)));
            self::$commentsListCache = null;
        }
    }

    /**
     * 
     * @param string $threadID
     * @return void
     */
    static function deleteThread(string $threadID): void
    {
        $app = App::get();
        $app->data->delete(self::getThreadDataKey($threadID));
        $prefix = 'bearcms/comments/files/' . md5($threadID) . '.';
        $list = $app->data->getList()
            ->filterBy('key', $prefix, 'startWith')
            ->sliceProperties(['key']);
        foreach ($list as $item) {
            $fileDataKey = $item->key;
            if (strpos($fileDataKey, $prefix) === 0) { // just in case
                $app->data->delete($fileDataKey);
            }
        }
    }

    /**
     * 
     * @param string $threadID
     * @param string $commentID
     * @return void
     */
    static function deleteComment(string $threadID, string $commentID): void
    {
        $app = App::get();
        $dataKey = self::getThreadDataKey($threadID);
        $data = $app->data->getValue($dataKey);
        $hasChange = false;
        $filesToDelete = [];
        if ($data !== null) {
            $threadData = json_decode($data, true);
            if (is_array($threadData['comments']) && isset($threadData['comments'])) {
                foreach ($threadData['comments'] as $i => $comment) {
                    if (isset($comment['id']) && $comment['id'] === $commentID) {
                        if (isset($comment['files']) && is_array($comment['files'])) {
                            foreach ($comment['files'] as $fileData) {
                                $filesToDelete[] = $fileData['id'];
                            }
                        }
                        unset($threadData['comments'][$i]);
                        $hasChange = true;
                        break;
                    }
                }
            }
        }
        if ($hasChange) {
            $threadData['comments'] = array_values($threadData['comments']);
            $app->data->set($app->data->make($dataKey, json_encode($threadData, JSON_THROW_ON_ERROR)));
        }
        foreach ($filesToDelete as $fileID) {
            $app->data->delete(self::getFileDataKey($threadID, $fileID));
        }
        self::$commentsListCache = null;
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
            $properties = ['id', 'status', 'author', 'text', 'createdTime', 'files'];
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
     * @return string
     */
    static function generateNewThreadID(): string
    {
        $app = App::get();
        for ($i = 0; $i < 100; $i++) {
            $threadID = base_convert(md5(uniqid()), 16, 36);
            $dataKey = self::getThreadDataKey($threadID);
            if (!$app->data->exists($dataKey)) {
                return $threadID;
            }
        }
        throw new \Exception('Too many retries');
    }

    /**
     * 
     * @param string $sourceThreadID
     * @param string $targetThreadID
     * @return void
     */
    static function copyThread(string $sourceThreadID, string $targetThreadID): void
    {
        $app = App::get();
        $sourceDataKey = self::getThreadDataKey($sourceThreadID);
        $targetDataKey = self::getThreadDataKey($targetThreadID);
        $data = $app->data->getValue($sourceDataKey);
        $data = $data !== null ? json_decode($data, true) : [];
        $newData = $data;
        $newData['id'] = $targetThreadID;
        if (empty($newData['comments'])) {
            $newData['comments'] = [];
        }
        foreach ($newData['comments'] as $index => $comment) {
            $newData['comments'][$index]['id'] = base_convert(md5(uniqid()), 16, 36) . 'cc';
            if (isset($comment['files']) && is_array($comment['files'])) {
                foreach ($comment['files'] as $fileData) {
                    $fileID = $fileData['id'];
                    $sourceFileDataKey = self::getFileDataKey($sourceThreadID, $fileID);
                    $targetFileDataKey = self::getFileDataKey($targetThreadID, $fileID);
                    $app->data->duplicate($sourceFileDataKey, $targetFileDataKey);
                }
            }
        }
        $app->data->setValue($targetDataKey, json_encode($newData, JSON_THROW_ON_ERROR));
    }

    /**
     * Retrieves a list of all comments
     * 
     * @return \IvoPetkov\DataList|\BearCMS\Internal\Data2\Comment[] List containing all comments data
     */
    static function getList()
    {
        if (self::$commentsListCache === null) {
            self::$commentsListCache = Internal2::$data2->comments->getList();
        }
        return clone (self::$commentsListCache);
    }

    /**
     * 
     * @param \BearCMS\Internal\Data2\Comment $comment
     * @return \BearCMS\Internal\Data2\Comment
     */
    static function setCommentLocation(\BearCMS\Internal\Data2\Comment $comment): \BearCMS\Internal\Data2\Comment
    {
        $app = App::get();
        if (self::$commentsLocationsCache === null) {
            self::$commentsLocationsCache = InternalCommentsLocations::get();
        }
        $locations = self::$commentsLocationsCache;
        if (isset($locations[$comment->threadID])) {
            $comment->location = $app->urls->get($locations[$comment->threadID]);
        } else {
            $comment->location = '';
        }
        return $comment;
    }

    /**
     * 
     * @param \BearCMS\Internal\Data2\Comment $comment
     * @return \BearCMS\Internal\Data2\Comment
     */
    static function updateCommentAuthor(\BearCMS\Internal\Data2\Comment $comment): \BearCMS\Internal\Data2\Comment
    {
        $comment->author = InternalPublicProfile::getFromAuthor($comment->author)->toArray();
        return $comment;
    }

    /**
     * 
     * @param string $threadID
     * @param string $fileID
     * @return string
     */
    static function getFileURL(string $threadID, string $fileID): string
    {
        $app = App::get();
        $context = $app->contexts->get(__DIR__);
        return $context->assets->getURL('/assets/c/' . $threadID . '/' . $fileID, ['cacheMaxAge' => 86400, 'robotsNoIndex' => true, 'download' => true]);
    }

    /**
     * 
     * @param string $path
     * @return string|null
     */
    static function getFilenameFromURL(string $path): ?string
    {
        $app = App::get();
        $parts = explode('/', str_replace('/assets/c/', '', $path));
        if (isset($parts[0], $parts[1])) {
            $threadID = $parts[0];
            $fileID = $parts[1];
            return $app->data->getFilename(self::getFileDataKey($threadID, $fileID));
        }
    }

    /**
     * 
     * @param string $threadID
     * @return string
     */
    static function getThreadDataKey(string $threadID): string
    {
        return 'bearcms/comments/thread/' . md5($threadID) . '.json';
    }

    /**
     * 
     * @param string $threadID
     * @param string $fileID
     * @return string
     */
    static function getFileDataKey(string $threadID, string $fileID): string
    {
        return 'bearcms/comments/files/' . md5($threadID) . '.' . $fileID;
    }
}
