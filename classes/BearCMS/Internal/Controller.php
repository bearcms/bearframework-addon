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
            $originalFileName = strtolower($_FILES['Filedata']["name"]);
            $pathinfo = pathinfo($originalFileName);
            $fileExtension = isset($pathinfo['extension']) ? $pathinfo['extension'] : '';
            $tempFileName = md5('fileupload' . uniqid()) . (isset($fileExtension{0}) ? '.' . $fileExtension : '');
            $filename = $app->data->getFilename('.temp/bearcms/files/' . $tempFileName);
            $app->filesystem->makeFileDir($filename);
            move_uploaded_file($_FILES['Filedata']["tmp_name"], $filename);
            if (is_file($filename)) {
                $response = Server::call('fileupload', array('tempFileName' => $tempFileName, 'requestData' => json_encode($_GET)));
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

}
