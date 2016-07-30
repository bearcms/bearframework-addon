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
        return $this->walkOptions(1);
    }

    /**
     * 
     * @param int $resultType 1 - values, 2 - definition
     * @return []
     */
    private function walkOptions($resultType)
    {
        $cacheKey = 'options' . $resultType; //todo optimize
        $app = App::$instance;
        if (!isset(self::$cache[$cacheKey])) {
            $currentTemplateID = $this->getID();
            $result = [];
            $values = $app->bearCMS->data->templates->getOptions($currentTemplateID);
            if ($app->bearCMS->currentUser->exists()) {
                $userOptions = $app->bearCMS->data->templates->getTempOptions($currentTemplateID, $app->bearCMS->currentUser->getID());
                if (!empty($userOptions)) {
                    $values = array_merge($values, $userOptions);
                }
            }
// todo optimize
            $templates = \BearCMS\Internal\Data\Templates::getTemplatesList();
            foreach ($templates as $template) {
                if ($template['id'] === $currentTemplateID) {
                    if (isset($template['manifestFilename'])) {
                        $manifestData = \BearCMS\Internal\Data\Templates::getManifestData($template['manifestFilename'], $template['dir']);
                        if (isset($manifestData['options'])) {
                            $walkOptions = function($options) use (&$result, $values, &$walkOptions, $resultType) {
                                foreach ($options as $option) {
                                    if (isset($option['id'])) {
                                        if (isset($values[$option['id']])) {
                                            $result[$option['id']] = $values[$option['id']];
                                        } else {
                                            $result[$option['id']] = isset($option['defaultValue']) ? (is_array($option['defaultValue']) ? json_encode($option['defaultValue']) : $option['defaultValue']) : null;
                                        }
                                        if ($resultType === 2) {
                                            $result[$option['id']] = [$result[$option['id']], $option];
                                        }
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
            self::$cache[$cacheKey] = new \BearCMS\CurrentTemplateOptions($result);
        }
        return self::$cache[$cacheKey];
    }

    public function getOptionsCss()
    {
        $app = App::$instance;
        $result = [];
        $options = $this->walkOptions(2);
        $applyImageUrls = function($text) use ($app) {
            $matches = [];
            preg_match_all('/url\((.*?)\)/', $text, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $key) {
                    $filename = $app->bearCMS->data->getRealFilename($key);
                    $text = str_replace($key, is_file($filename) ? $app->assets->getUrl($filename) : "", $text);
                }
            }
            return $text;
        };

        foreach ($options as $optionData) {
            $optionValue = $optionData[0];
            $optionDefinition = $optionData[1];
            if (isset($optionDefinition['cssOutput'])) {
                $optionType = $optionDefinition['type'];
                foreach ($optionDefinition['cssOutput'] as $outputDefinition) {
                    if (is_array($outputDefinition)) {
                        if (isset($outputDefinition[0], $outputDefinition[1]) && $outputDefinition[0] === 'selector') {
                            $selector = $outputDefinition[1];
                            $selectorVariants = ['', '', ''];
                            if ($optionType === 'css' || $optionType === 'cssText' || $optionType === 'cssTextShadow' || $optionType === 'cssPadding' || $optionType === 'cssBorder' || $optionType === 'cssRadius' || $optionType === 'cssShadow' || $optionType === 'cssBackground') {
                                $temp = strlen($optionValue) > 0 ? json_decode($optionValue, true) : [];
                                foreach ($temp as $key => $value) {
                                    if (substr($key, -6) === ':hover') {
                                        $selectorVariants[1] .= substr($key, 0, -6) . ':' . $value . ';';
                                    } elseif (substr($key, -7) === ':active') {
                                        $selectorVariants[2] .= substr($key, 0, -7) . ':' . $value . ';';
                                    } else {
                                        $selectorVariants[0] .= $key . ':' . $value . ';';
                                    }
                                }
                            }
                            if ($optionType === 'css' || $optionType === 'cssBackground') {
                                $selectorVariants[0] = $applyImageUrls($selectorVariants[0]);
                                $selectorVariants[1] = $applyImageUrls($selectorVariants[1]);
                                $selectorVariants[2] = $applyImageUrls($selectorVariants[2]);
                            }
                            if (strlen($selectorVariants[0]) > 0) {
                                if (!isset($result[$selector])) {
                                    $result[$selector] = '';
                                }
                                $result[$selector] .= $selectorVariants[0];
                            }
                            if (strlen($selectorVariants[1]) > 0) {
                                if (!isset($result[$selector . ':hover'])) {
                                    $result[$selector . ':hover'] = '';
                                }
                                $result[$selector . ':hover'] .= $selectorVariants[1];
                            }
                            if (strlen($selectorVariants[2]) > 0) {
                                if (!isset($result[$selector . ':active'])) {
                                    $result[$selector . ':active'] = '';
                                }
                                $result[$selector . ':active'] .= $selectorVariants[2];
                            }
                        } elseif (isset($outputDefinition[0], $outputDefinition[1], $outputDefinition[2]) && $outputDefinition[0] === 'rule') {
                            $selector = $outputDefinition[1];
                            if (!isset($result[$selector])) {
                                $result[$selector] = '';
                            }
                            $result[$selector] .= $outputDefinition[2];
                        }
                    }
                }
            }
        }
        $temp = '';
        foreach ($result as $key => $value) {
            $temp .= $key . '{' . $value . '}';
        }
        return $temp;
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
