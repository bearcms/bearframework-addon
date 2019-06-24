<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

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
        $sitemap = new \BearCMS\Internal\Sitemap\Sitemap();
        foreach (self::$callbacks as $callback) {
            $callback($sitemap);
        }
        $list = $sitemap->getList()->sortBy('location');
        $elements = [];
        foreach ($list as $item) {
            $elements[] = '<url>'
                    . '<loc>' . $item->location . '</loc>'
                    . ($item->changeFrequency !== null ? '<lastmod>' . $item->changeFrequency . '</lastmod>' : '')
                    . ($item->lastModified !== null ? '<lastmod>' . $item->lastModified . '</lastmod>' : '')
                    . ($item->priority !== null ? '<priority>' . $item->priority . '</priority>' : '')
                    . '</url>';
        }
        return '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . implode('', $elements) . '</urlset>';
    }

}
