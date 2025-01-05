<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearCMS\Internal;
use BearFramework\App;

$app = App::get();

$outputType = (string) $component->getAttribute('output-type');
$outputType = isset($outputType[0]) ? $outputType : 'full-html';
$isFullHtmlOutputType = $outputType === 'full-html';

$value = trim((string)$component->value);

$cssTypesOptionsDetails = [];
$cssTypesOptionsDetails['text'] = [
    "cssTypes" => ['cssText', 'cssTextShadow', 'cssBackground', 'cssPadding', 'cssBorder', 'cssRadius', 'cssShadow', 'cssPosition', 'cssSize', 'cssOpacity', 'cssRotation'],
    "cssOutput" => [
        ["rule", 'xxx', 'position:absolute;'],
        ["selector", 'xxx']
    ]
];
$cssTypesOptionsDetails['block'] = [
    "cssTypes" => ['cssBackground', 'cssBorder', 'cssRadius', 'cssShadow', 'cssPosition', 'cssSize', 'cssOpacity', 'cssRotation'],
    "cssOutput" => [
        ["rule", 'xxx', 'position:absolute;'],
        ["selector", 'xxx']
    ]
];
$cssTypesOptionsDetails['image'] = [
    "cssTypes" => ['cssBorder', 'cssRadius', 'cssShadow', 'cssPosition', 'cssSize', 'cssOpacity', 'cssRotation'],
    "cssOutput" => [
        ["rule", 'xxx', 'position:absolute;'],
        ["selector", 'xxx']
    ]
];
$cssTypesOptionsDetails['background'] = [
    "cssTypes" => ['cssBackground', 'cssBorder', 'cssRadius', 'cssShadow', 'cssSize'],
    "cssOutput" => [
        ["rule", 'xxx', 'position:relative;overflow:hidden;'],
        ["selector", 'xxx']
    ]
];

$cssCode = '';
$addStyleCSS = function (string $selector, string $type, array $style) use (&$cssCode, $cssTypesOptionsDetails): void {
    if (isset($cssTypesOptionsDetails[$type])) {
        $options = new \BearCMS\Themes\Theme\Options();
        $options->addOption("css", "css", '', $cssTypesOptionsDetails[$type]);
        $options->setValues(['css' => json_encode($style, JSON_THROW_ON_ERROR)]);
        $htmlData = Internal\Themes::getOptionsHTMLData($options->getList());
        $html = Internal\Themes::processOptionsHTMLData($htmlData);
        $html = str_replace(['<html><head><style>', '</style></head></html>'], '', $html);
        $html = str_replace('xxx', $selector, $html);
        $cssCode .= $html;
    }
};

$containerClassName = 'cnvs' . md5($value);

$elementsHTML = '';
$parsedValue = json_decode($value, true);
if (is_array($parsedValue)) {
    if (isset($parsedValue['elements']) && is_array($parsedValue['elements'])) {
        $parsedValue['elements'] = array_reverse($parsedValue['elements']);
        $childrenCounter = 0;
        foreach ($parsedValue['elements'] as $elementData) {
            if (is_array($elementData) && isset($elementData['type'], $elementData['style']) && is_string($elementData['type']) && is_array($elementData['style'])) {
                $childrenCounter++;
                $addStyleCSS('.' . $containerClassName . ' :nth-child(' . $childrenCounter . ')', $elementData['type'], $elementData['style']);
                $elementsHTML .= '<div>';
                if ($elementData['type'] === 'text' && isset($elementData['data'], $elementData['data']['text'])) {
                    $elementsHTML .= nl2br(htmlspecialchars($elementData['data']['text']));
                }
                $elementsHTML .= '</div>';
            }
        }
    }
    if (isset($parsedValue['background']) && is_array($parsedValue['background']) && isset($parsedValue['background']['style']) && is_array($parsedValue['background']['style'])) {
        $addStyleCSS('.' . $containerClassName, 'background', $parsedValue['background']['style']);
    }
}

$content = '<div' . ($isFullHtmlOutputType ? ' class="bearcms-canvas-element"' : '') . '>';
if ($isFullHtmlOutputType) {
    $content = '<div class="' . $containerClassName . '">';
    $content .= $elementsHTML;
    $content .= '</div>';
}
$content .= '</div>';

echo '<html>';
if (isset($cssCode[0])) {
    echo '<head><style>' . $cssCode . '</style></head>';
}
echo '<body>';
echo $content;
echo '</body></html>';
