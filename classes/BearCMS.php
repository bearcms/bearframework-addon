<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use \BearCMS\Internal\Options;

/**
 * Contains references to all Bear CMS related objects.
 * 
 * @property-read \BearCMS\Data $data A reference to the data related objects
 * @property-read \BearCMS\CurrentTheme $currentTheme Information about the current theme
 * @property-read \BearCMS\CurrentUser $currentUser Information about the current loggedin user
 * @property-read \BearCMS\ElementsTypes $elementsTypes Information about the available elements types
 * @property-read \BearCMS\Themes $themes
 */
class BearCMS
{

    use \IvoPetkov\DataObjectTrait;

    /**
     * Addon version
     */
    const VERSION = 'dev';

    /**
     * The constructor
     */
    function __construct()
    {
        $this->defineProperty('data', [
            'init' => function() {
                return new \BearCMS\Data();
            },
            'readonly' => true
        ]);

        $this->defineProperty('currentTheme', [
            'init' => function() {
                return new \BearCMS\CurrentTheme();
            },
            'readonly' => true
        ]);
        $this->defineProperty('currentUser', [
            'init' => function() {
                return new \BearCMS\CurrentUser();
            },
            'readonly' => true
        ]);

        $this->defineProperty('elementsTypes', [
            'init' => function() {
                return new \BearCMS\ElementsTypes();
            },
            'readonly' => true
        ]);

        $this->defineProperty('themes', [
            'init' => function() {
                return new \BearCMS\Themes();
            },
            'readonly' => true
        ]);
    }

    public function enableUI(\BearFramework\App\Response $response): void
    {
        $response->enableBearCMSUI = true;
        $app = App::get();
        $app->users->enableUI($response);
    }

    public function applyTheme(\BearFramework\App\Response $response): void
    {
        $response->applyBearCMSTheme = true;
    }

    public function disabledCheck(): ?\BearFramework\App\Response
    {
        $app = App::get();
        $currentUserExists = Options::hasServer() && (Options::hasFeature('USERS') || Options::hasFeature('USERS_LOGIN_*')) ? $app->bearCMS->currentUser->exists() : false;
        $settings = $app->bearCMS->data->settings->get();
        $isDisabled = !$currentUserExists && $settings->disabled;
        if ($isDisabled) {
            $response = new App\Response\TemporaryUnavailable(htmlspecialchars($settings->disabledText));
            $response->content = $settings->disabledText;
            return $response;
        }
        return null;
    }

}
