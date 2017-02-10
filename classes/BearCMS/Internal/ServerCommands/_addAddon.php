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
    if (isset($data['type']) && isset($data['value'])) {
        $filenameOrUrl = null;
        if ($data['type'] === 'url') {
            $filenameOrUrl = $data['value'];
        } elseif ($data['type'] === 'file') {
            $filename = $app->data->getFilename('.temp/bearcms/files/' . $data['value']);
            if (is_file($filename)) {
                $filenameOrUrl = $filename;
            }
        }
        try {
            $id = $app->maintenance->addons->getID($filenameOrUrl);
        } catch (Exception $e) {
            return ['error' => 'invalidValue'];
        }
        if (BearFramework\Addons::exists($id)) {
            $result = $app->data->getValue('bearcms/addons/addon/' . md5($id) . '.json');
            if ($result !== null) { // Not managed by Bear CMS
                return ['error' => 'notManagedByBearCMS'];
            }
        }
        try {
            $context = $app->context->get(__FILE__);
            $id = $app->maintenance->addons->install($context->options['addonsDir'], $filenameOrUrl);
            return $id;
        } catch (Exception $e) {
            return ['error' => 'invalidValue'];
        }
    }
    return null;
};
