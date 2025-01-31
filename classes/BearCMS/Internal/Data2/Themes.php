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
     * @param bool $updateValues Update values
     * @return array A list containing the theme options
     * @throws \InvalidArgumentException
     */
    public function getValues(string $id, bool $updateValues = true): array
    {
        $result = [];
        $data = Internal\Data::getValue('bearcms/themes/theme/' . md5($id) . '.json');
        if ($data !== null) {
            $data = json_decode($data, true);
            if (isset($data['options'])) {
                $result = $data['options'];
            }
        }
        if ($updateValues) {
            $result = $this->updateOptionsValues($id, $result);
        }
        return $result;
    }

    /**
     * Returns a list containing the theme options a specific user has made
     * 
     * @param string $id The id of the theme
     * @param string $userID The id of the user
     * @param bool $updateValues Update values
     * @return array A list containing the theme options
     * @throws \InvalidArgumentException
     */
    public function getUserOptions(string $id, string $userID, bool $updateValues = true): ?array
    {
        $result = null;
        $app = App::get();
        $data = $app->data->getValue('.temp/bearcms/userthemeoptions/' . md5($userID) . '/' . md5($id) . '.json');
        if ($data !== null) {
            $data = json_decode($data, true);
            if (isset($data['options'])) {
                $result = $data['options'];
            } else {
                $result = []; // the user wants the default values
            }
        }
        if ($updateValues) {
            $result = $this->updateOptionsValues($id, $result);
        }
        return $result;
    }

    /**
     * Call the theme updateValues() method to update the values if options are modified
     *
     * @param string $id
     * @param array|null $values
     * @return array|null
     */
    private function updateOptionsValues(string $id, ?array $values = null): ?array
    {
        $theme = Internal\Themes::get($id);
        if ($theme !== null && is_callable($theme->updateValues)) {
            $values = call_user_func($theme->updateValues, $values);
        }
        return $values;
    }

    /**
     * 
     * @param string $id The theme ID
     * @param array|null $values Option values
     * @param string $userID The user ID
     */
    private function setOptionsValues(string $id, $values, ?string $userID = null): void
    {
        if (!is_array($values) && $values !== null) {
            throw new \InvalidArgumentException('The values argument is not valid');
        }
        $app = App::get();
        $hasUser = $userID !== null && strlen($userID) > 0;
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
        foreach ($filesInCurrentValues as $filename) {
            $dataKey = Internal\Data::getFilenameDataKey($filename);
            if ($dataKey !== null && (strpos($dataKey, '.temp/bearcms/files/themeimage/') === 0 || strpos($dataKey, 'bearcms/files/themeimage/') === 0)) {
                if ($hasUser && strpos($dataKey, 'bearcms/files/themeimage/') === 0) {
                    // Do not delete theme files when changes to the user values are made
                } else {
                    $dataKeysToDelete[] = $dataKey;
                }
            }
        }

        $themeDataKey = $hasUser ? '.temp/bearcms/userthemeoptions/' . md5($userID) . '/' . md5($id) . '.json' : 'bearcms/themes/theme/' . md5($id) . '.json';
        if ($values === null) {
            $app->data->delete($themeDataKey);
        } else {
            $filesInNewValues = Internal\Themes::getFilesInValues($values);
            foreach ($filesInNewValues as $filename) {
                $dataKey = Internal\Data::getFilenameDataKey($filename);
                if ($dataKey !== null) {
                    $dataKeysToDelete = array_diff($dataKeysToDelete, [$dataKey]); // Keeps the file if it's in the new values
                }
            }

            $dataToSet = [];
            $dataToSet['id'] = $id;
            if ($hasUser) {
                $dataToSet['userID'] = $userID;
            }
            $dataToSet['options'] = $values;
            if (!$hasUser && empty($values)) { // use default theme values
                $app->data->delete($themeDataKey);
            } else {
                $app->data->setValue($themeDataKey, json_encode($dataToSet, JSON_THROW_ON_ERROR));
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
