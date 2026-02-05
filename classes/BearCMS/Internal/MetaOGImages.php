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
     * @param string|null $filename
     * @param string|null $url
     * @return string|null
     */
    private static function callSources(string $path, ?string $filename, ?string $url): ?string
    {
        foreach (self::$sources as $source) {
            $result = call_user_func($source, $path, $filename, $url);
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

        $filename = null;
        $url = null;

        $getFilenameURL = function (string $filename) use ($app) {
            return $app->assets->getURL($filename, ['cacheMaxAge' => 999999999]);
        };

        $containerID = null;
        if (strpos($path, Config::$blogPagesPathPrefix) === 0) {
            $slug = rtrim(substr($path, strlen(Config::$blogPagesPathPrefix)), '/');
            $blogPosts = $app->bearCMS->data->blogPosts->getList();
            foreach ($blogPosts as $blogPost) {
                if ($blogPost->slug === $slug && ($blogPost->status === 'published' || $blogPost->status === 'draft')) {
                    $blogPostImage = (string)$blogPost->image;
                    if (strlen($blogPostImage) > 0) {
                        $filename = $blogPostImage;
                        break;
                    }
                    $containerID = 'bearcms-blogpost-' . $blogPost->id;
                    break;
                }
            }
        } else {
            $pages = $app->bearCMS->data->pages->getList();
            foreach ($pages as $page) {
                if ($page->path === $path && ($page->status === 'public' || $page->status === 'secret')) {
                    $pageImage = (string)$page->image;
                    if (strlen($pageImage) > 0) {
                        $filename = $pageImage;
                        break;
                    }
                    $containerID = 'bearcms-page-' . $page->id;
                    break;
                }
            }
            if ($path === '/' && $filename === null) {
                $containerID = 'bearcms-page-home';
            }
        }

        if ($filename === null) {
            $settings = $app->bearCMS->data->settings->get();
            if (!empty($settings->image)) {
                $filename = $settings->image;
            }
        }

        if ($filename !== null) {
            $url = $getFilenameURL($filename);
        } else {
            if ($containerID !== null) {
                $content = $app->components->process('<component src="bearcms-elements" id="' . htmlentities($containerID) . '"/>');
                if (strpos($content, '<img') !== false) {
                    $html5Document = new HTML5DOMDocument();
                    $html5Document->loadHTML($content, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
                    $imageElement = $html5Document->querySelector('img');
                    if ($imageElement !== null) {
                        $url = $imageElement->getAttribute('src');
                    }
                }
            }
        }

        if ($url === null) {
            $filename = \BearCMS\Internal\Data\Settings::getIconForSize(2000); // use the website icon if no image found on page
            $url = $getFilenameURL($filename);
        }

        $newURL = self::callSources($path, $filename, $url);
        if ($newURL !== null) {
            return $newURL;
        }

        return $url;
    }
}
