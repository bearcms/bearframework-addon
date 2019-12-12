<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;
use DateTime;

/**
 * 
 */
class Sitemap
{

    /**
     *
     * @var array 
     */
    static $callbacks = [];

    /**
     * 
     */
    static $cache = [];

    /**
     * Register a new sitemap callbacks.
     * 
     * @param callable $callback A function to add sitemap urls.
     */
    static public function register(callable $callback)
    {
        self::$callbacks[] = $callback;
    }

    /**
     * 
     * @return string
     */
    static public function getXML(): string
    {
        $app = App::get();
        $pathsToUpdate = [];
        $sitemap = self::getSitemap();
        $list = $sitemap->getList()->sortBy('location');
        $code = [];
        foreach ($list as $item) {
            $code[] = '<url>';
            $code[] = '<loc>' . $item->location . '</loc>';
            if ($item->changeFrequency !== null) {
                $code[] =  '<changefreq>' . $item->changeFrequency . '</changefreq>';
            }
            if ($item->lastModified !== null) {
                $lastModified = null;
                if (is_callable($item->lastModified)) {
                    $locationPath = str_replace($app->request->base, '', $item->location);
                    $date = self::getCachedDate($locationPath);
                    if ($date !== null) {
                        $lastModified = $date;
                    } else {
                        $pathsToUpdate[] = $locationPath;
                    }
                } else {
                    $lastModified = $item->lastModified;
                }
                if ($lastModified !== null) {
                    $code[] =  '<lastmod>' . $lastModified . '</lastmod>';
                }
            }
            if ($item->priority !== null) {
                $code[] =  '<priority>' . $item->priority . '</priority>';
            }
            $code[] =  '</url>';
        }
        if (!empty($pathsToUpdate)) {
            self::addUpdateCachedDatesTasks($pathsToUpdate);
        }
        return '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . implode('', $code) . '</urlset>';
    }

    /**
     * 
     * @return \BearCMS\Internal\Sitemap\Sitemap
     */
    static private function getSitemap(): \BearCMS\Internal\Sitemap\Sitemap
    {
        $sitemap = new \BearCMS\Internal\Sitemap\Sitemap();
        foreach (self::$callbacks as $callback) {
            $callback($sitemap);
        }
        return $sitemap;
    }

    /**
     * 
     * @param string $path
     * @return void
     */
    static function updateCachedDate(string $path): void
    {
        $app = App::get();
        $sitemap = self::getSitemap();
        $list = $sitemap->getList()->filterBy('location', $app->request->base . $path);
        if (isset($list[0])) {
            $item = $list[0];
            if ($item->lastModified !== null) {
                if (is_callable($item->lastModified)) {
                    $date = call_user_func($item->lastModified);
                    $minAllowedDate = 1572633993; // the date this feature is added
                    if (strlen($date) > 0) {
                        $dateTime = new DateTime($date);
                        if ($dateTime->getTimestamp() < $minAllowedDate) {
                            $date = '';
                        }
                    }
                    if (strlen($date) === 0) {
                        $date = date('c', $minAllowedDate);
                    }
                    self::setCachedDate(str_replace($app->request->base, '', $item->location), $date);
                }
            }
        }
    }

    /**
     * 
     * @param string $path
     * @param string $date
     * @return void
     */
    static private function setCachedDate(string $path, string $date)
    {
        $data = self::getCachedDatesData();
        $data[$path] = $date;
        self::setCachedDatesData($data);
    }

