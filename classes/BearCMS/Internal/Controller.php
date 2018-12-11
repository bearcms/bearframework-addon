<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;
use BearCMS\Internal;
use BearCMS\Internal\Config;
use BearCMS\Internal2;

/**
 * @internal
 */
class Controller
{

    /**
     * 
     * @return \BearFramework\App\Response
     */
    static function handleAdminPage(): \BearFramework\App\Response
    {
        $app = App::get();
        $path = (string) $app->request->path;
        if ($path === Config::$adminPagesPathPrefix) {
            if (!$app->bearCMS->data->users->hasUsers()) {
                return new App\Response\TemporaryRedirect($app->request->base . Config::$adminPagesPathPrefix . 'firstrun/');
            }
        } elseif ($path === Config::$adminPagesPathPrefix . 'firstrun/') {
            if ($app->bearCMS->data->users->hasUsers()) {
                return new App\Response\TemporaryRedirect($app->request->base . Config::$adminPagesPathPrefix);
            }
        }
        $arguments = [];
        $arguments['path'] = $path;
        $data = Internal\Server::call('adminpage', $arguments, true);
        if (isset($data['error'])) {
            return new App\Response\TemporaryUnavailable(isset($data['errorMessage']) ? $data['errorMessage'] : 'Unknown error!');
        }
        if (isset($data['result'])) {
            if ($data['result'] === 'notFound') {
                return new App\Response\NotFound();
            } elseif (is_array($data['result']) && isset($data['result']['content'])) {
                $content = $data['result']['content'];
                $content = Internal\Server::updateAssetsUrls($content, false);
                $response = new App\Response\HTML($content);
                $response->headers->set($response->headers->make('Cache-Control', 'private, max-age=0, no-cache, no-store'));
                $response->headers->set($response->headers->make('X-Robots-Tag', 'noindex, nofollow'));
                return $response;
            }
        }
        return new App\Response\TemporaryUnavailable();
    }

    /**
     * 
     * @return \BearFramework\App\Response
     */
    static function handleAjax(): \BearFramework\App\Response
    {
        $data = Internal\Server::proxyAjax();
        $response = new App\Response\JSON($data);
        $response->headers->set($response->headers->make('Cache-Control', 'private, max-age=0, no-cache, no-store'));
        $response->headers->set($response->headers->make('X-Robots-Tag', 'noindex, nofollow'));
        return $response;
    }

    /**
     * 
     * @return \BearFramework\App\Response
     * @throws \Exception
     */
    static function handleFileUpload(): \BearFramework\App\Response
    {
        $app = App::get();
        $file = $app->request->formData->get('Filedata');
        if ($file !== null && strlen($file->value) > 0 && is_file($file->filename)) {
            $originalFilename = strtolower($file->value);
            $pathinfo = pathinfo($originalFilename);
            $fileExtension = isset($pathinfo['extension']) ? $pathinfo['extension'] : '';
            $tempFilename = md5('fileupload' . uniqid()) . (isset($fileExtension{0}) ? '.' . $fileExtension : '');
            $filename = $app->data->getFilename('.temp/bearcms/files/' . $tempFilename);
            $pathinfo = pathinfo($filename);
            if (isset($pathinfo['dirname'])) {
                if (!is_dir($pathinfo['dirname'])) {
                    try {
                        mkdir($pathinfo['dirname'], 0777, true);
                    } catch (\Exception $e) {
                        if ($e->getMessage() !== 'mkdir(): File exists') { // The directory may be just created in other process.
                            throw $e;
                        }
                    }
                }
            }
            move_uploaded_file($file->filename, $filename);
            if (is_file($filename)) {
                $queryList = $app->request->query->getList();
                $temp = [];
                foreach ($queryList as $queryListItem) {
                    $temp[$queryListItem->name] = $queryListItem->value;
                }
                $data = Internal\Server::call('fileupload', ['tempFilename' => $tempFilename, 'requestData' => json_encode($temp)]);
                if (isset($data['result'])) {
                    return new App\Response\JSON($data['result']);
                } else {
                    return new App\Response\TemporaryUnavailable();
                }
            }
        }
        $response = new App\Response();
        $response->statusCode = 400;
        $response->headers->set($response->headers->make('Content-Type', 'text/json'));
        return $response;
    }

    /**
     * 
     * @param bool $preview
     * @return \BearFramework\App\Response
     */
    static function handleFileRequest(bool $preview): \BearFramework\App\Response
    {
        $app = App::get();
        $filename = (string) $app->request->path->getSegment(2);
        $fileData = Internal\Data\Files::getFileData($filename);
        $download = false;
        if (is_array($fileData)) {
            if ($fileData['published'] === 1) {
                $download = true;
            } else {
                if ($app->bearCMS->currentUser->exists() && $app->bearCMS->currentUser->hasPermission('manageFiles')) {
                    $download = true;
                }
            }
        }
        if ($download) {
            $fullFilename = $app->data->getFilename('bearcms/files/custom/' . $filename);
            $response = new App\Response\FileReader($fullFilename);
            $mimeType = $app->assets->getMimeType($fullFilename);
            if ($mimeType !== null) {
                $response->headers->set($response->headers->make('Content-Type', $mimeType));
            }
            if (!$preview) {
                $response->headers->set($response->headers->make('Content-Disposition', 'attachment; filename=' . $fileData['name'])); // rawurlencode
                $response->headers->set($response->headers->make('Content-Length', (string) filesize($fullFilename)));
            }
            return $response;
        }
        return new App\Response\NotFound();
    }

