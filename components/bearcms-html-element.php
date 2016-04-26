<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

$content = '<div class="bearcms-html-element">' . $component->code . '</div>';

$content = \BearCMS\Internal\ElementsHelper::getElementComponentContent($component, 'html', $content);
?><html>
    <body><?= $content ?></body>
</html>