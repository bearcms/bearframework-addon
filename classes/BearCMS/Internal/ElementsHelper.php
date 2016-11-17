<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;

final class ElementsHelper
{

    static $editorData = [];
    static $elementTypes = [
        'bearcms-heading-element' => 'heading',
        'bearcms-text-element' => 'text',
        'bearcms-link-element' => 'link',
        'bearcms-video-element' => 'video',
        'bearcms-image-element' => 'image',
        'bearcms-image-gallery-element' => 'imageGallery',
        'bearcms-navigation-element' => 'navigation',
        'bearcms-html-element' => 'html',
        'bearcms-blog-posts-element' => 'blogPosts'
    ];

    /**
     * 
     * @param type $component
     */
    static function updateComponentEditableAttribute($component)
    {
        $app = App::$instance;
        $editable = false;
        if ($component->editable === 'true' && strlen($component->id) > 0) {
            if ($app->bearCMS->currentUser->exists() && $app->bearCMS->currentUser->hasPermission('modifyContent')) {
                $editable = true;
            }
        }
        $component->editable = $editable ? 'true' : 'false';
    }

    /**
     * 
     * @param type $component
     */
    static function updateComponentContextAttributes($component)
    {
        $getUpdatedHTMLUnit = function($value) {
            if (strlen($value) > 0) {
                if (is_numeric($value)) {
                    $value .= 'px';
                }
                if (preg_match('/^(([0-9]+)|(([0-9]*)\.([0-9]+)))(px|rem|em|%|in|cm)$/', $value) !== 1) {
                    $value = '';
                }
            }
            return (string) $value;
        };

        // Update width
        $component->width = $getUpdatedHTMLUnit($component->width);
        if ((string) $component->width === '') {
            $component->width = '100%';
        }

        // Update spacing
        $component->spacing = $getUpdatedHTMLUnit($component->spacing);
        if ((string) $component->spacing === '') {
            $component->spacing = '1rem';
        }

        // Update color
        if (strlen($component->color) > 0) {
            if (preg_match('/^#[0-9a-fA-F]{6}$/', $component->color) !== 1) {
                $component->color = '';
            }
        }
        if ((string) $component->color === '') {
            $component->color = Options::$uiColor;
        }

        // Update canEdit
//        if (strlen($component->canEdit) > 0) {
//            if ($component->canEdit !== 'true') {
//                $component->canEdit = '';
//            }
//        }
//        if ((string) $component->canEdit === '') {
//            $component->canEdit = 'true';
//        }
        // Update canMove
//        if (strlen($component->canMove) > 0) {
//            if ($component->canMove !== 'true') {
//                $component->canMove = '';
//            }
//        }
//        if ((string) $component->canMove === '') {
//            $component->canMove = 'true';
//        }
        // Update canDelete
//        if (strlen($component->canDelete) > 0) {
//            if ($component->canDelete !== 'true') {
//                $component->canDelete = '';
//            }
//        }
//        if ((string) $component->canDelete === '') {
//            $component->canDelete = 'true';
//        }
    }

    /**
     * 
     * @param type $component
     * @return type
     */
    static function getComponentContextData($component)
    {
        $result = [];
        $result['width'] = $component->width;
        $result['spacing'] = $component->spacing;
        $result['color'] = $component->color;
//        $result['canEdit'] = $component->canEdit === 'true';
//        $result['canMove'] = $component->canMove === 'true';
//        $result['canDelete'] = $component->canDelete === 'true';
        if ($component->getAttribute('bearcms-internal-attribute-not-found-in-data') === 'true') {
            $result['rawData'] = $component->getAttribute('bearcms-internal-attribute-raw-data');
        }
        return $result;
    }

    /**
     * 
     * @param type $component
     * @throws \Exception
     */
    static function updateContainerComponent($component)
    {
        if (strlen($component->id) === 0) {
            throw new \Exception('');
        }
        self::updateComponentEditableAttribute($component);
        self::updateComponentContextAttributes($component);
        if (strlen($component->group) === 0) {
            $component->group = 'default';
        }
    }

