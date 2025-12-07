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
class Sitemap
{

    /**
     *
     * @var array 
     */
    static $callbacks = [];

    /**
     * Register a new sitemap callback.
     * 
     * @param callable $callback A function to add sitemap urls.
     */
    static public function addSource(callable $callback)
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
        $appURLs = $app->urls;
        $pathsToUpdate = [];
        $sitemap = self::getSitemap();
        $list = $sitemap->getList()->sortBy('locationPath');
        $items = [];
        $tempData = null;
        foreach ($list as $item) {
            $code = '<url>';
            $locationPath = $item->locationPath;
            $code .= '<loc>' . $appURLs->get($locationPath) . '</loc>';
            if ($item->changeFrequency !== null) {
                $code .=  '<changefreq>' . $item->changeFrequency . '</changefreq>';
            }
            if ($item->lastModified !== null) {
                $lastModified = null;
                if (is_callable($item->lastModified)) {
                    if ($tempData === null) {
                        $tempData = self::getTempData();
                    }
                    $date = isset($tempData['dates'][$locationPath]) ? $tempData['dates'][$locationPath] : null;
                    if ($date !== null) {
                        $lastModified = $date;
                    } else {
                        $pathsToUpdate[] = $locationPath;
                    }
                } else {
                    $lastModified = $item->lastModified;
                }
                if ($lastModified !== null) {
                    $code .=  '<lastmod>' . date('c', $lastModified) . '</lastmod>';
                }
            }
            if ($item->priority !== null) {
                $code .=  '<priority>' . $item->priority . '</priority>';
            }
            $code .= '</url>';
            $items[$locationPath] = $code;
        }
        ksort($items);
        if (!empty($pathsToUpdate)) {
            self::addUpdateDatesTasks($pathsToUpdate);
        }
        return '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . implode('', $items) . '</urlset>';
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
    static function updateDate(string $path): void
    {
        $app = App::get();
        $sitemap = self::getSitemap();
        $list = $sitemap->getList()->filterBy('locationPath', $path);
        if (isset($list[0])) {
            $item = $list[0];
            if ($item->lastModified !== null) {
                if (is_callable($item->lastModified)) {
                    $date = (int)call_user_func($item->lastModified);
                    $minAllowedDate = 1572633993; // the date this feature is added
                    if ($date > 0 && $date < $minAllowedDate) {
                        $date = 0;
                    }
                    if ($date === 0) {
                        $date = $minAllowedDate;
                    }
                    $tempData = self::getTempData();
                    $hasChange = !isset($tempData['dates'][$path]) || $tempData['dates'][$path] !== $date;
                    if ($hasChange) {
                        $tempData['dates'][$path] = $date;
                        self::setTempData($tempData);
                        $settings = $app->bearCMS->data->settings->get();
                        if (!empty($settings->allowSearchEngines)) {
                            $app->tasks->add('bearcms-sitemap-notify-search-engines', null, [
                                'id' => 'bearcms-sitemap-notify-search-engines',
                                'startTime' => (time() + 5 * 60),
                                'priority' => 4,
                                'ignoreIfExists' => true
                            ]);
                        }
                        $app->bearCMS->dispatchEvent('internalSitemapChange');
                    }
                }
            }
        }
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static private function setTempData(array $data): void
    {
        $app = App::get();
        $app->data->setValue(self::getTempDataKey(), json_encode($data, JSON_THROW_ON_ERROR));
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
        if (!isset($data['dates'])) {
            $data['dates'] = [];
        }
        if (!isset($data['pings'])) {
            $data['pings'] = [];
        }
        return $data;
    }

    /**
     * 
     * @return string
     */
    static private function getTempDataKey(): string
    {
        return '.temp/bearcms/sitemap.json';
    }

    /**
     * 
     * @param array $paths
     * @return void
     */
    static function addUpdateDatesTasks(array $paths): void
    {
        $app = App::get();
        $app->tasks->add('bearcms-sitemap-update-dates', $paths, [
            'id' => 'bearcms-sitemap-update-dates-' . md5(json_encode($paths, JSON_THROW_ON_ERROR)) . '-' . count($paths), // for debugging purposes
            'ignoreIfExists' => true
        ]);
    }

    /**
     * 
     * @param string $path
     * @return void
     */
    static function addUpdateDateTask(string $path): void
    {
        $app = App::get();
        $app->tasks->add('bearcms-sitemap-update-date', $path, [
            'id' => 'bearcms-sitemap-update-date-' . md5($path),
            'priority' => 4,
            'ignoreIfExists' => true
        ]);
    }

    /**
     * 
     * @return void
     */
    static function notifySearchEngines(): void
    {
        $app = App::get();
        $ping = function (string $url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            $response = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            // curl_close($ch); not needed since PHP 8.0
            return date('c') . ' - ' . $url . ' - ' . $status . ' - ' . $response;
        };
        $tempData = self::getTempData();
        $sitemapURL = $app->urls->get('/sitemap.xml');
        $tempData['pings'][] = $ping('https://www.google.com/webmasters/tools/ping?sitemap=' . $sitemapURL);
        $tempData['pings'][] = $ping('https://www.bing.com/webmaster/ping.aspx?siteMap=' . $sitemapURL);
        $pingsCount = count($tempData['pings']);
        if ($pingsCount > 20) {
            $tempData['pings'] = array_slice($tempData['pings'], $pingsCount - 20);
        }
        self::setTempData($tempData);
    }
}
