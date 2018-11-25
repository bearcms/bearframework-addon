<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

require __DIR__ . '/vendor/autoload.php';

$app = new BearFramework\App();
$app->addons->add('bearcms/bearframework-addon');

$docsGenerator = new IvoPetkov\DocsGenerator(__DIR__, ['/classes']);
$docsGenerator->generateMarkdown(__DIR__ . '/docs/markdown');
