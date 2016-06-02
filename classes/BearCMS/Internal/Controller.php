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

class Controller
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
                return new App\Response\HTML($content);
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
            $app->filesystem->makeFileDir($filename);
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

}
