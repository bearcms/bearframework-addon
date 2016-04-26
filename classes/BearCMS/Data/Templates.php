<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data;

use BearFramework\App;

class Templates
{

    /**
     * 
     * @param string $id
     * @return array
     */
    static function getOptions($id)
    {
        $app = App::$instance;
        $data = $app->data->get(
                [
                    'key' => 'bearcms/templates/template/' . md5($id) . '.json',
                    'result' => ['body']
                ]
        );
        if (isset($data['body'])) {
            $data = json_decode($data['body'], true);
            if (isset($data['options'])) {
                return $data['options'];
            }
        }
        return [];
    }

    /**
     * 
     * @param string $id
     * @param string $userID
     * @return array
     */
    static function getTempOptions($id, $userID)
    {
        $app = App::$instance;
        $data = $app->data->get(
                [
                    'key' => '.temp/bearcms/user-template-options/' . md5($userID) . '/' . md5($id) . '.json',
                    'result' => ['body']
                ]
        );
        if (isset($data['body'])) {
            $data = json_decode($data['body'], true);
            if (isset($data['options'])) {
                return $data['options'];
            }
        }
        return [];
    }

}
