<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal;

return function($data, $response) {
    $app = App::get();
    if (!isset($data['url'])) {
        throw new Exception('');
    }
    Internal\Cookies::setList(Internal\Cookies::TYPE_SERVER, Internal\Cookies::parseServerCookies($response['headers']));
    $response = new App\Response\TemporaryRedirect($data['url']);
    Internal\Cookies::apply($response);
    $app->respond($response);
    exit;
};
