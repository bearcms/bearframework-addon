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
        $app->data->delete('bearcms/comments/thread/' . md5($threadID) . '.json');
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
     * @return string
     */
    static function generateNewThreadID(): string
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

    /**
     * 
     * @param string $sourceThreadID
     * @param string $targetThreadID
     * @return void
     */
    static function copyThread(string $sourceThreadID, string $targetThreadID): void
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
}
