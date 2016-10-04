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

final class Controller
{

    static function handleAdminPage()
    {
        $app = App::$instance;
        $path = (string) $app->request->path;
        if ($path === '/admin/') {
            if (!$app->bearCMS->data->users->hasUsers()) {
                return new App\Response\TemporaryRedirect($app->request->base . '/admin/firstrun/');
            }
        } elseif ($path === '/admin/firstrun/') {
            if ($app->bearCMS->data->users->hasUsers()) {
                return new App\Response\TemporaryRedirect($app->request->base . '/admin/');
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
        $response->headers[] = 'X-Robots-Tag: noindex';
        return $response;
    }

    static function handleFileUpload()
    {
        $app = App::$instance;
        if (isset($_FILES['Filedata']) && isset($_FILES['Filedata']["name"]) && !$_FILES['Filedata']["error"] && is_file($_FILES['Filedata']["tmp_name"])) {
            $originalFilename = strtolower($_FILES['Filedata']["name"]);
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
            move_uploaded_file($_FILES['Filedata']["tmp_name"], $filename);
            if (is_file($filename)) {
                $response = Server::call('fileupload', array('tempFilename' => $tempFilename, 'requestData' => json_encode($_GET)));
                if (isset($response['result'])) {
                    return new App\Response\JSON($response['result']);
                } else {
                    return new App\Response\TemporaryUnavailable();
                }
            }
        }
        $response = new App\Response();
        $response->headers['contentType'] = 'Content-Type: text/json; charset=UTF-8';
        $response->headers['serviceUnavailable'] = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1') . ' 400 Bad Request';
        return $response;
    }

    static function handleFileRequest($preview)
    {
        $app = App::$instance;
        $filename = (string) $app->request->path[2];
        $data = \BearCMS\Internal\Data\Files::getFileData($filename);
        if ($data === false || $data['published'] === 0) {
            return new App\Response\NotFound();
        } else {
            $fullFilename = $app->data->getFilename('bearcms/files/custom/' . $filename);
            $response = new App\Response\FileReader($fullFilename);
            $mimeType = $app->assets->getMimeType($fullFilename);
            if ($mimeType !== null) {
                $response->headers[] = 'Content-Type: ' . $mimeType;
            }
            if (!$preview) {
                $response->headers[] = 'Content-Disposition: attachment; filename=' . urlencode($filename);
                $response->headers[] = 'Content-Type: application/force-download';
                $response->headers[] = 'Content-Type: application/octet-stream';
                $response->headers[] = 'Content-Type: application/download';
                $response->headers[] = 'Content-Description: File Transfer';
                $response->headers[] = 'Content-Length: ' . filesize($fullFilename);
            }
            return $response;
        }
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
        $app = App::$instance;
        $settings = $app->bearCMS->data->settings->get();
        $baseUrl = $app->request->base;

        $data = '<title>' . (isset($settings['title']) ? htmlspecialchars($settings['title']) : '') . '</title>';
        $data .= '<link>' . $baseUrl . '/</link>';
        $data .= '<description>' . (isset($settings['description']) ? htmlspecialchars($settings['description']) : '') . '</description>';
        $data .= '<language>' . (isset($settings['language']) ? htmlspecialchars($settings['language']) : '') . '</language>';
        $data .= '<atom:link href="' . $baseUrl . '/rss.xml" rel="self" type="application/rss+xml">';
        $data .= '</atom:link>';

        $blogPosts = $app->bearCMS->data->blog->getList(['PUBLISHED_ONLY', 'SORT_BY_PUBLISHED_TIME_DESC']);
        foreach ($blogPosts as $blogPost) {
            $blogPostUrl = isset($blogPost['slug']) ? $baseUrl . '/b/' . $blogPost['slug'] . '/' : '';
            $data .= '<item>';
            $data .= '<title>' . (isset($blogPost['title']) ? htmlspecialchars($blogPost['title']) : '') . '</title>';
            $data .= '<link>' . $blogPostUrl . '</link>';
            $data .= '<description><![CDATA[Read the full article at <a href="' . $blogPostUrl . '">' . $blogPostUrl . '</a>]]></description>';
            $data .= '<pubDate>' . (isset($blogPost['publishedTime']) ? date('r', $blogPost['publishedTime']) : '') . '</pubDate>';
            $data .= '<guid isPermaLink="false">' . $blogPostUrl . '</guid>';
            $data .= '</item>';
        }
        $response = new App\Response('<?xml version="1.0" encoding="UTF-8"?><rss xmlns:atom="http://www.w3.org/2005/Atom" version="2.0"><channel>' . $data . '</channel></rss>');
        $response->setContentType('text/xml');
        return $response;
    }

    static function handleSitemap()
    {
        $app = App::$instance;
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
            $addUrl('/b/' . $slug . '/');
        }

        $response = new App\Response('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.google.com/schemas/sitemap/0.84">' . implode('', $urls) . '</urlset>');
        $response->setContentType('text/xml');
        return $response;
    }

    static function handleRobots()
    {
        $response = new App\Response('User-agent: *
Disallow:');
        $response->setContentType('text/plain');
        return $response;
    }

}
