<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearFramework\App;
use BearCMS\Internal\ElementsHelper;

final class Comments
{

    static function add(string $threadID, array $author, string $text, string $status): void
    {
        $app = App::get();
        $data = $app->data->getValue('bearcms/comments/thread/' . md5($threadID) . '.json');
        $data = $data !== null ? json_decode($data, true) : [];
        if (empty($data['id'])) {
            $data['id'] = $threadID;
        }
        if (empty($data['comments'])) {
            $data['comments'] = [];
        }
        $data['comments'][] = [
            'id' => md5(uniqid()),
            'status' => $status,
            'author' => $author,
            'text' => $text,
            'createdTime' => time()
        ];
        $app->data->set($app->data->make('bearcms/comments/thread/' . md5($threadID) . '.json', json_encode($data)));
    }

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

    static function createCommentsCollection(array $rawCommentsData, string $threadID): \BearCMS\DataList
    {
        $dataList = new \BearCMS\DataList();
        foreach ($rawCommentsData as $rawCommentData) {
            $comment = new \BearCMS\Data\Comment();
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

    static function getCommentsElementsLocations(): array
    {
        // todo cache
        $app = App::get();
        $result = [];
        $pages = $app->bearCMS->data->pages->getList();
        foreach ($pages as $page) {
            $containerElementIDs = ElementsHelper::getContainerElementsIDs('bearcms-page-' . $page->id);
            $elementsRawData = ElementsHelper::getElementsRawData($containerElementIDs);
            foreach ($elementsRawData as $elementRawData) {
                $elementData = ElementsHelper::decodeElementRawData($elementRawData);
                if ($elementData['type'] === 'comments') {
                    if (isset($elementData['data']['threadID'])) {
                        $result[$elementData['data']['threadID']] = $app->request->base . $page->path;
                    }
                }
            }
        }
        $blogPosts = $app->bearCMS->data->blogPosts->getList();
        foreach ($blogPosts as $blogPost) {
            $containerElementIDs = ElementsHelper::getContainerElementsIDs('bearcms-blogpost-' . $blogPost->id);
            $elementsRawData = ElementsHelper::getElementsRawData($containerElementIDs);
            foreach ($elementsRawData as $elementRawData) {
                $elementData = ElementsHelper::decodeElementRawData($elementRawData);
                if ($elementData['type'] === 'comments') {
                    if (isset($elementData['data']['threadID'])) {
                        $result[$elementData['data']['threadID']] = $app->request->base . Options::$blogPagesPathPrefix . $blogPost->slug . '/';
                    }
                }
            }
        }
        return $result;
    }

}
