<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;

/**
 * 
 */
class CommentsLocations
{

    /**
     *
     * @var array 
     */
    static $callbacks = [];

    /**
     * Register a new comments locations callback.
     * 
     * @param callable $callback A function to add comments locations.
     */
    static public function addSource(callable $callback)
    {
        self::$callbacks[] = $callback;
    }

    /**
     * 
     * @return array
     */
    static public function get(): array
    {
        $data = self::getTempData();
        if (empty($data)) {
            self::initializeTempData();
            return self::getTempData();
        }
        return $data;
    }

    /**
     * 
     * @return void
     */
    static private function initializeTempData(): void
    {
        foreach (self::$callbacks as $callback) {
            $callback();
        }
    }

    /**
     * 
     * @param string $threadID
     * @param string $path
     * @return void
     */
    static public function setLocation(string $threadID, string $path): void
    {
        self::setLocations([$threadID => $path]);
    }

    /**
     * 
     * @param array $list [threadID=>locationPath,threadID=>locationPath,...]
     * @return void
     */
    static public function setLocations(array $list): void
    {
        $data = self::getTempData();
        foreach ($list as $threadID => $locationPath) {
            $data[$threadID] = $locationPath;
        }
        self::setTempData($data);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static private function setTempData(array $data): void
    {
        $app = App::get();
        $app->data->setValue(self::getTempDataKey(), json_encode($data));
    }

    /**
     * 
     * @return array
     */
    static private function getTempData(): array
    {
        $app = App::get();
        $data = (string)$app->data->getValue(self::getTempDataKey());
        $data = strlen($data) > 0 ? json_decode($data, true) : null;
        if (!is_array($data)) {
            $data = [];
        }
        return $data;
    }

    /**
     * 
     * @return string
     */
    static private function getTempDataKey(): string
    {
        return '.temp/bearcms/comments-locations.json';
    }
}
