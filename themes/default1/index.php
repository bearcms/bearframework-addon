<?php

/* @var $app \BearFramework\App */
/* @var $context \BearFramework\App\AppContext */

use BearFramework\App;

$context->assets->addDir('themes/default1/assets');

$app->hooks->add('responseCreated', function($response) use ($app, $context) {
    if (!empty($response->applyBearCMSTemplate)) {
        $templateContent = null;
        if ($response instanceof App\Response\HTML) {
            $templateContent = '<component src="file:' . $context->dir . '/themes/default1/components/defaultTemplate.php"/>';
        } elseif ($response instanceof App\Response\NotFound) {
            $templateContent = '<component src="file:' . $context->dir . '/themes/default1/components/defaultTemplate.php" mode="notFound"/>';
        } elseif ($response instanceof App\Response\TemporaryUnavailable) {
            $templateContent = '<component src="file:' . $context->dir . '/themes/default1/components/defaultTemplate.php" mode="temporaryUnavailable"/>';
        }

        if ($templateContent !== null) {
            $templateContent = $app->components->process($templateContent, ['recursive' => false]);
            $object = new ArrayObject();
            $object->content = $templateContent;
            $app->hooks->execute('bearCMSDefaultTheme1Created', $object);
            $templateContent = $object->content;
            $templateContent = $app->components->process($templateContent);
            $object = new ArrayObject();
            $object->content = $templateContent;
            $app->hooks->execute('bearCMSDefaultTheme1Ready', $object);

            $content = $response->content;
            $object = new ArrayObject();
            $object->content = $content;
            $app->hooks->execute('bearCMSDefaultTheme1ContentCreated', $object);
            $content = $object->content;
            $content = $app->components->process($content);
            $object = new ArrayObject();
            $object->content = $content;
            $app->hooks->execute('bearCMSDefaultTheme1ContentReady', $object);

            $template = new BearFramework\HTMLTemplate($templateContent);
            $template->insert('body', $app->components->process($response->content));
            $response->content = $template->getResult();
        }
    }
});
