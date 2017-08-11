<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;

class TempClientData
{

    static function get($key)
    {
        $app = App::get();
        $dataHash = substr($key, 0, 32);
        try {
            $data = gzuncompress($app->encryption->decrypt(base64_decode(substr($key, 32))));
        } catch (\Exception $e) {
            return;
        }
        if (md5($data) !== $dataHash) {
            return;
        }
        $data = json_decode($data, true);
        if (is_array($data) && isset($data[0], $data[1]) && $data[0] === 'bearcms') {
            return $data[1];
        }
        return false;
    }

    static function set($data)
    {
        $app = App::get();
        $encodedData = json_encode(['bearcms', $data]);
        return md5($encodedData) . base64_encode($app->encryption->encrypt(gzcompress($encodedData)));
    }

}
