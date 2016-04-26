<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS;

use \BearCMS\Internal\Cookies;
use \BearCMS\CurrentUser;
use \BearCMS\Internal\Data as InternalData;

class CurrentTemplate
{

    private static $cache = [];

    static function getID()
    {
        if (!isset(self::$cache['id'])) {
            $cookies = Cookies::getList(Cookies::TYPE_SERVER);
            self::$cache['id'] = isset($cookies['tmpr']) ? $cookies['tmpr'] : InternalData\Templates::getActiveTemplateID();
        }
        return self::$cache['id'];
    }

    static function getOptions()
    {
        if (!isset(self::$cache['options'])) {
            $currentTemplateID = self::getID();
            $result = \BearCMS\Data\Templates::getOptions($currentTemplateID);
            if (CurrentUser::exists()) {
                $userOptions = \BearCMS\Data\Templates::getTempOptions($currentTemplateID, CurrentUser::getID());
                if (!empty($userOptions)) {
                    $result = array_merge($result, $userOptions);
                }
            }
            // todo optimize
            $templates = \BearCMS\Internal\Data\Templates::getTemplatesList();
            foreach ($templates as $template) {
                if ($template['id'] === $currentTemplateID) {
                    if (isset($template['manifestFilename'])) {
                        $manifestData = \BearCMS\Internal\Data\Templates::getManifestData($template['manifestFilename'], $template['dir']);
                        if (isset($manifestData['options'])) {
                            $walkOptions = function($options) use (&$result, &$walkOptions) {
                                foreach ($options as $option) {
                                    if (isset($option['id']) && !isset($result[$option['id']])) {
                                        $result[$option['id']] = isset($option['defaultValue']) ? $option['defaultValue'] : null;
                                    }
                                    if (isset($option['options'])) {
                                        $walkOptions($option['options']);
                                    }
                                }
                            };
                            $walkOptions($manifestData['options']);
                        }
                    }
                    break;
                }
            }
            self::$cache['options'] = $result;
        }
        return self::$cache['options'];
    }

}
