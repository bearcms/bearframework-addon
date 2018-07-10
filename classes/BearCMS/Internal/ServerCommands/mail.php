<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

return function($data) {
    $app = App::get();

    $defaultEmailSender = \BearCMS\Internal\Options::$defaultEmailSender;
    if (!is_array($defaultEmailSender)) {
        throw new \Exception('The defaultEmailSender option is empty.');
    }
    $email = $app->emails->make();
    $email->sender->email = $defaultEmailSender['email'];
    $email->sender->name = $defaultEmailSender['name'];
    $email->subject = $data['subject'];
    $email->content->add($data['body']);
    $email->recipients->add($data['recipient']);
    $app->emails->send($email);
    return 1;
};
