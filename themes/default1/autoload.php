<?php

use BearFramework\App;

$app = App::get();
$context = $app->getContext(__FILE__);

$context->assets->addDir('themes/default1/assets');