    /**
     * 
     * @param string $path
     * @return string|null
     */
    static private function getCachedDate(string $path): ?string
    {
        $data = self::getCachedDatesData();
        if (isset($data[$path])) {
            return (string) $data[$path];
        }
        return null;
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static private function setCachedDatesData(array $data): void
    {
        $app = App::get();
        $app->data->setValue(self::getCachedDatesDataKey(), json_encode($data));
        self::$cache['cached-dates'] = $data;
    }

    /**
     * 
     * @return array
     */
    static private function getCachedDatesData(): array
    {
        if (isset(self::$cache['cached-dates'])) {
            return self::$cache['cached-dates'];
        }
        $app = App::get();
        $data = $app->data->getValue(self::getCachedDatesDataKey());
        $data = strlen($data) > 0 ? json_decode($data, true) : null;
        if (!is_array($data)) {
            $data = [];
        }
        self::$cache['cached-dates'] = $data;
        return $data;
    }

    /**
     * 
     * @return string
     */
    static private function getCachedDatesDataKey(): string
    {
        return '.temp/bearcms/sitemap-cached-dates.json';
    }

    /**
     * 
     * @param string $url
     * @param array $details
     * @return void
     */
    static function addLastModifiedDetails(string $url, array $details): void
    {
        $app = App::get();
        $path = str_replace($app->request->base, '', $url);
        $data = self::getLastModifiedDetailsData();
        $data[$path] = $details;
        self::setLastModifiedDetailsData($data);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static private function setLastModifiedDetailsData(array $data): void
    {
        $app = App::get();
        $app->data->setValue(self::getLastModifiedDetailsDataKey(), json_encode($data));
    }

    /**
     * 
     * @return array
     */
    static private function getLastModifiedDetailsData(): array
    {
        $app = App::get();
        $data = $app->data->getValue(self::getLastModifiedDetailsDataKey());
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
    static private function getLastModifiedDetailsDataKey(): string
    {
        return '.temp/bearcms/sitemap-cached-details.json';
    }

    /**
     * 
     * @param array $details
     * @return string|null
     */
    static function getDateFromLastModifiedDetails(array $details): ?string
    {
        $results = [];
        foreach ($details['dates'] as $date) {
            if (strlen($date) > 0) {
                if (is_numeric($date)) {
                    $results[] = (int) $date;
                } else {
                    // other formats
                }
            }
        }
        return empty($results) ? null : date('c', max($results));
    }

    /**
     * 
     * @return string
     */
    static private function getChangedDataKeysListDataKey(): string
    {
        return '.temp/bearcms/sitemap-changed-datakeys.json';
    }

    /**
     * Logs changed data keys to check them later and update the urls' cached dates
     * 
     * @param array $dataKeys
     * @return void
     */
    static function onDataChanged(array $dataKeys): void
    {
        if (empty($dataKeys)) {
            return;
        }
        $validDataKeys = [];
        foreach ($dataKeys as $dataKey) {
            if (strpos($dataKey, 'bearcms/') === 0) {
                $validDataKeys[] = $dataKey;
            }
        }
        if (empty($validDataKeys)) {
            return;
        }
        $app = App::get();
        $app->data->append(self::getChangedDataKeysListDataKey(), substr(json_encode($validDataKeys), 1, -1) . ',');
        $app->tasks->add('bearcms-sitemap-process-changes', null, [
            'id' => 'bearcms-sitemap-process-changes',
            'startTime' => (ceil(time() / 300) * 300),
            'priority' => 5,
            'ignoreIfExists' => true
        ]);
    }

    /**
     * Updates the cached dates of the urls that contain the changed datakeys
     * 
     * @return void
     */
    static function processChangedDataKeys(): void
    {
        $app = App::get();
        $changedDataKeysListDataKey = self::getChangedDataKeysListDataKey();
        $dataKeys = $app->data->getValue($changedDataKeysListDataKey);
        if (strlen($dataKeys) === 0) {
            return;
        }
        $app->data->delete($changedDataKeysListDataKey);
        $dataKeys = json_decode('[' . trim($dataKeys, ',') . ']', true);
        if (!is_array($dataKeys)) {
            return;
        }
        $allDetailsData = self::getLastModifiedDetailsData();
        if (empty($allDetailsData)) {
            return;
        }
        $dataKeys = array_unique($dataKeys);
        $pathsToUpdate = [];
        foreach ($allDetailsData as $path => $detailsData) {
            if (!empty(array_intersect($detailsData['dataKeys'], $dataKeys))) {
                $pathsToUpdate[] = $path;
            }
        }
        $pathsToUpdate = array_unique($pathsToUpdate);
        if (!empty($pathsToUpdate)) {
            self::addUpdateCachedDatesTasks($pathsToUpdate);
        }
    }

    /**
     * 
     * @param array $paths
     * @return void
     */
    static function addUpdateCachedDatesTasks(array $paths): void
    {
        $app = App::get();
        $app->tasks->add('bearcms-sitemap-update-cached-dates', $paths, [
            'id' => 'bearcms-sitemap-update-cached-dates-' . md5(json_encode($paths)),
            'priority' => 5,
            'ignoreIfExists' => true
        ]);
    }

    /**
     * 
     * @param string $path
     * @return void
     */
    static function addUpdateCachedDateTasks(string $path): void
    {
        $app = App::get();
        $app->tasks->add('bearcms-sitemap-update-cached-date', $path, [
            'id' => 'bearcms-sitemap-update-cached-date-' . md5($path),
            'priority' => 4,
            'ignoreIfExists' => true
        ]);
    }

    /**
     * 
     * @return void
     */
    static function addCheckSitemapForChangesTask(): void
    {
        $app = App::get();
        $app->tasks->add('bearcms-sitemap-check-for-changes', null, [
            'id' => 'bearcms-sitemap-check-for-changes',
            'startTime' => time() + 5 * 60,
            'ignoreIfExists' => true
        ]);
    }

    /**
     * 
     * @return void
     */
    static function checkSitemapForChanges(): void
    {
        $app = App::get();
        $xml = self::getXML();
        $xmlMD5 = md5($xml);
        $dataKey = '.temp/bearcms/last-checked-sitemap-xml-md5';
        if ($app->data->getValue($dataKey) !== $xmlMD5) {
            $app->data->setValue($dataKey, $xmlMD5);
            $app->bearCMS->dispatchEvent('internalSitemapChange');
            $settings = $app->bearCMS->data->settings->get();
            if (!empty($settings->allowSearchEngines)) {
                $app->tasks->add('bearcms-sitemap-notify-search-engines', null, [
                    'id' => 'bearcms-sitemap-notify-search-engines',
                    'startTime' => (time() + 5 * 60),
                    'priority' => 4,
                    'ignoreIfExists' => true
                ]);
            }
        }
    }

    /**
     * 
     * @return void
     */
    static function notifySearchEngines(): void
    {
        $app = App::get();
        $ping = function (string $url) use ($app) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            $response = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $app->data->append('.temp/bearcms/sitemap-ping.log', date('c') . "\n" . $url . "\n" . $status . "\n" . $response . "\n\n");
        };
        $sitemapURL = $app->urls->get('/sitemap.xml');
        $ping('https://www.google.com/webmasters/tools/ping?sitemap=' . $sitemapURL);
        $ping('https://www.bing.com/webmaster/ping.aspx?siteMap=' . $sitemapURL);
    }
}
