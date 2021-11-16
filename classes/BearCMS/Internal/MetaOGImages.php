<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;
use IvoPetkov\HTML5DOMDocument;

/**
 * @internal
 * @codeCoverageIgnore
 */
class MetaOGImages
{

    /**
     * 
     * @var array
     */
    static private $sources = [];

    /**
     * 
     * @param callable $callback
     * @return void
     */
    static function addSource(callable $callback): void
    {
        self::$sources[] = $callback;
    }

    /**
     * 
     * @param string $path
     * @return string|null
     */
    private static function callSources(string $path): ?string
    {
        foreach (self::$sources as $source) {
            $result = call_user_func($source, $path);
            if (is_string($result)) {
                return $result;
            }
        }
        return null;
    }

    /**
     * 
     * @param string $path
     * @return string|null
     */
    static function getImage(string $path): ?string
    {
        $app = App::get();

        $iconCache = null;
        $getIconFilename = function () use (&$iconCache) {
            if ($iconCache === null) {
                $iconCache = [\BearCMS\Internal\Data\Settings::getIconForSize(2000)];
            }
            return $iconCache[0];
        };

        $filename = null;

        $containerID = null;
        if (strpos($path, Config::$blogPagesPathPrefix) === 0) {
            $slug = rtrim(substr($path, strlen(Config::$blogPagesPathPrefix)), '/');
            $blogPosts = $app->bearCMS->data->blogPosts->getList();
            foreach ($blogPosts as $blogPost) {
                if ($blogPost->status === 'published' && $blogPost->slug === $slug) {
                    if (strlen($blogPost->image) > 0) {
                        $filename = $blogPost->image;
                        break;
                    }
                    $containerID = 'bearcms-blogpost-' . $blogPost->id;
                    break;
                }
            }
        } else {
            $pages = $app->bearCMS->data->pages->getList();
            foreach ($pages as $page) {
                if ($page->status === 'public' && $page->path === $path) {
                    if (strlen($page->image) > 0) {
                        $filename = $page->image;
                        break;
                    }
                    $containerID = 'bearcms-page-' . $page->id;
                    break;
                }
            }
            if ($path === '/' && $filename === null) {
                $iconFilename = $getIconFilename();
                if ($iconFilename !== null) {
                    $filename = $iconFilename;
                } else {
                    $containerID = 'bearcms-page-home';
                }
            }
        }

        if ($filename !== null) {
            return $app->assets->getURL($filename, ['cacheMaxAge' => 999999999]);
        }

        if ($containerID !== null) {
            $content = $app->components->process('<component src="bearcms-elements" id="' . htmlentities($containerID) . '"/>');
            if (strpos($content, '<img') !== false) {
                $html5Document = new HTML5DOMDocument();
                $html5Document->loadHTML($content, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
                $imageElement = $html5Document->querySelector('img');
                if ($imageElement !== null) {
                    return $imageElement->getAttribute('src');
                }
            }
        } else {
            $url = self::callSources($path);
            if ($url !== null) {
                return $url;
            }
        }

        // use the website icon if no image found on page
        $filename = $getIconFilename();
        if ($filename !== null) {
            return $app->assets->getURL($filename, ['cacheMaxAge' => 999999999]);
        }

        return null;
    }
}
