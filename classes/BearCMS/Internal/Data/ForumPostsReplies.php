<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearFramework\App;

final class ForumPostsReplies
{

    static function add(string $forumPostID, array $author, string $text, string $status): void
    {
        $app = App::get();
        $data = $app->data->getValue('bearcms/forum/posts/post/' . md5($forumPostID) . '.json');
        $data = $data !== null ? json_decode($data, true) : [];
        if (empty($data['id'])) {
            return;
        }
        if (empty($data['replies'])) {
            $data['replies'] = [];
        }
        $data['replies'][] = [
            'id' => md5(uniqid()),
            'status' => $status,
            'author' => $author,
            'text' => $text,
            'createdTime' => time()
        ];
        $app->data->set($app->data->make('bearcms/forum/posts/post/' . md5($forumPostID) . '.json', json_encode($data)));
    }

    static function setStatus(string $forumPostID, string $replyID, string $status): void
    {
        $app = App::get();
        $dataKey = 'bearcms/forum/posts/post/' . md5($forumPostID) . '.json';
        $data = $app->data->getValue($dataKey);
        $hasChange = false;
        if ($data !== null) {
            $forumPostData = json_decode($data, true);
            if (is_array($forumPostData['replies']) && isset($forumPostData['replies'])) {
                foreach ($forumPostData['replies'] as $i => $reply) {
                    if (isset($reply['id']) && $reply['id'] === $replyID) {
                        if (isset($reply['status']) && $reply['status'] === $status) {
                            break;
                        }
                        $reply['status'] = $status;
                        $forumPostData['replies'][$i] = $reply;
                        $hasChange = true;
                        break;
                    }
                }
            }
        }
        if ($hasChange) {
            $app->data->set($app->data->make($dataKey, json_encode($forumPostData)));
        }
    }

    static function deleteReplyForever(string $forumPostID, string $replyID)
    {
        $app = App::get();
        $dataKey = 'bearcms/forum/posts/post/' . md5($forumPostID) . '.json';
        $data = $app->data->getValue($dataKey);
        $hasChange = false;
        if ($data !== null) {
            $forumPostData = json_decode($data, true);
            if (is_array($forumPostData['replies']) && isset($forumPostData['replies'])) {
                foreach ($forumPostData['replies'] as $i => $reply) {
                    if (isset($reply['id']) && $reply['id'] === $replyID) {
                        unset($forumPostData['replies'][$i]);
                        $hasChange = true;
                        break;
                    }
                }
            }
        }
        if ($hasChange) {
            $forumPostData['replies'] = array_values($forumPostData['replies']);
            $app->data->set($app->data->make($dataKey, json_encode($forumPostData)));
        }
    }

}
