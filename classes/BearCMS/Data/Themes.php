<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data;

use BearFramework\App;

/**
 * Information about the site themes
 */
class Themes
{

    /**
     * Returns a list containing the options for the theme specified
     * 
     * @param string $id The id of the theme
     * @return array A list containing the theme options
     * @throws \InvalidArgumentException
     */
    public function getOptions($id)
    {
        if (!is_string($id)) {
            throw new \InvalidArgumentException('');
        }
        $app = App::$instance;
        $data = $app->data->get(
                [
                    'key' => 'bearcms/themes/theme/' . md5($id) . '.json',
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
     * Returns a list containing the theme options a specific user has made
     * 
     * @param array $id The id of the theme
     * @param array $userID The id of the user
     * @return array A list containing the theme options
     * @throws \InvalidArgumentException
     */
    public function getTempOptions($id, $userID)
    {
        if (!is_string($id)) {
            throw new \InvalidArgumentException('');
        }
        if (!is_string($userID)) {
            throw new \InvalidArgumentException('');
        }
        $app = App::$instance;
        $data = $app->data->get(
                [
                    'key' => '.temp/bearcms/userthemeoptions/' . md5($userID) . '/' . md5($id) . '.json',
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
