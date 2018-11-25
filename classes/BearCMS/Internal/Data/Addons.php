<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearFramework\App;
use BearCMS\Internal\Config;

/**
 * @internal
 */
class Addons
{

    static public $announcements = [];

    static function getList(): \BearFramework\DataList
    {
        $app = App::get();
        $list = $app->data->getList()
                ->filterBy('key', 'bearcms/addons/addon/', 'startWith');
        $result = new \BearFramework\DataList();
        foreach ($list as $item) {
            $result[] = self::makeFromRawData($item->value);
        }
        return $result;
    }

    static function get(string $addonID): ?\BearCMS\Internal\DataObject
    {
        $data = self::getData($addonID);
        if ($data !== null) {
            return self::makeFromRawData(json_encode($data));
        }
        return null;
    }

    static function makeFromRawData(string $raw)
    {
        $data = json_decode($raw, true);
        return new \BearCMS\Internal\DataObject([
            'id' => $data['id'],
            'enabled' => (isset($data['enabled']) ? (int) $data['enabled'] > 0 : false),
            'exists' => \BearFramework\Addons::exists($data['id']),
            'options' => (isset($data['options']) ? $data['options'] : []),
        ]);
//        $includeOptions = isset($data['includeOptions']) && !empty($data['includeOptions']);
//        $addonManifestData = BearCMS\Internal\Data\Addons::getManifestData($data['id']);
//        if (is_array($addonManifestData)) {
//            $addonData['hasOptions'] = isset($addonManifestData['options']) && !empty($addonManifestData['options']);
//            if ($includeOptions) {
//                $addonData['options'] = [];
//                $addonData['options']['definition'] = isset($addonManifestData['options']) ? $addonManifestData['options'] : [];
//                $addonData['options']['values'] = $optionsValues;
//                $addonData['options']['valid'] = BearCMS\Internal\Data\Addons::validateOptions($addonData['options']['definition'], $addonData['options']['values']);
//            }
//            unset($addonManifestData['options']);
//            $addonData = array_merge($addonData, $addonManifestData);
//        } else {
//            $addonData['hasOptions'] = false;
//            if ($includeOptions) {
//                $addonData['options'] = [];
//                $addonData['options']['definition'] = [];
//                $addonData['options']['values'] = [];
//                $addonData['options']['valid'] = true;
//            }
//        }
    }

    static function getData(string $addonID)
    {
        $app = App::get();
        $dataKey = 'bearcms/addons/addon/' . md5($addonID) . '.json';
        $value = $app->data->getValue($dataKey);
        if ($value !== null) {
            return json_decode($value, true);
        }
        return null;
    }

    static function setData(string $addonID, $data)
    {
        $app = App::get();
        $dataKey = 'bearcms/addons/addon/' . md5($addonID) . '.json';
        $app->data->setValue($dataKey, json_encode($data));
        self::onChange();
    }

    static function add(string $addonID)
    {
        $manager = Config::getAddonManager();
        $manager->addAddon($addonID);
        $data = self::getData($addonID);
        if ($data === null) {
            $data = ['id' => $addonID];
            self::setData($addonID, $data);
        }
    }

    static function delete(string $addonID)
    {
        $manager = Config::getAddonManager();
        $manager->removeAddon($addonID);
        $app = App::get();
        $dataKey = 'bearcms/addons/addon/' . md5($addonID) . '.json';
        $app->data->delete($dataKey);
        self::onChange();
    }

    static function enable(string $addonID)
    {
        self::enableOrDisable($addonID, true);
    }

    static function disable(string $addonID)
    {
        self::enableOrDisable($addonID, false);
    }

    static function enableOrDisable(string $addonID, bool $enable)
    {
        $data = self::getData($addonID);
        if ($data !== null) {
            $data['enabled'] = (int) $enable;
            self::setData($addonID, $data);
        }
    }

    static function setOptions(string $addonID, array $options)
    {
        $data = self::getData($addonID);
        if ($data !== null) {
            if (empty($options)) {
                if (isset($data['options'])) {
                    unset($data['options']);
                }
            } else {
                $data['options'] = $options;
            }
            self::setData($addonID, $data);
        }
    }

    static function onChange()
    {
        $app = App::get();
        $cacheKey = 'bearcms-addons-to-add';
        $app->cache->delete($cacheKey);
    }

    static function validateOptions($definition, $values)
    {
//        foreach ($definition as $optionData) {
//            if (isset($optionData['id'])) {
//                $id = $optionData['id'];
//                $validations = isset($optionData['validations']) ? $optionData['validations'] : [];
//                if (empty($validations)) {
//                    continue;
//                }
//                $isValid = true;
//                foreach ($validations as $validationData) {
//                    if (isset($validationData[0])) {
//                        if ($validationData[0] === 'required') {
//                            if (!isset($values[$id]) || strlen($values[$id]) === 0) {
//                                $isValid = false;
//                                break;
//                            }
//                        }
//                    }
//                }
//                if (!$isValid) {
//                    return false;
//                }
//            }
//        }
//        return true;
    }

    static function addToApp()
    {
        $app = App::get();
        $cacheKey = 'bearcms-addons-to-add';
        $addonIDsToAdd = $app->cache->getValue($cacheKey);
        if ($addonIDsToAdd === null) {
            $addonIDsToAdd = [];
            $list = self::getList();
            $list->filterBy('enabled', true);
            foreach ($list as $item) {
                $addonIDsToAdd[] = $item->id;
            }
            $app->cache->set($app->cache->make($cacheKey, $addonIDsToAdd));
        }
        foreach ($addonIDsToAdd as $addonID) {
            if (\BearFramework\Addons::exists($addonID)) {
                $app->addons->add($addonID);
                if (isset(self::$announcements[$addonID])) {
                    $addon = new \BearCMS\Addons\Addon($addonID);
                    call_user_func(self::$announcements[$addonID], $addon);
                    if (is_callable($addon->initialize)) {
                        call_user_func($addon->initialize);
                    }
                }
            }
        }
    }

}
