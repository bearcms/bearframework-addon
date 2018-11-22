<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearCMS\Internal;

return function($data, $response) {
    $response1 = $response['value'];
    $response2 = ['js' => 'var e=document.querySelector(\'#' . $data['elementID'] . '\');if(e){html5DOMDocument.evalElement(e);}'];
    $response['value'] = Internal\Server::mergeAjaxResponses($response1, $response2);
};
