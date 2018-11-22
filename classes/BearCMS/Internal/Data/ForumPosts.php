<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearFramework\App;
use BearCMS\Internal\Config;
use BearCMS\Internal;

final class ForumPosts
{

    static function add(string $categoryID, array $author, string $title, string $text, string $status): string
    {
        $app = App::get();
        $id = md5(uniqid());
        $data = [
            'id' => $id,
            'status' => $status,
            'author' => $author,
            'title' => $title,
            'text' => $text,
            'categoryID' => $categoryID,
            'createdTime' => time()
        ];

        $dataKey = 'bearcms/forums/posts/post/' . md5($id) . '.json';
        $app->data->set($app->data->make($dataKey, json_encode($data)));

        if (Config::hasFeature('NOTIFICATIONS')) {
            if (!$app->tasks->exists('bearcms-send-new-forum-post-notification')) {
                $app->tasks->add('bearcms-send-new-forum-post-notification', [
                    'categoryID' => $categoryID,
                    'forumPostID' => $id
                        ], ['id' => 'bearcms-send-new-forum-post-notification']);
            }
        }

        Internal\Data::setChanged($dataKey);
        return $id;
    }

    static function setStatus(string $forumPostID, string $status): void
    {
        $app = App::get();
        $dataKey = 'bearcms/forums/posts/post/' . md5($forumPostID) . '.json';
        $data = $app->data->getValue($dataKey);
        $hasChange = false;
        if ($data !== null) {
            $forumPostData = json_decode($data, true);
            $forumPostData['status'] = $status;
            $hasChange = true;
        }
        if ($hasChange) {
            $app->data->set($app->data->make($dataKey, json_encode($forumPostData)));
            Internal\Data::setChanged($dataKey);
        }
    }

}
