<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal;

return function($data) {
    $app = App::get();
    if (!isset($data['id'])) {
        throw new Exception('');
    }
    $elementID = $data['id'];
    $rawDataList = Internal\ElementsHelper::getElementsRawData([$elementID]);
    if ($rawDataList[$elementID] !== null) {
        $elementData = json_decode($rawDataList[$elementID], true);
        $app->data->delete('bearcms/elements/element/' . md5($elementID) . '.json');
        if (isset($elementData['type'])) {
            $componentName = array_search($elementData['type'], Internal\ElementsHelper::$elementsTypesCodes);
            $options = Internal\ElementsHelper::$elementsTypesOptions[$componentName];
            if (isset($options['onDelete']) && is_callable($options['onDelete'])) {
                call_user_func($options['onDelete'], isset($elementData['data']) ? $elementData['data'] : []);
            }
        }
    }
};
