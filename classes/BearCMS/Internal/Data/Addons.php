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
 * @codeCoverageIgnore
 */
class Addons
{

    /**
     *
     * @var array 
     */
    static public $registrations = [];

    /**
     * 
     * @return \BearFramework\DataList
     */
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

    /**
     * 
     * @param string $addonID
     * @return \IvoPetkov\DataObject|null
     */
    static function get(string $addonID): ?\IvoPetkov\DataObject
    {
        $data = self::getData($addonID);
        if ($data !== null) {
            return self::makeFromRawData(json_encode($data, JSON_THROW_ON_ERROR));
        }
        return null;
    }

    /**
     * 
     * @param string $raw
     * @return \IvoPetkov\DataObject
     */
    static function makeFromRawData(string $raw): \IvoPetkov\DataObject
    {
        $data = json_decode($raw, true);
        return new \IvoPetkov\DataObject([
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

    /**
     * 
     * @param string $addonID
     * @return array|null
     */
    static function getData(string $addonID): ?array
    {
        $app = App::get();
        $dataKey = 'bearcms/addons/addon/' . md5($addonID) . '.json';
        $value = $app->data->getValue($dataKey);
        if ($value !== null) {
            return json_decode($value, true);
        }
        return null;
    }

    /**
     * 
     * @param string $addonID
     * @param type $data
     * @return void
     */
    static function setData(string $addonID, $data): void
    {
        $app = App::get();
        $dataKey = 'bearcms/addons/addon/' . md5($addonID) . '.json';
        $app->data->setValue($dataKey, json_encode($data, JSON_THROW_ON_ERROR));
        self::onChange();
    }

    /**
     * 
     * @param string $addonID
     * @return void
     */
    static function add(string $addonID): void
    {
        $manager = Config::getAddonManager();
        $manager->addAddon($addonID);
        $data = self::getData($addonID);
        if ($data === null) {
            $data = ['id' => $addonID];
            self::setData($addonID, $data);
        }
    }

    /**
     * 
     * @param string $addonID
     * @return void
     */
    static function delete(string $addonID): void
    {
        $manager = Config::getAddonManager();
        $manager->removeAddon($addonID);
        $app = App::get();
        $dataKey = 'bearcms/addons/addon/' . md5($addonID) . '.json';
        $app->data->delete($dataKey);
        self::onChange();
    }

    /**
     * 
     * @param string $addonID
     * @return void
     */
    static function enable(string $addonID): void
    {
        self::enableOrDisable($addonID, true);
    }

    /**
     * 
     * @param string $addonID
     * @return void
     */
    static function disable(string $addonID): void
    {
        self::enableOrDisable($addonID, false);
    }

    /**
     * 
     * @param string $addonID
     * @param bool $enable
     * @return void
     */
    static function enableOrDisable(string $addonID, bool $enable): void
    {
        $data = self::getData($addonID);
        if ($data !== null) {
            $data['enabled'] = (int) $enable;
            self::setData($addonID, $data);
        }
    }

    /**
     * 
     * @param string $addonID
     * @param array $options
     * @return void
     */
    static function setOptions(string $addonID, array $options): void
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

    /**
     * 
     * @return void
     */
    static function onChange(): void
    {
        $app = App::get();
        $cacheKey = 'bearcms-addons-to-add';
        $app->cache->delete($cacheKey);
    }

    //    static function validateOptions($definition, $values)
    //    {
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
    //    }

    /**
     * 
     * @return void
     */
    static function addToApp(): void
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
        if (!empty($addonIDsToAdd)) {
            $bearCMSAddons = $app->bearCMS->addons;
            foreach ($addonIDsToAdd as $addonID) {
                $bearCMSAddons->add($addonID);
            }
        }
    }
}