    /**
     * 
     * @return \BearFramework\App\Response
     */
    static function handleFilePreview(): \BearFramework\App\Response
    {
        return self::handleFileRequest(true);
    }

    /**
     * 
     * @return \BearFramework\App\Response
     */
    static function handleFileDownload(): \BearFramework\App\Response
    {
        return self::handleFileRequest(false);
    }

    /**
     * 
     * @return \BearFramework\App\Response
     */
    static function handleRSS(): \BearFramework\App\Response
    {
        $app = App::get();
        $settings = $app->bearCMS->data->settings->get();

        $data = '<title>' . htmlspecialchars($settings->title) . '</title>';
        $data .= '<link>' . $app->urls->get('/') . '</link>';
        $data .= '<description>' . htmlspecialchars($settings->description) . '</description>';
        $data .= '<language>' . htmlspecialchars($settings->language) . '</language>';
        $data .= '<atom:link href="' . $app->urls->get('/rss.xml') . '" rel="self" type="application/rss+xml">';
        $data .= '</atom:link>';

        $blogPosts = $app->bearCMS->data->blogPosts->getList()
                ->filterBy('status', 'published')
                ->sortBy('publishedTime', 'desc');
        $contentType = $settings->rssType;
        $counter = 0;
        foreach ($blogPosts as $blogPost) {
            $blogPostUrl = $app->urls->get(Config::$blogPagesPathPrefix . $blogPost->slug . '/');
            $blogPostContent = $app->components->process('<component src="bearcms-elements" id="bearcms-blogpost-' . $blogPost->id . '"/>');
            $domDocument = new \IvoPetkov\HTML5DOMDocument();
            $domDocument->loadHTML($blogPostContent);
            $contentElementsContainer = $domDocument->querySelector('body')->firstChild;
            $content = '';
            if ($contentType === 'fullContent') {
                $content = $contentElementsContainer->innerHTML;
            } elseif ($contentType === 'contentSummary') {
                $content = '';
                $child = $contentElementsContainer->childNodes->item(0);
                if ($child != null) {
                    $content .= $child->outerHTML . '<br><br>';
                }
                $content .= sprintf(__('bearcms.rss.Read the full post at %s'), '<a href="' . $blogPostUrl . '">' . $blogPostUrl . '</a>');
            } elseif ($contentType === 'noContent') {
                $content .= sprintf(__('bearcms.rss.Read the post at %s'), '<a href="' . $blogPostUrl . '">' . $blogPostUrl . '</a>');
            }
            $data .= '<item>';
            $data .= '<title>' . htmlspecialchars($blogPost->title) . '</title>';
            $data .= '<link>' . $blogPostUrl . '</link>';
            $data .= '<description><![CDATA[' . $content . ']]></description>';
            $data .= '<pubDate>' . date('r', $blogPost->publishedTime) . '</pubDate>';
            $data .= '<guid isPermaLink="false">' . $blogPostUrl . '</guid>';
            $data .= '</item>';
            $counter++;
            if ($counter >= 20) {
                break;
            }
        }
        $response = new App\Response('<?xml version="1.0" encoding="UTF-8"?><rss xmlns:atom="http://www.w3.org/2005/Atom" version="2.0"><channel>' . $data . '</channel></rss>');
        $response->headers->set($response->headers->make('Content-Type', 'text/xml'));
        return $response;
    }

    /**
     * 
     * @return \BearFramework\App\Response
     */
    static function handleSitemap(): \BearFramework\App\Response
    {
        $app = App::get();
        $urls = [];
        $baseUrl = $app->request->base;

        $addUrl = function($path) use (&$urls, $baseUrl) {
            $encodedPath = implode('/', array_map('urlencode', explode('/', $path)));
            $urls[] = '<url><loc>' . $baseUrl . $encodedPath . '</loc></url>';
        };
        $addUrl('/');

        $list = Internal\Data\Pages::getPathsList('published');
        foreach ($list as $path) {
            $addUrl($path);
        }

        $list = Internal\Data\BlogPosts::getSlugsList('published');
        foreach ($list as $slug) {
            $addUrl(Config::$blogPagesPathPrefix . $slug . '/');
        }

        $response = new App\Response('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . implode('', $urls) . '</urlset>');
        $response->headers->set($response->headers->make('Content-Type', 'text/xml'));
        return $response;
    }

    /**
     * 
     * @return \BearFramework\App\Response
     */
    static function handleRobots(): \BearFramework\App\Response
    {
        $app = App::get();
        $response = new App\Response('User-agent: *
Disallow:

Sitemap: ' . $app->request->base . '/sitemap.xml');
        $response->headers->set($response->headers->make('Content-Type', 'text/plain'));
        return $response;
    }

}
