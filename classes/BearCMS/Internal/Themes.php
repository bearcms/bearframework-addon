<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;

class Themes
{

    static $list = [];
    static $elementsOptions = [];

    /**
     * 
     * @param string $id
     * @param array|callable $options
     */
    static function add(string $id, $options = [])
    {
        self::$list[$id] = $options;
    }

    static function getActiveThemeID(): string
    {
        $app = App::get();
        $data = \BearCMS\Internal\Data::getValue('bearcms/themes/active.json');
        if ($data !== null) {
            $data = json_decode($data, true);
            if (isset($data['id'])) {
                return $data['id'];
            }
        }
        if (strlen(\BearCMS\Internal\Options::$defaultThemeID) > 0) {
            return \BearCMS\Internal\Options::$defaultThemeID;
        }
        return 'none';
    }

    static function getList(): array
    {
        return array_keys(self::$list);
    }

    static function prepareOptions($id)
    {
        if (isset(self::$list[$id]) && is_callable(self::$list[$id])) {
            self::$list[$id] = call_user_func(self::$list[$id]);
        }
    }

    static function getVersion(string $id): ?string
    {
        if (isset(self::$list[$id])) {
            self::prepareOptions($id);
            $options = self::$list[$id];
            $version = null;
            if (isset($options['version'])) {
                $version = $options['version'];
            }
            return $version;
        }
        return null;
    }

    static function getManifest(string $id, $updateMediaFilenames = true): array
    {
        if (isset(self::$list[$id])) {
            $app = App::get();
            self::prepareOptions($id);
            $options = self::$list[$id];
            if (isset($options['manifest'])) {
                $context = $app->context->get(__FILE__);
                if (is_array($options['manifest'])) {
                    $result = $options['manifest'];
                } elseif (is_callable($options['manifest'])) {
                    $result = call_user_func($options['manifest']);
                } else {
                    $result = null;
                }
                if (!is_array($result)) {
                    throw new \Exception('Invalid theme manifest value for theme ' . $id . '!');
                }
                if ($updateMediaFilenames) {
                    if (isset($result['media']) && is_array($result['media'])) {
                        foreach ($result['media'] as $i => $mediaItem) {
                            if (is_array($mediaItem) && isset($mediaItem['filename']) && is_string($mediaItem['filename'])) {
                                $result['media'][$i]['filename'] = $context->dir . '/assets/tm/' . md5($id) . '/' . md5($mediaItem['filename']) . '.' . pathinfo($mediaItem['filename'], PATHINFO_EXTENSION);
                            }
                        }
                    }
                }
            } else {
                $result = [];
            }
            return $result;
        }
        return [];
    }

    static function getOptions(string $id): array
    {
        if (isset(self::$list[$id])) {
            self::prepareOptions($id);
            $options = self::$list[$id];
            if (isset($options['options'])) {
                if (is_array($options['options'])) {
                    $result = $options['options'];
                } elseif (is_callable($options['options'])) {
                    $result = call_user_func($options['options']);
                } else {
                    $result = null;
                }
                if ($result instanceof \BearCMS\Themes\OptionsDefinition) {
                    $result = $result->toArray();
                }
                if (!is_array($result)) {
                    throw new \Exception('Invalid theme options value for theme ' . $id . '!');
                }
            } else {
                $result = [];
            }
            return $result;
        }
        return [];
    }

    static function getStyles(string $id, $updateMediaFilenames = true): array
    {
        if (isset(self::$list[$id])) {
            $app = App::get();
            self::prepareOptions($id);
            $options = self::$list[$id];
            if (isset($options['styles'])) {
                $context = $app->context->get(__FILE__);
                if (is_array($options['styles'])) {
                    $result = $options['styles'];
                } elseif (is_callable($options['styles'])) {
                    $result = call_user_func($options['styles']);
                } else {
                    $result = null;
                }
                if (!is_array($result)) {
                    throw new \Exception('Invalid theme styles value for theme ' . $id . '!');
                }
                foreach ($result as $j => $style) {
                    if (isset($result[$j]['id'])) {
                        $result[$j]['id'] = (string) $result[$j]['id'];
                    } else {
                        $result[$j]['id'] = 'style' . $j;
                    }
                    if (isset($result[$j]['name'])) {
                        $result[$j]['name'] = (string) $result[$j]['name'];
                    } else {
                        $result[$j]['name'] = '';
                    }
                    if (isset($result[$j]['media'])) {
                        if (!is_array($result[$j]['media'])) {
                            throw new \Exception('');
                        }
                    } else {
                        $result[$j]['media'] = [];
                    }
                }
                if ($updateMediaFilenames) {
                    foreach ($result as $j => $style) {
                        if (isset($style['media']) && is_array($style['media'])) {
                            foreach ($style['media'] as $i => $mediaItem) {
                                if (is_array($mediaItem) && isset($mediaItem['filename']) && is_string($mediaItem['filename'])) {
                                    $result[$j]['media'][$i]['filename'] = $context->dir . '/assets/tm/' . md5($id) . '/' . md5($mediaItem['filename']) . '.' . pathinfo($mediaItem['filename'], PATHINFO_EXTENSION);
                                }
                            }
                        }
                    }
                }
            } else {
                $result = [];
            }
            return $result;
        }
        return [];
    }

    static function defineElementOption($definition)
    {
        self::$elementsOptions[] = $definition;
    }

    static public function getCacheItemKey(string $id, $userID = null)
    {
        $version = self::getVersion($id);
        if ($version === null) {
            return null;
        }
        return 'bearcms-theme-options-' . \BearCMS\Internal\Options::$dataCachePrefix . '-' . md5($id) . '-' . md5($version) . '-' . md5($userID) . '-2';
    }

    /**
     * 
     * @param type $values
     * @return array
     */
    static public function getFilesInValues($values): array
    {
        $result = [];
        foreach ($values as $value) {
            $matches = [];
            preg_match_all('/url\((.*?)\)/', $value, $matches);
            if (!empty($matches[1])) {
                $matches[1] = array_unique($matches[1]);
                foreach ($matches[1] as $key) {
                    $jsJsonEncoded = is_array(json_decode($value, true));
                    $result[] = $jsJsonEncoded ? json_decode('"' . $key . '"') : $key;
                }
            }
            if (strpos($value, 'data:') === 0 || strpos($value, 'app:') === 0 || strpos($value, 'addon:') === 0) {
                $result[] = $value;
            }
        }
        return array_values(array_unique($result));
    }

    /**
     * 
     * @param type $values
     * @param type $keysToUpdate
     * @return array
     */
    static public function updateFilesInValues($values, $keysToUpdate): array
    {
        if (!empty($keysToUpdate)) {
            $search = [];
            $replace = [];
            foreach ($keysToUpdate as $oldKey => $newKey) {
                $search[] = 'url(' . $oldKey . ')';
                $replace[] = 'url(' . $newKey . ')';
                $search[] = trim(json_encode('url(' . $oldKey . ')'), '"');
                $replace[] = trim(json_encode('url(' . $newKey . ')'), '"');
            }
            foreach ($values as $name => $value) {
                $values[$name] = str_replace($search, $replace, $values[$name]);
            }
            foreach ($values as $name => $value) {
                if (isset($keysToUpdate[$value])) {
                    $values[$name] = $keysToUpdate[$value];
                }
            }
        }
        return $values;
    }

}
