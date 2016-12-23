<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearFramework\App;

final class Comments
{

    static function add($threadID, $author, $text)
    {
        $app = App::get();
        $data = $app->data->get(
                [
                    'key' => 'bearcms/comments/thread/' . md5($threadID) . '.json',
                    'result' => ['body']
                ]
        );
        $data = strlen($data['body']) > 0 ? json_decode($data['body'], true) : [];
        if (empty($data['id'])) {
            $data['id'] = $threadID;
        }
        if (empty($data['comments'])) {
            $data['comments'] = [];
        }
        $data['comments'][] = [
            'id' => md5(uniqid()),
            'status' => 'approved',
            'author' => $author,
            'text' => $text,
            'createdTime' => time()
        ];
        $app->data->set(
                [
                    'key' => 'bearcms/comments/thread/' . md5($threadID) . '.json',
                    'body' => json_encode($data)
                ]
        );
    }

    static function setStatus($threadID, $commentID, $status)
    {
        $app = App::get();
        $dataKey = 'bearcms/comments/thread/' . md5($threadID) . '.json';
        $result = $app->data->get(
                [
                    'key' => $dataKey,
                    'result' => ['body']
                ]
        );
        $hasChange = false;
        if (isset($result['body'])) {
            $threadData = json_decode($result['body'], true);
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
            $app->data->set(
                    [
                        'key' => $dataKey,
                        'body' => json_encode($threadData)
                    ]
            );
        }
    }

    static function deleteCommentForever($threadID, $commentID)
    {
        $app = App::get();
        $dataKey = 'bearcms/comments/thread/' . md5($threadID) . '.json';
        $result = $app->data->get(
                [
                    'key' => $dataKey,
                    'result' => ['body']
                ]
        );
        $hasChange = false;
        if (isset($result['body'])) {
            $threadData = json_decode($result['body'], true);
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
            $app->data->set(
                    [
                        'key' => $dataKey,
                        'body' => json_encode($threadData)
                    ]
            );
        }
    }

    static function createCommentsCollection($rawCommentsData, $threadID)
    {
        $dataCollection = new \BearCMS\DataCollection();
        foreach ($rawCommentsData as $rawCommentData) {
            $comment = new \BearCMS\DataObject($rawCommentData);
            $comment->threadID = $threadID;
            $comment->author = new \BearCMS\DataObject(isset($rawCommentData['author']) && is_array($rawCommentData['author']) ? $rawCommentData['author'] : []);
            $dataCollection[] = $comment;
        }
        return $dataCollection;
    }

}
