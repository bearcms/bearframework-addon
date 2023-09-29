<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;

/**
 * @internal
 * @codeCoverageIgnore
 */
class Links
{
    /**
     * 
     * @param string $url
     * @return array [$url, $onClick, $html]
     */
    static function updateURL(string $url): array
    {
        $onClick = null;
        $html = null;
        if (strpos($url, 'scroll:') === 0) {
            $scrollLocation = substr($url, 7);
            if ($scrollLocation === 'top') {
                $onClick = "window.scroll({top:0,behavior:'smooth'});";
            } elseif (strpos($scrollLocation, '#') === 0) {
                $onClick = "try{document.querySelector('" . $scrollLocation . "').scrollIntoView({behavior:'smooth'})}catch(e){};";
            } elseif (strpos($scrollLocation, 'tag#') === 0) {
                $onClick = "try{bearCMS.tags.getElement('" . substr($scrollLocation, 3) . "').scrollIntoView({behavior:'smooth'})}catch(e){};";
            }
            $url = null;
        } elseif (strpos($url, 'js:') === 0) {
            $onClick = substr($url, 3);
            $url = null;
        } elseif (strpos($url, 'bearcms-lightbox:') === 0) {
            $contentID = substr($url, 17);
            $onClick = "bearCMS.lightboxContent.open(" . json_encode($contentID) . ");";
            $url = null;
            $html = '<html><head><link rel="client-packages-embed" name="bearcms-lightbox-content"></head></html>';
        }
        return [$url, $onClick, $html !== null ? '<component src="data:base64,' . base64_encode($html) . '" />' : null];
    }
}
