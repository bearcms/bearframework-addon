<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

$code = trim($component->code);
if ($code !== '') {
    $content = '<div class="bearcms-html-element">' . $code . '</div>';
} else {
    $content = '';
}

?><html>
    <body><?= $content ?></body>
</html>