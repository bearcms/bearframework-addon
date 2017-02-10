<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearCMS\Internal\Server;

return function($data, $response) {
    $response1 = json_decode($response['body'], true);
    $response2 = ['js' => 'var e=document.querySelector(\'#' . $data['elementID'] . '\');if(e){html5DOMDocument.evalElement(e);}'];
    $response['body'] = json_encode(Server::mergeAjaxResponses($response1, $response2));
};
