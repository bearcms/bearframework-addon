<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;
use BearCMS\Internal\Server;
use BearCMS\Internal\Options;

final class Controller
{

    static function handleAdminPage()
    {
        $app = App::get();
        $path = (string) $app->request->path;
        if ($path === Options::$adminPagesPathPrefix) {
            if (!$app->bearCMS->data->users->hasUsers()) {
                return new App\Response\TemporaryRedirect($app->request->base . Options::$adminPagesPathPrefix . 'firstrun/');
            }
        } elseif ($path === Options::$adminPagesPathPrefix . 'firstrun/') {
            if ($app->bearCMS->data->users->hasUsers()) {
                return new App\Response\TemporaryRedirect($app->request->base . Options::$adminPagesPathPrefix);
            }
        }
        $arguments = [];
        $arguments['path'] = $path;
        $data = Server::call('adminpage', $arguments, true);
        if (isset($data['result'])) {
            if ($data['result'] === 'notFound') {
                return new App\Response\NotFound();
            } elseif (is_array($data['result']) && isset($data['result']['content'])) {
                $content = $data['result']['content'];
                $content = Server::updateAssetsUrls($content, false);
                $response = new App\Response\HTML($content);
                $response->enableBearCMS = true;
                $response->bearCMSSystemPage = true;
                return $response;
            }
        }
        return new App\Response\TemporaryUnavailable();
    }

    static function handleAjax()
    {
        $data = Server::proxyAjax();
        $response = new App\Response\JSON($data);
        $response->headers->set('X-Robots-Tag', 'noindex');
        return $response;
    }

    static function handleFileUpload()
    {
        $app = App::get();
        $file = $app->request->files->get('Filedata');
        if ($file !== null && strlen($file['filename']) > 0 && $file['errorCode'] === UPLOAD_ERR_OK && is_file($file['tempFilename'])) {
            $originalFilename = strtolower($file['filename']);
            $pathinfo = pathinfo($originalFilename);
            $fileExtension = isset($pathinfo['extension']) ? $pathinfo['extension'] : '';
            $tempFilename = md5('fileupload' . uniqid()) . (isset($fileExtension{0}) ? '.' . $fileExtension : '');
            $filename = $app->data->getFilename('.temp/bearcms/files/' . $tempFilename);
            $pathinfo = pathinfo($filename);
            if (isset($pathinfo['dirname'])) {
                if (!is_dir($pathinfo['dirname'])) {
                    mkdir($pathinfo['dirname'], 0777, true);
                }
            }
            move_uploaded_file($file['tempFilename'], $filename);
            if (is_file($filename)) {
                $queryList = $app->request->query->getList();
                $temp = [];
                foreach ($queryList as $queryListItem) {
                    $temp[$queryListItem['name']] = $queryListItem['value'];
                }
                $response = Server::call('fileupload', array('tempFilename' => $tempFilename, 'requestData' => json_encode($temp)));
                if (isset($response['result'])) {
                    return new App\Response\JSON($response['result']);
                } else {
                    return new App\Response\TemporaryUnavailable();
                }
            }
        }
        $response = new App\Response();
        $response->statusCode = 400;
        $response->headers->set('Content-Type', 'text/json');
        return $response;
    }

    static function handleFileRequest($preview)
    {
        $app = App::get();
        $filename = (string) $app->request->path[2];
        $fileData = \BearCMS\Internal\Data\Files::getFileData($filename);
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
                $response->headers->set('Content-Type', $mimeType);
            }
            if (!$preview) {
                $response->headers->set('Content-Disposition', 'attachment; filename=' . $fileData['name']); // rawurlencode
                $response->headers->set('Content-Length', (string) filesize($fullFilename));
            }
            return $response;
        }
        return new App\Response\NotFound();
    }

    static function handleFilePreview()
    {
        return self::handleFileRequest(true);
    }

    static function handleFileDownload()
    {
        return self::handleFileRequest(false);
    }

    static function handleRSS()
    {
        $app = App::get();
        $settings = $app->bearCMS->data->settings->get();
        $baseUrl = $app->request->base;

        $data = '<title>' . (isset($settings['title']) ? htmlspecialchars($settings['title']) : '') . '</title>';
        $data .= '<link>' . $baseUrl . '/</link>';
        $data .= '<description>' . (isset($settings['description']) ? htmlspecialchars($settings['description']) : '') . '</description>';
        $data .= '<language>' . (isset($settings['language']) ? htmlspecialchars($settings['language']) : '') . '</language>';
        $data .= '<atom:link href="' . $baseUrl . '/rss.xml" rel="self" type="application/rss+xml">';
        $data .= '</atom:link>';

        $blogPosts = $app->bearCMS->data->blog->getList()
                ->filterBy('status', 'published')
                ->sortBy('publishedTime', 'desc');
        foreach ($blogPosts as $blogPost) {
            $blogPostUrl = isset($blogPost['slug']) ? $baseUrl . Options::$blogPagesPathPrefix . $blogPost['slug'] . '/' : '';
            $data .= '<item>';
            $data .= '<title>' . (isset($blogPost['title']) ? htmlspecialchars($blogPost['title']) : '') . '</title>';
            $data .= '<link>' . $blogPostUrl . '</link>';
            $data .= '<description><![CDATA[Read the full article at <a href="' . $blogPostUrl . '">' . $blogPostUrl . '</a>]]></description>';
            $data .= '<pubDate>' . (isset($blogPost['publishedTime']) ? date('r', $blogPost['publishedTime']) : '') . '</pubDate>';
            $data .= '<guid isPermaLink="false">' . $blogPostUrl . '</guid>';
            $data .= '</item>';
        }
        $response = new App\Response('<?xml version="1.0" encoding="UTF-8"?><rss xmlns:atom="http://www.w3.org/2005/Atom" version="2.0"><channel>' . $data . '</channel></rss>');
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }

    static function handleSitemap()
    {
        $app = App::get();
        $urls = [];
        $baseUrl = $app->request->base;
        $addUrl = function($path) use (&$urls, $baseUrl) {
            $urls[] = '<url><loc>' . $baseUrl . $path . '</loc></url>';
        };
        $addUrl('/');

        $list = \BearCMS\Internal\Data\Pages::getPathsList('published');
        foreach ($list as $path) {
            $addUrl($path);
        }

        $list = \BearCMS\Internal\Data\Blog::getSlugsList('published');
        foreach ($list as $slug) {
            $addUrl(Options::$blogPagesPathPrefix . $slug . '/');
        }

        $response = new App\Response('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.google.com/schemas/sitemap/0.84">' . implode('', $urls) . '</urlset>');
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }

    static function handleRobots()
    {
        $response = new App\Response('User-agent: *
Disallow:');
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }

}
