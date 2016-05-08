<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS;

use BearFramework\App;
use BearCMS\Internal\Cookies;
use BearCMS\Internal\Data as InternalData;

class CurrentTemplate
{

    private static $cache = [];

    public function getID()
    {
        if (!isset(self::$cache['id'])) {
            $cookies = Cookies::getList(Cookies::TYPE_SERVER);
            self::$cache['id'] = isset($cookies['tmpr']) ? $cookies['tmpr'] : InternalData\Templates::getActiveTemplateID();
        }
        return self::$cache['id'];
    }

    public function getOptions()
    {
        $app = App::$instance;
        if (!isset(self::$cache['options'])) {
            $currentTemplateID = $this->getID();
            $result = $app->bearCMS->data->templates->getOptions($currentTemplateID);
            if ($app->bearCMS->currentUser->exists()) {
                $userOptions = $app->bearCMS->data->templates->getTempOptions($currentTemplateID, $app->bearCMS->currentUser->getID());
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

    public function getFontFamily($fontName)
    {
        if (!is_string($fontName)) {
            throw new \InvalidArgumentException('');
        }
        if (substr($fontName, 0, 12) === 'googlefonts:') {
            $fontName = substr($fontName, 12);
            return strpos($fontName, ' ') !== false ? '"' . $fontName . '"' : $fontName;
        } else {
            $data['Arial,Helvetica,sans-serif'] = 'Arial';
            $data['"Arial Black",Gadget,sans-serif'] = 'Arial Black';
            $data['"Comic Sans MS",cursive,sans-serif'] = 'Comic Sans';
            $data['"Courier New",Courier,monospace'] = 'Courier';
            $data['Georgia,serif'] = 'Georgia';
            $data['Impact,Charcoal,sans-serif'] = 'Impact';
            $data['"Lucida Sans Unicode","Lucida Grande",sans-serif'] = 'Lucida';
            $data['"Lucida Console",Monaco,monospace'] = 'Lucida Console';
            $data['"Palatino Linotype","Book Antiqua",Palatino,serif'] = 'Palatino';
            $data['Tahoma,Geneva,sans-serif'] = 'Tahoma';
            $data['"Times New Roman",Times,serif'] = 'Times New Roman';
            $data['"Trebuchet MS",Helvetica,sans-serif'] = 'Trebuchet';
            $data['Verdana,Geneva,sans-serif'] = 'Verdana';
            $key = array_search($fontName, $data);
            if ($key !== false) {
                return $key;
            }
            return 'unknown';
        }
    }

    public function getFontsHTML($fontNames)
    {
        if (!is_array($fontNames) && !is_string($fontNames)) {
            throw new \InvalidArgumentException('');
        }
        if (!is_array($fontNames)) {
            $fontNames = [$fontNames];
        }
        $fontNames = array_unique($fontNames);
        $result = '<html><head>';
        foreach ($fontNames as $fontName) {
            if (substr($fontName, 0, 12) === 'googlefonts:') {
                $fontName = substr($fontName, 12);
                $result .= '<link href="//fonts.googleapis.com/css?family=' . urlencode($fontName) . '" rel="stylesheet" type="text/css" />';
            }
        }
        $result .= '</head></html>';
        return $result;
    }

}
