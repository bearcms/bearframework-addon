<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearCMS\Internal\ImportExport\ImportContext;
use BearCMS\Internal\ImportExport\ItemHandler;
use BearFramework\App;

/**
 * @internal
 * @codeCoverageIgnore
 */
class ImportExport
{

    /**
     * Used by addons
     */
    static $handlers = [];

    /**
     * 
     * @var boolean
     */
    static private $defaultHandlersInitialized = false;

    /**
     * 
     * @return void
     */
    static private function initializeDefaultHandlers(): void
    {
        if (!self::$defaultHandlersInitialized) {
            self::$handlers['elementsContainer'] = function () {
                return self::makeHandler(
                    function (array $args, callable $add) {
                        ElementsDataHelper::exportContainer($args['containerID'], $add);
                    },
                    function (array $args, ImportContext $context, $options) {
                        return ElementsDataHelper::importContainer($args['containerID'], $context, $options);
                    }
                );
            };
            self::$handlers['element'] = function () {
                return self::makeHandler(
                    function (array $args, callable $add): void {
                        ElementsDataHelper::exportElement($args['elementID'], isset($args['containerID']) ? $args['containerID'] : null, $add);
                    },
                    function (array $args, ImportContext $context, $options) {
                        return ElementsDataHelper::importElement($args['elementID'], isset($args['containerID']) ? $args['containerID'] : null, $context, $options);
                    }
                );
            };
            self::$handlers['data'] = function () {
                return self::makeHandler(
                    function (array $args, callable $add): void {
                        $keys = [];
                        $skipPrefixes = isset($args['skipPrefixes']) ? $args['skipPrefixes'] : [];
                        $skipKeys = isset($args['skipKeys']) ? $args['skipKeys'] : [];
                        $app = App::get();
                        $appData = $app->data;
                        $list = $appData->getList()
                            ->sliceProperties(['key']);
                        foreach ($skipPrefixes as $skipPrefix) {
                            $list->filterBy('key', $skipPrefix, 'notStartWith');
                        }
                        foreach ($list as $item) {
                            $dataKey = $item->key;
                            if (array_search($dataKey, $skipKeys) !== false) {
                                continue;
                            }
                            $dataItem = $app->data->get($dataKey);
                            $add('values/' . $dataKey, $dataItem->value);
                            $dataItemMetadata = $dataItem->metadata->toArray();
                            if (!empty($dataItemMetadata)) {
                                $add('metadata/' . $dataKey, json_encode($dataItemMetadata));
                            }
                            $keys[] = $dataKey;
                        }
                        $add('keys.json', json_encode($keys));
                    },
                    function (array $args, ImportContext $context, $options) {
                        $app = App::get();
                        $appData = $app->data;
                        $keys = json_decode($context->getValue('keys.json'), true);
                        foreach ($keys as $key) {
                            $value = $context->getValue('values/' . $key);
                            $metadata = $context->getValue('metadata/' . $key);
                            $dataItemExists = $appData->exists($key);
                            if ($dataItemExists) {
                                $context->logWarning('The data key ' . $key . ' already exists!', ['dataKey' => $key]);
                            } else {
                                $context->logChange('setDataItem', ['dataKey' => $key]);
                            }
                            if (!$dataItemExists && $context->isExecuteMode()) {
                                $dataItem = $appData->make($key, $value);
                                if ($metadata !== null) {
                                    $metadata = json_decode($metadata, true);
                                    foreach ($metadata as $metadataName => $metadataValue) {
                                        $dataItem->metadata[$metadataName] = $metadataValue;
                                    }
                                }
                                $appData->set($dataItem);
                            }
                        }
                    }
                );
            };
            self::$defaultHandlersInitialized = true;
        }
    }

    /**
     * 
     * @param string $type
     * @return ItemHandler
     */
    static private function getHandler(string $type): ItemHandler
    {
        self::initializeDefaultHandlers();
        if (!isset(self::$handlers[$type])) {
            throw new \Exception('Not found type for ' . $type . '!');
        }
        if (!is_a(self::$handlers[$type], ItemHandler::class)) {
            self::$handlers[$type] = call_user_func(self::$handlers[$type]);
        }
        return self::$handlers[$type];
    }

    /**
     * 
     * @param callable $export
     * @param callable $import
     * @return ItemHandler
     */
    static function makeHandler(callable $export, callable $import): ItemHandler
    {
        return new ItemHandler($export, $import);
    }

