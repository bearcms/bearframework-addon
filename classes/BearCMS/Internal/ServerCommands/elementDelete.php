<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal\ElementsHelper;

return function($data) {
    $app = App::get();
    if (!isset($data['id'])) {
        throw new Exception('');
    }
    $elementID = $data['id'];
    $rawDataList = ElementsHelper::getElementsRawData([$elementID]);
    if (isset($rawDataList[$elementID])) {
        $elementData = json_decode($rawDataList[$elementID], true);
        $app->data->delete('bearcms/elements/element/' . md5($elementID) . '.json');
        if (isset($elementData['type'])) {
            $componentName = array_search($elementData['type'], ElementsHelper::$elementsTypesCodes);
            $options = ElementsHelper::$elementsTypesOptions[$componentName];
            if (isset($options['onDelete']) && is_callable($options['onDelete'])) {
                call_user_func($options['onDelete'], isset($elementData['data']) ? $elementData['data'] : []);
            }
        }
    }
};
