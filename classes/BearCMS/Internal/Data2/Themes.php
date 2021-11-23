<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data2;

use BearFramework\App;
use BearCMS\Internal;

/**
 * @internal
 * @codeCoverageIgnore
 */
class Themes
{

    /**
     * Returns a list containing the options for the theme specified
     * 
     * @param string $id The id of the theme
     * @return array A list containing the theme options
     * @throws \InvalidArgumentException
     */
    public function getValues(string $id): array
    {
        $data = Internal\Data::getValue('bearcms/themes/theme/' . md5($id) . '.json');
        if ($data !== null) {
            $data = json_decode($data, true);
            if (isset($data['options'])) {
                return $data['options'];
            }
        }
        return [];
    }

    /**
     * Returns a list containing the theme options a specific user has made
     * 
     * @param string $id The id of the theme
     * @param string $userID The id of the user
     * @return array A list containing the theme options
     * @throws \InvalidArgumentException
     */
    public function getUserOptions(string $id, string $userID): ?array
    {
        $app = App::get();
        $data = $app->data->getValue('.temp/bearcms/userthemeoptions/' . md5($userID) . '/' . md5($id) . '.json');
        if ($data !== null) {
            $data = json_decode($data, true);
            if (isset($data['options'])) {
                return $data['options'];
            } else {
                return []; // the user wants the default values
            }
        }
        return null;
    }

    /**
     * 
     * @param string $id The theme ID
     * @param array|null $values Option values
     * @param string $userID The user ID
     */
    private function setOptionsValues(string $id, $values, string $userID = null): void
    {
        if (!is_array($values) && $values !== null) {
            throw new \InvalidArgumentException('The values argument is not valid');
        }
        $app = App::get();
        $hasUser = strlen($userID) > 0;
        $dataKeysToDelete = [];

        if ($hasUser) {
            $currentValues = $this->getUserOptions($id, $userID);
            if ($currentValues === null) {
                $currentValues = [];
            }
        } else {
            $currentValues = $this->getValues($id);
        }
        $filesInCurrentValues = Internal\Themes::getFilesInValues($currentValues);
        foreach ($filesInCurrentValues as $key) {
            if (strpos($key, 'data:') === 0) {
                $dataKay = substr($key, 5);
                if ($hasUser && strpos($dataKay, 'bearcms/files/themeimage/') === 0) {
                    // Do not delete theme files when changes to the user values are made
                } else {
                    $dataKeysToDelete[] = $dataKay;
                }
            }
        }

        $dataKey = $hasUser ? '.temp/bearcms/userthemeoptions/' . md5($userID) . '/' . md5($id) . '.json' : 'bearcms/themes/theme/' . md5($id) . '.json';
        if ($values === null) {
            $app->data->delete($dataKey);
        } else {
            $filesInNewValues = Internal\Themes::getFilesInValues($values);
            foreach ($filesInNewValues as $key) {
                if (strpos($key, 'data:') === 0) {
                    $dataKay = substr($key, 5);
                    $dataKeysToDelete = array_diff($dataKeysToDelete, [$dataKay]); // Keeps the file if it's in the new values
                }
            }

            $dataToSet = [];
            $dataToSet['id'] = $id;
            if ($hasUser) {
                $dataToSet['userID'] = $userID;
            }
            $dataToSet['options'] = $values;
            if (!$hasUser && empty($values)) { // use default theme values
                $app->data->delete($dataKey);
            } else {
                $app->data->setValue($dataKey, json_encode($dataToSet));
            }
        }

        $recycleBinPrefix = '.recyclebin/bearcms/theme-changes-' . str_replace('.', '-', microtime(true)) . '/';
        foreach ($dataKeysToDelete as $dataKeyToDelete) {
            if ($app->data->exists($dataKeyToDelete)) {
                $app->data->rename($dataKeyToDelete, $recycleBinPrefix . $dataKeyToDelete);
            }
        }

        Internal\Themes::clearCustomizationsCache($id, $hasUser ? $userID : null);
    }

    /**
     * 
     * @param string $id The theme ID
     * @param string $values The values
     */
    public function setOptions(string $id, array $values): void
    {
        $this->setOptionsValues($id, $values);
    }

    /**
     * 
     * @param string $id The theme ID
     * @param string $userID The user ID
     * @param string $values The values
     */
    public function setUserOptions(string $id, string $userID, array $values): void
    {
        $this->setOptionsValues($id, $values, $userID);
    }

    /**
     * 
     * @param string $id The theme ID
     * @param string $userID The user ID
     */
    public function discardUserOptions(string $id, string $userID): void
    {
        $this->setOptionsValues($id, null, $userID);
    }

    /**
     * 
     * @param string $id The theme ID
     */
    public function discardOptions(string $id): void
    {
        $this->setOptionsValues($id, null);
    }
}
