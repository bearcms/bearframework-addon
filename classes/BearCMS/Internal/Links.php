<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

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
            }
            $url = "javascript:void(0);";
        }
        return [$url, $onClick, $html !== null ? '<component src="data:base64,' . base64_encode($html) . '" />' : null];
    }
}
