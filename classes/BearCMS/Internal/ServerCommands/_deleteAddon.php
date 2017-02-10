<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

return function($data) {
    $app = App::get();
    if (!isset($data['id'])) {
        throw new Exception('');
    }
    $context = $app->context->get(__FILE__);
    $app->maintenance->addons->delete($context->options['addonsDir'], $data['id']);
};
