<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;

/**
 * @internal
 * @codeCoverageIgnore
 */
class DataExport
{

    static public $handlers = [];

    /**
     * 
     * @param string $id
     * @param callable $callable
     * @return void
     */
    static function addHandler(string $id, callable $callable)
    {
        self::$handlers[$id] = $callable;
    }

    /**
     * 
     * @param string $type
     * @param string $handlerID
     * @param array $options
     * @return array|null
     */
    static function getResult(string $type, string $handlerID, array $options): ?array
    {
        if (isset(self::$handlers[$handlerID])) {
            $result = call_user_func(self::$handlers[$handlerID], $options);
            if (is_array($result) && isset($result['filename'], $result['data'])) {
                $filename = $result['filename'];
                $data = $result['data'];
                $multiple = isset($result['multiple']) && (int)$result['multiple'] === 1 ? $result['multiple'] : false;
                if (!$multiple) {
                    $data = [$data];
                }
                $value = null;
                if ($type === 'html') {
                    $value = [];
                    foreach ($data as $item) {
                        $itemHTML = [];
                        $counter = 0;
                        foreach ($item as $itemName => $itemValue) {
                            $counter++;
                            $itemHTML[] = '<div style="font-weight:bold;' . ($counter === 1 ? '' : 'margin-top:15px;') . '">' . htmlspecialchars((string)$itemName) . '</div><div>' . nl2br(htmlspecialchars((string)$itemValue)) . '</div>';
                        }
                        $value[] = '<div>' . implode('', $itemHTML) . '</div>';
                    }
                    $value = '<html><head><meta charset="utf-8"></head><body style="font-family:Arial;font-size:14px;line-height:140%;">' . implode('<div style="page-break-after:always;"></div>', $value) . '</body></html>';
                } elseif ($type === "json") {
                    $columns = [];
                    foreach ($data as $item) {
                        foreach ($item as $name => $value) {
                            if (array_search($name, $columns) === false) {
                                $columns[] = $name;
                            }
                        }
                    }
                    $value = [];
                    $value[] = $columns;
                    foreach ($data as $item) {
                        $row = [];
                        foreach ($columns as $column) {
                            $row[] = isset($item[$column]) ? $item[$column] : null;
                        }
                        $value[] = $row;
                    }
                    $value = json_encode($value);
                }
                if ($value !== null) {
                    return [
                        'filename' => $filename . '-' . date('Ymd-His'),
                        'value' => $value
                    ];
                }
            }
        }
        return null;
    }
}
