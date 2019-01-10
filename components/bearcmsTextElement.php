<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

$content = '<div class="bearcms-text-element">' . $component->text . '</div>';
?><html>
    <head><style>.bearcms-text-element{word-wrap:break-word;}</style></head>
    <body><?= $content ?></body>
</html>