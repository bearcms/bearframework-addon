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
class TempClientData
{

    /**
     * 
     * @param string $key
     * @return mixed|boolean
     */
    static function get(string $key)
    {
        $app = App::get();
        $dataHash = substr($key, 0, 32);
        try {
            $data = gzuncompress($app->encryption->decrypt(base64_decode(substr($key, 32))));
        } catch (\Exception $e) {
            return false;
        }
        if (md5($data) !== $dataHash) {
            return false;
        }
        $data = json_decode($data, true);
        if (is_array($data) && isset($data[0], $data[1]) && $data[0] === 'bearcms') {
            return $data[1];
        }
        return false;
    }

    /**
     * 
     * @param mixed $data
     * @return string
     */
    static function set($data): string
    {
        $app = App::get();
        $encodedData = json_encode(['bearcms', $data], JSON_THROW_ON_ERROR);
        return md5($encodedData) . base64_encode($app->encryption->encrypt(gzcompress($encodedData)));
    }

}
