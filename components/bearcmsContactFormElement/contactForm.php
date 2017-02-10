<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use \BearFramework\App;

$app = App::get();
$context = $app->context->get(__FILE__);

$form->constraints->setRequired('email');
$form->constraints->setEmail('email');

$form->constraints->setRequired('message');
$form->constraints->setMinLength('message', 2);

$form->onSubmit = function($values) use ($form, $component) {

    $form->throwError('asdsad');

    $email = $component->email;

    return [
        'success' => 1
    ];
};
?><html>
    <head>
        <style>
            .bearcms-contact-form-element-textarea{
                display:block;
                width:100%;
                resize: none;
                box-sizing: border-box;
                height:100px;
                padding:20px;
            }
            .bearcms-contact-form-element-send-button{
                background-color:gray;
                display:inline-block;
                padding:10px;

                margin-top: 15px;
                cursor: pointer;
            }
        </style>
    </head>
    <body><?php
        echo '<form onsubmitdone="bearCMS.contactFormElement.onSubmitFormDone(event);">';
        echo '<label for="email">Email</label>';
        echo '<input type="text" name="email" class="bearcms-contact-form-element-email"/>';
        echo '<label for="message">Message</label>';
        echo '<textarea name="message" class="bearcms-contact-form-element-textarea"></textarea>';
        echo '<span onclick="this.parentNode.submit();" href="javascript:void(0);" class="bearcms-contact-form-element-send-button">Send</span>';
        echo '<span style="display:none;" class="bearcms-contact-form-element-send-button bearcms-contact-form-element-send-button-waiting">Sending ...</span>';
        echo '</form>';
        ?></body>
</html>