    /**
     * 
     * @param array $items [[type=>element, args=>[elementID=>key1, containerID=>key2], exportArgs=>false], [type=>activeTheme], [type=>data], [type=>elementsContainer, args=>[containerID=>key1]], ...] Available types: elementsContainer, element, theme, activeTheme
     * @param array $options Available values: memoryLimit
     * @return string
     */
    static function export(array $items, array $options = []): string
    {
        $app = App::get();
        $items = array_values($items);
        $archiveFilename = $app->data->getFilename('.temp/bearcms/export/export-' . date('Ymd-His') . '.zip');
        $tempArchiveFilename = sys_get_temp_dir() . '/bearcms-export-' . uniqid() . '.zip';

        $getConfigMemoryLimit = function (): int {
            $limit = trim(ini_get('memory_limit'));
            $letter = strtolower(substr($limit, -1));
            $number = substr($limit, 0, -1);
            if ($letter === 'g') {
                return (int) $number * 1024 * 1024 * 1024;
            } elseif ($letter === 'm') {
                return (int) $number * 1024 * 1024;
            } elseif ($letter === 'k') {
                return (int) $number * 1024;
            }
            return (int) $limit;
        };

        $startMemoryUsage = memory_get_usage();
        $memoryLimit = isset($options['memoryLimit']) ? (int) $options['memoryLimit'] : ($getConfigMemoryLimit() - $startMemoryUsage) / 2;

        $zip = null;
        $openZip = function () use (&$zip, $tempArchiveFilename) {
            if ($zip === null) {
                $zip = new \ZipArchive();
                if (!$zip->open($tempArchiveFilename, \ZipArchive::CREATE)) {
                    throw new \Exception('Cannot open zip file for writing (' . $tempArchiveFilename . ')!');
                }
            }
        };
        $closeZip = function () use (&$zip) {
            if ($zip !== null) {
                $zip->close();
                $zip = null;
            }
        };

        $manifest = [
            'type' => 'items',
            'items' => [],
            'date' => date('c'),
            'files' => []
        ];
        foreach ($items as $index => $item) {
            if (!isset($item['type'])) {
                throw new \Exception('Not found type for ' . print_r($item, true) . '!');
            }
            $itemType = $item['type'];
            $itemArgs = isset($item['args']) ? $item['args'] : [];
            $exportArgs = !(isset($item['exportArgs']) && $item['exportArgs'] === false);
            $handler = self::getHandler($itemType);
            $files = [];
            $handler->export($itemArgs, function (string $key, string $content) use ($index, &$files, &$zip, $openZip, $closeZip, $startMemoryUsage, $memoryLimit) {
                $openZip();
                $zip->addFromString('items/' . $index . '/' . md5($key), $content);
                $files[$key] = 1; // may have duplicates (shared styles for example)
                if (memory_get_usage() - $startMemoryUsage > $memoryLimit) {
                    $closeZip();
                }
            });
            ksort($files);
            $manifest['items'][$index] = ['type' => $itemType];
            if ($exportArgs) {
                $manifest['items'][$index]['args'] = $itemArgs;
            }
            $manifest['files'][$index] = array_keys($files);
        }
        $openZip();
        $zip->addFromString('manifest.json', json_encode($manifest, JSON_THROW_ON_ERROR));
        $closeZip();

        copy($tempArchiveFilename, $archiveFilename);
        unlink($tempArchiveFilename);
        return $archiveFilename;
    }

    /**
     * 
     * @param string $filename
     * @param boolean $preview
     * @param callable|null $updateManifestCallback
     * @return array
     */
    static function import(string $filename, bool $preview, callable $updateManifestCallback = null): array
    {
        $result = ['results' => [], 'changes' => []];
        if (!is_file($filename)) {
            throw new \Exception('Import file not found!', 2);
        }
        $tempArchiveFilename = sys_get_temp_dir() . '/bearcms-import-' . uniqid() . '.zip';
        copy($filename, $tempArchiveFilename);
        $filename = null; // safe to use below
        $zip = new \ZipArchive();
        if ($zip->open($tempArchiveFilename) === true) {
            try {
                $getManifest = function () use ($zip) {
                    $data = $zip->getFromName('manifest.json');
                    if (strlen($data) > 0) {
                        $data = json_decode($data, true);
                        if (is_array($data) && isset($data['items']) && is_array($data['items'])) {
                            return $data;
                        }
                    }
                    throw new \Exception('The manifest file is not valid!', 3);
                };
                $context = new ImportContext($preview ? 'preview' : 'execute');
                $manifest = $getManifest();
                if ($updateManifestCallback !== null) {
                    $manifest = call_user_func($updateManifestCallback, $manifest);
                }
                foreach ($manifest['items'] as $index => $item) {
                    if (!isset($item['type'])) {
                        throw new \Exception('Not found type for ' . print_r($item, true) . '!');
                    }
                    $itemContext = $context->makeGetValueContext(function (string $key) use ($index, $zip) {
                        $data = $zip->getFromName('items/' . $index . '/' . md5($key));
                        if ($data !== false) {
                            return $data;
                        }
                        return null;
                    });
                    if (isset($item['args'])) {
                        $itemArgs = $item['args'];
                    } else { // old format
                        $itemArgs = $item;
                        unset($itemArgs['type']);
                    }
                    $handler = self::getHandler($item['type']);
                    $importResult = $handler->import($itemArgs, $itemContext, isset($item['importOptions']) && is_array($item['importOptions']) ? $item['importOptions'] : []);
                    $result['results'][$index] = [
                        'item' => $item,
                        'result' => $importResult
                    ];
                }
                $zip->close();
                unlink($tempArchiveFilename);
                $result['changes'] = $context->getChanges();
                if (isset($result['changes']['_warnings'])) {
                    $result['warnings'] = $result['changes']['_warnings'];
                    unset($result['changes']['_warnings']);
                } else {
                    $result['warnings'] = [];
                }
            } catch (\Exception $e) {
                $zip->close();
                unlink($tempArchiveFilename);
                throw $e;
            }
        } else {
            unlink($tempArchiveFilename);
            throw new \Exception('Cannot open zip archive (' . $tempArchiveFilename . ')', 8);
        }
        return $result;
    }
}
