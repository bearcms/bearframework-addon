<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use \BearFramework\App;

$email = strlen($component->email) > 0 ? $component->email : '';
$email = 'test@test.com';

$app = App::get();
$context = $app->context->get(__FILE__);

$content = '';
$content .= '<component src="form" filename="' . $context->dir . '/components/bearcmsContactFormElement/contactForm.php" email="' . htmlentities($email) . '" />';
$content .= '<script src="' . htmlentities($context->assets->getUrl('components/bearcmsContactFormElement/assets/contactFormElement.js')) . '"></script>';

?><html>
    <body><?= $content ?></body>
</html>