    /**
     * 
     * @param type $component
     * @throws \Exception
     */
    static function updateElementComponent($component)
    {
        self::updateComponentEditableAttribute($component);
        self::updateComponentContextAttributes($component);

        $rawData = $component->getAttribute('bearcms-internal-attribute-raw-data');
        $elementData = null;
        if (strlen($rawData) > 0) {
            $elementData = self::decodeElementRawData($rawData);
            $component->id = $elementData['id'];
        } elseif (strlen($component->id) > 0) {
            $elementsRawData = self::getElementsRawData([$component->id]);
            if (isset($elementsRawData[$component->id])) {
                $elementData = self::decodeElementRawData($elementsRawData[$component->id]);
            }
        }
        if ($elementData !== null) {
            self::updateComponentFromRawData($component, $elementData);
        } else {
            if (strlen($component->id) > 0 && $component->editable === 'true') {
                $rawData = self::getRawDataFromComponent($component);
                $component->setAttribute('bearcms-internal-attribute-raw-data', json_encode($rawData));
                $component->setAttribute('bearcms-internal-attribute-not-found-in-data', 'true');
            }
        }
    }

    static function updateComponentFromRawData(&$component, $rawData)
    {
        $type = $rawData['type'];
        $data = $rawData['data'];

        $copyString = function($name) use (&$component, $data) {
            $component->$name = isset($data[$name]) ? (string) $data[$name] : '';
        };

        $copyBoolean = function($name) use (&$component, $data) {
            $component->$name = isset($data[$name]) ? ($data[$name] ? 'true' : 'false') : '';
        };

        $copyInt = function($name) use (&$component, $data) {
            $component->$name = isset($data[$name]) ? (string) $data[$name] : '';
        };

        if ($type === 'heading') {
            $copyString('text');
            $copyString('size');
        } elseif ($type === 'text') {
            $copyString('text');
        } elseif ($type === 'link') {
            $copyString('url');
            $copyString('text');
            $copyString('title');
        } elseif ($type === 'video') {
            $copyString('url');
            $copyString('filename');
        } elseif ($type === 'image') {
            $copyString('filename');
            $copyString('title');
            $copyString('onClick');
            $copyString('url');
        } elseif ($type === 'imageGallery') {
            $copyString('type');
            $copyString('columnsCount');
            $copyString('imageSize');
            $copyString('imageAspectRatio');
            if (isset($data['files'])) {
                foreach ($data['files'] as $file) {
                    if (is_array($file) && isset($file['filename'])) {
                        $component->innerHTML .= '<file filename="' . $file['filename'] . '" />';
                    }
                }
            }
        } elseif ($type === 'navigation') {
            $copyString('type');
            $copyString('pageID');
        } elseif ($type === 'html') {
            $copyString('code');
        } elseif ($type === 'blogPosts') {
            $copyString('type');
            $copyBoolean('showDate');
            $copyInt('limit');
        }
    }

    static function getRawDataFromComponent($component)
    {
        $type = self::$elementTypes[$component->src];
        $data = [];

        $copyString = function($name) use ($component, &$data) {
            $data[$name] = (string) $component->$name;
        };

        $copyBoolean = function($name) use ($component, &$data) {
            $data[$name] = $component->$name === 'true' ? true : false;
        };

        $copyInt = function($name) use ($component, &$data) {
            $data[$name] = (int) $component->$name;
        };

        if ($type === 'heading') {
            $copyString('text');
            $copyString('size');
        } elseif ($type === 'text') {
            $copyString('text');
        } elseif ($type === 'link') {
            $copyString('url');
            $copyString('text');
            $copyString('title');
        } elseif ($type === 'video') {
            $copyString('url');
            $copyString('filename');
        } elseif ($type === 'image') {
            $copyString('filename');
            $copyString('title');
            $copyString('onClick');
            $copyString('url');
        } elseif ($type === 'imageGallery') {
            $copyString('type');
            $copyString('columnsCount');
            if (is_numeric($data['columnsCount'])) {
                $data['columnsCount'] = (int) $data['columnsCount'];
            }
            $copyString('imageSize');
            $copyString('imageAspectRatio');
            $data['files'] = [];
            if (strlen($component->innerHTML) > 0) {
                $domDocument = new \IvoPetkov\HTML5DOMDocument();
                $domDocument->loadHTML($component->innerHTML);
                $files = $domDocument->querySelectorAll('file');
                foreach ($files as $file) {
                    $filename = $file->getAttribute('filename');
                    $data['files'][] = ['filename' => $filename];
                }
            }
        } elseif ($type === 'navigation') {
            $copyString('type');
            $copyString('pageID');
        } elseif ($type === 'html') {
            $copyString('code');
        } elseif ($type === 'blogPosts') {
            $copyString('type');
            $copyBoolean('showDate');
            $copyInt('limit');
        }
        return ['id' => $component->id, 'type' => $type, 'data' => $data];
    }

