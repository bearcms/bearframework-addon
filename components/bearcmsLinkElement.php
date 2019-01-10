<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

$url = $component->url;
$text = $component->text;
$title = $component->title;
$content = '<div class="bearcms-link-element"><a title="' . htmlentities($title) . '" href="' . htmlentities($url) . '">' . htmlspecialchars(isset($text{0}) ? $text : $url) . '</a></div>';
?><html>
    <head><style>.bearcms-link-element{word-wrap:break-word;}</style></head>
    <body><?= $content ?></body>
</html>