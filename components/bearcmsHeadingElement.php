<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

$text = (string) $component->text;
$size = 'large';
if (strlen($component->size) > 0) {
    if (array_search($component->size, ['large', 'medium', 'small']) !== false) {
        $size = $component->size;
    }
}

if ($size === 'large') {
    $tagName = 'h1';
    $className = 'bearcms-heading-element-large';
} elseif ($size === 'medium') {
    $tagName = 'h2';
    $className = 'bearcms-heading-element-medium';
} else {
    $tagName = 'h3';
    $className = 'bearcms-heading-element-small';
}

$content = '<' . $tagName . ' class="' . $className . '">' . htmlspecialchars($text) . '</' . $tagName . '>';
?><html>
    <body><?= $content ?></body>
</html>