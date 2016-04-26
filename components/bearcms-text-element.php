<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

$content = '<div class="bearcms-text-element">' . $component->text . '</div>';

$content = \BearCMS\Internal\ElementsHelper::getElementComponentContent($component, 'text', $content);
?><html>
    <body><?= $content ?></body>
</html>