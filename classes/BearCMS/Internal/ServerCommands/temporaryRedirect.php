<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal\Cookies;

return function($data, $response) {
    $app = App::get();
    if (!isset($data['url'])) {
        throw new Exception('');
    }
    Cookies::setList(Cookies::TYPE_SERVER, Cookies::parseServerCookies($response['headers']));
    $response = new App\Response\TemporaryRedirect($data['url']);
    Cookies::apply($response);
    $app->respond($response);
    exit;
};
