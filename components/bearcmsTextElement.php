<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

$outputType = (string) $component->getAttribute('output-type');
$outputType = isset($outputType[0]) ? $outputType : 'full-html';
$isFullHtmlOutputType = $outputType === 'full-html';

$attributes = $isFullHtmlOutputType ? ' class="bearcms-text-element"' : '';

$text = $component->text;

$linksHTML = '';

$search = [];
$replace = [];
$matches = null;
preg_match_all('/<font color="(.*?)">(.*?)<\/font>/', $text, $matches);
foreach ($matches[0] as $i => $match) {
    $search[] = $match;
    $replace[] = '<span style="color:' . $matches[1][$i] . '">' . $matches[2][$i] . '</span>';
}
$matches = null;
preg_match_all('/<strike>(.*?)<\/strike>/', $text, $matches);
foreach ($matches[0] as $i => $match) {
    $search[] = $match;
    $replace[] = '<span style="text-decoration:line-through">' . $matches[1][$i] . '</span>';
}
$matches = null;
preg_match_all('/<u>(.*?)<\/u>/', $text, $matches);
foreach ($matches[0] as $i => $match) {
    $search[] = $match;
    $replace[] = '<span style="text-decoration:underline">' . $matches[1][$i] . '</span>';
}
$matches = null;
preg_match_all('/<div align="(.*?)">(.*?)<\/div>/', $text, $matches);
foreach ($matches[0] as $i => $match) {
    $search[] = $match;
    $replace[] = '<div style="text-align:' . $matches[1][$i] . '">' . $matches[2][$i] . '</div>';
}
if (!empty($search)) {
    $text = str_replace($search, $replace, $text);
}

$search = [];
$replace = [];
$matches = null;
preg_match_all('/href=\"(.*?)\"/', $text, $matches);
foreach ($matches[0] as $i => $match) {
    $search[] = $match;
    list($linkURL, $linkOnClick, $linkHTML) = \BearCMS\Internal\Links::updateURL($matches[1][$i]);
    if ($linkURL !== null) {
        $replace[] = 'href="' . $linkURL . '"'; // The URL is already encoded, so now htmlentities() is required
    } else if ($linkOnClick !== null) {
        $replace[] = 'href="javascript:void(0);" onclick="' . htmlentities($linkOnClick) . '"';
    }
    $linksHTML .= (string)$linkHTML;
}
if (!empty($search)) {
    $text = str_replace($search, $replace, $text);
}

$content = '<div' . $attributes . '>' . $text . $linksHTML . '</div>';

echo '<html>';
if ($isFullHtmlOutputType) {
    echo '<head><style>';
    echo '.bearcms-text-element{display:block;word-break:break-word;}'; // no clear:both - breaks floating box
    echo '.bearcms-text-element:after{visibility:hidden;display:block;font-size:0;content:" ";clear:both;height:0;}';
    echo '</style></head>';
}
echo '<body>';
echo $content;
echo '</body></html>';