    /**
     * 
     * @param type $component
     * @param type $content
     * @return type
     */
    static function getElementComponentContent($component, $type, $content)
    {
        if ($component->getAttribute('bearcms-internal-attribute-container') === 'none') {
            return $content;
        }
        $attributes = '';
        if ($component->editable === 'true') {
            $htmlElementID = 'brelc' . md5($component->id);
            ElementsHelper::$editorData[] = ['element', $component->id, self::getComponentContextData($component), $type];
            $attributes .= ' id="' . $htmlElementID . '"';
        }
        return '<div' . $attributes . '>' . $content . '</div>';
    }

    /**
     * 
     * @param type $data
     * @return type
     * @throws \Exception
     */
    static function decodeElementRawData($data)
    {
        $data = json_decode($data, true);
        if (!is_array($data)) {
            throw new \Exception('');
        }
        if (!isset($data['id']) || !is_string($data['id'])) {
            throw new \Exception('');
        }
        if (!isset($data['type']) || !is_string($data['type'])) {
            throw new \Exception('');
        }
        if (!isset($data['data']) || !is_array($data['data'])) {
            throw new \Exception('');
        }
        return $data;
    }

    /**
     * 
     * @param type $rawData
     * @param type $editable
     * @param type $contextData
     * @return type
     * @throws \Exception
     */
    static function renderElement($rawData, $editable, $contextData)
    {
        $elementData = self::decodeElementRawData($rawData);
        if (!isset($elementData['id']) || strlen($elementData['id']) === 0) {
            throw new \Exception('');
        }
        if (!isset($elementData['type']) || strlen($elementData['type']) === 0) {
            throw new \Exception('');
        }
        $componentName = array_search($elementData['type'], self::$elementTypes);
        if ($componentName === false) {
            throw new \Exception('');
        }
        return '<component src="' . $componentName . '" editable="' . ($editable ? 'true' : 'false') . '" bearcms-internal-attribute-raw-data="' . htmlentities($rawData) . '" width="' . $contextData['width'] . '" spacing="' . $contextData['spacing'] . '" color="' . $contextData['color'] . '"/>'; // canEdit="' . ($contextData['canEdit'] ? 'true' : 'false') . '" canMove="' . ($contextData['canMove'] ? 'true' : 'false') . '" canDelete="' . ($contextData['canDelete'] ? 'true' : 'false') . '"
    }

