<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearFramework\App;

final class Addons
{

    static function getList()
    {
        $app = App::get();
        $list = $app->data->getList()
                ->filterBy('key', 'bearcms/addons/addon/', 'startWith');

        $temp = [];
        foreach ($list as $item) {
            $addonData = json_decode($item->value, true);
            $temp[] = [
                'id' => $addonData['id'],
                'enabled' => (isset($addonData['enabled']) ? (int) $addonData['enabled'] > 0 : false)
            ];
        }
        return $temp;
    }

    static function getManifestData($name)
    {
        if (\BearFramework\Addons::exists($name)) {
            $addonData = \BearFramework\Addons::get($name);
            $addonOptions = $addonData['options'];
            if (isset($addonOptions['bearCMS']) && is_array($addonOptions['bearCMS']) && isset($addonOptions['bearCMS']['manifest']) && is_string($addonOptions['bearCMS']['manifest'])) {
                $filename = $addonData['dir'] . '/' . $addonOptions['bearCMS']['manifest'];
                if (is_file($filename)) {
                    $data = json_decode(file_get_contents($filename), true);
                    if (isset($data['media']) && is_array($data['media'])) {
                        foreach ($data['media'] as $i => $media) {
                            if (isset($media['filename']) && is_string($media['filename'])) {
                                $data['media'][$i]['filename'] = $addonData['dir'] . '/' . $media['filename'];
                            }
                        }
                    }
                    return $data;
                }
            }
        }
        return null;
    }

    static function validateOptions($definition, $values)
    {
        foreach ($definition as $optionData) {
            if (isset($optionData['id'])) {
                $id = $optionData['id'];
                $validations = isset($optionData['validations']) ? $optionData['validations'] : [];
                if (empty($validations)) {
                    continue;
                }
                $isValid = true;
                foreach ($validations as $validationData) {
                    if (isset($validationData[0])) {
                        if ($validationData[0] === 'required') {
                            if (!isset($values[$id]) || strlen($values[$id]) === 0) {
                                $isValid = false;
                                break;
                            }
                        }
                    }
                }
                if (!$isValid) {
                    return false;
                }
            }
        }
        return true;
    }

}
