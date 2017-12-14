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

    $themes = BearCMS\Internal\Themes::getList();
    foreach ($themes as $id) {
        if ($id === $data['id']) {
            $styles = BearCMS\Internal\Themes::getStyles($id, true);
            return $styles;
        }
    }
    return null;
};
