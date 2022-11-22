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
class Downloads
{
    /**
     * Downloads a file and caches it locally
     * 
     * @param string $url
     * @param boolean $useCached
     * @return string
     */
    static function download(string $url, bool $useCached = false): string
    {
        $app = App::get();
        $filename = $app->data->getFilename('.temp/bearcms/downloads/' . md5($url) . '.' . pathinfo($url, PATHINFO_EXTENSION));
        if (!is_file($filename) || !$useCached) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $response = (string)curl_exec($ch);
            $valid = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200 && strlen($response) > 0;
            $error = curl_error($ch);
            curl_close($ch);
            if ($valid) {
                file_put_contents($filename, $response);
            } else {
                throw new \Exception('Cannot download file from URL (' . $url . ', ' . $error . ')');
            }
        }
        return $filename;
    }
}
