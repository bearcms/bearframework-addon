<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal\Server;

return function($data, $response) {
    $app = App::get();
    $value = json_encode($response['value']);
    $content = $app->components->process($data['content']);
    $domDocument = new \IvoPetkov\HTML5DOMDocument();
    $domDocument->loadHTML($content);
    $bodyElement = $domDocument->querySelector('body');
    $content = $bodyElement->innerHTML;
    $bodyElement->parentNode->removeChild($bodyElement);
    $allButBody = $domDocument->saveHTML();
    $startPosition = strpos($value, '{bearcms-replace-content-' . $data['id'] . '-');
    if ($startPosition === false) {
        return;
    }

    $endPosition = strpos($value, '}', $startPosition);

    $modificationsString = substr($value, $startPosition + 58, $endPosition - $startPosition - 58);
    $parts = explode('\'', $modificationsString);
    $singleQuoteSlashesCount = strlen($parts[0]);
    $doubleQuoteSlashesCount = strlen($parts[1]) - 1;
    for ($i = 0; $i < $doubleQuoteSlashesCount; $i += 2) {
        $content = substr(json_encode($content), 1, -1);
    }
    for ($i = 0; $i < $singleQuoteSlashesCount; $i += 2) {
        $content = addslashes($content);
    }
    $value = str_replace(substr($value, $startPosition, $endPosition - $startPosition + 1), $content, $value);
    //todo optimize
    $response1 = ['js' => 'html5DOMDocument.insert(' . json_encode($allButBody, true) . ');'];
    $response2 = json_decode($value, true);
    $response['value'] = Server::mergeAjaxResponses($response1, $response2);
};
