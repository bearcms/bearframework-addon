<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS;

class Fonts
{

    /**
     * 
     * @param string $fontName
     * @return string
     * @throws \InvalidArgumentException
     */
    static function getFontFamily($fontName)
    {
        if (!is_string($fontName)) {
            throw new \InvalidArgumentException('');
        }
        if (substr($fontName, 0, 12) === 'googlefonts\\') {
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

    /**
     * 
     * @param string $fontName
     * @throws \InvalidArgumentException
     */
    static function getHTML($fontName)
    {
        if (!is_string($fontName)) {
            throw new \InvalidArgumentException('');
        }
        if (substr($fontName, 0, 12) === 'googlefonts\\') {
            $fontName = substr($fontName, 12);
            return '<link href="//fonts.googleapis.com/css?family=' . urlencode($fontName) . '" rel="stylesheet" type="text/css" />';
        }
        return '';
    }

}
