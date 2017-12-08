<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;

class Themes
{

    static $list = [];

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
        return 'none';
    }

    static function getList(): array
    {
        $list = Options::$useEmptyTheme ? ['none'] : [];
        $list = array_merge($list, array_keys(self::$list));
        return $list;
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
            if (isset($options['version'])) {
                return $options['version'];
            }
        }
        return null;
    }

    static function getManifest(string $id, $updateMediaFilenames = true): array
    {
        if (isset(self::$list[$id])) {
            self::prepareOptions($id);
            $options = self::$list[$id];
            if (isset($options['manifest'])) {
                $app = App::get();
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
                return $result;
            }
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
                if (is_array($result)) {
                    return $result;
                }
                if ($result instanceof \BearCMS\Themes\OptionsDefinition) {
                    return $result->toArray();
                }
                throw new \Exception('Invalid theme options value for theme ' . $id . '!');
            }
        }
        return [];
    }

    static function getStyles(string $id): array
    {
        if (isset(self::$list[$id])) {
            self::prepareOptions($id);
            $options = self::$list[$id];
            if (isset($options['styles'])) {
                if (is_array($options['styles'])) {
                    $result = $options['styles'];
                } elseif (is_callable($options['styles'])) {
                    $result = call_user_func($options['styles']);
                } else {
                    $result = null;
                }
                if (is_array($result)) {
                    return $result;
                }
                throw new \Exception('Invalid theme styles value for theme ' . $id . '!');
            }
        }
        return [];
    }

}
