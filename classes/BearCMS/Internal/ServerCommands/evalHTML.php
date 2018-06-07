<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearCMS\Internal\Server;

return function($data, $response) {
    $response1 = $response['body'];
    $response2 = ['js' => 'var e=document.querySelector(\'#' . $data['elementID'] . '\');if(e){html5DOMDocument.evalElement(e);}'];
    $response['body'] = Server::mergeAjaxResponses($response1, $response2);
};
