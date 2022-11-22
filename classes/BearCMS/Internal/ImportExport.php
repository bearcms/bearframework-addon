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
     * @param array $items [[type=element, id=key1], [type=activeTheme], [type=elementsContainer, id=>key1], ...] Available types: elementsContainer, element, theme, activeTheme
     * @return string
     */
    static function export(array $items): string
    {
        $app = App::get();
        $items = array_values($items);
        $archiveFilename = $app->data->getFilename('.temp/bearcms/export/export-' . date('Ymd-His') . '.zip');
        $tempArchiveFilename = sys_get_temp_dir() . '/bearcms-export-' . uniqid() . '.zip';
        $zip = new \ZipArchive();
        if ($zip->open($tempArchiveFilename, \ZipArchive::CREATE) === true) {
            $manifest = [
                'type' => 'items',
                'items' => $items,
                'exportDate' => date('c'),
                'files' => []
            ];
            foreach ($items as $index => $item) {
                if (!isset($item['type'])) {
                    throw new \Exception('Not found type for ' . print_r($item, true) . '!');
                }
                $itemArgs = $item;
                unset($itemArgs['type']);
                $handler = self::getHandler($item['type']);
                $keys = [];
                $handler->export($itemArgs, function (string $key, string $content) use ($index, &$keys, $zip) {
                    $zip->addFromString('items/' . $index . '/' . md5($key), $content);
                    $keys[] = $key;
                });
                $manifest['files'][$index] = $keys;
            }
            $zip->addFromString('manifest.json', json_encode($manifest, JSON_THROW_ON_ERROR));
            $zip->close();
        } else {
            throw new \Exception('Cannot open zip archive (' . $tempArchiveFilename . ')');
        }
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
                    $itemArgs = $item;
                    unset($itemArgs['type']);
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