    /**
     * 
     * @param type $elementContainerData
     * @param type $editable
     * @param type $contextData
     * @param type $inContainer
     * @return type
     */
    static function renderColumn($elementContainerData, $editable, $contextData, $inContainer)
    {
        $app = App::$instance;
        $context = $app->getContext(__DIR__);
        $columnsSizes = explode(':', $elementContainerData['data']['mode']);
        $columnsCount = sizeof($columnsSizes);
        $totalSize = array_sum($columnsSizes);
        $spacing = $contextData['spacing'];

        $content = '';
        for ($i = 0; $i < $columnsCount; $i++) {

            $columnContent = '';
            if (isset($elementContainerData['data']['elements'], $elementContainerData['data']['elements'][$i])) {
                $elementsInColumn = $elementContainerData['data']['elements'][$i];
                if (!empty($elementsInColumn)) {
                    $elementsInColumnContextData = $contextData;
                    $elementsInColumnContextData['width'] = '100%';
                    $itemsIDs = [];
                    foreach ($elementsInColumn as $elementInColumnContainerData) {
                        $itemsIDs[] = $elementInColumnContainerData['id'];
                    }
                    $elementsInColumnRawData = self::getElementsRawData($itemsIDs);
                    foreach ($elementsInColumn as $elementInColumnContainerData) {
                        $columnContent .= self::renderElement($elementsInColumnRawData[$elementInColumnContainerData['id']], $editable, $elementsInColumnContextData);
                    }
                }
            }

            $columnWidth = rtrim(rtrim(number_format($columnsSizes[$i] / $totalSize * 100, 3, '.', ''), 0), '.') . '%';
            $columnStyle = 'width:calc(' . $columnWidth . ' - (' . $spacing . '*' . ($columnsCount - 1) . '/' . $columnsCount . '));';
            if ($columnsCount > $i + 1) {
                $columnStyle .= 'margin-right:' . $spacing . ';';
            }
            $content .= '<div style="' . $columnStyle . '">' . $columnContent . '</div>';
        }

        if ($inContainer) {
            $attributes = '';
            $className = 'bre' . md5(uniqid());
            $attributes .= ' class="' . $className . '"';

            if ($editable) {
                $htmlElementID = 'brelb' . md5($elementContainerData['id']);
                $attributes .= ' id="' . $htmlElementID . '"';
                ElementsHelper::$editorData[] = ['column', $elementContainerData['id'], $contextData];
            }

            $attributes .= ' data-srvri="t2 s' . $spacing . '" data-responsive-attributes="w<=500=>data-srvri-vertical=1"';

            $styles = '';
            $styles .= '.' . $className . '[data-srvri-vertical="1"]>div{display:block !important;width:100% !important;margin-right:0 !important;}';
            $styles .= '.' . $className . '[data-srvri-vertical="1"]>div:not(:empty):not(:last-child){margin-bottom:' . $spacing . ' !important;}';
            $styles .= '.' . $className . '[data-rvr-editable][data-srvri-vertical="1"]>div:not(:last-child){margin-bottom:' . $spacing . ' !important;}';

            $content = '<html>'
                    . '<head>'
                    . '<script src="' . htmlentities($context->assets->getUrl('assets/responsiveAttributes.min.js')) . '"></script>'
                    . '<style>' . $styles . '</style>'
                    . '</head>'
                    . '<body>'
                    . '<div' . $attributes . '>' . $content . '</div>'
                    //. '<script>responsiveAttributes.run();</script>'
                    . '</body>'
                    . '</html>';
            return '<component src="data:base64,' . base64_encode($content) . '" />';
        } else {
            return $content;
        }
    }

    /**
     * 
     * @param type $itemsIDs
     * @return type
     */
    static function getElementsRawData($itemsIDs)
    {
        $app = App::$instance;
        $result = [];
        $commands = [];
        $itemsIDs = array_values($itemsIDs);
        foreach ($itemsIDs as $itemID) {
            $commands[] = [
                'command' => 'get',
                'key' => 'bearcms/elements/element/' . md5($itemID) . '.json',
                'result' => ['body']
            ];
        }
        $data = $app->data->execute($commands);
        foreach ($itemsIDs as $index => $itemID) {
            if (isset($data[$index]['body'])) {
                $result[$itemID] = $data[$index]['body'];
            }
        }
        return $result;
    }

    static function getContainerData($id)
    {
        $app = App::$instance;
        $container = $app->data->get(
                [
                    'key' => 'bearcms/elements/container/' . md5($id) . '.json',
                    'result' => ['body']
                ]
        );
        $data = isset($container['body']) ? json_decode($container['body'], true) : [];
        if (!isset($data['elements'])) {
            $data['elements'] = [];
        }
        if (!is_array($data['elements'])) {
            throw new Exception('');
        }
        return $data;
    }

}
