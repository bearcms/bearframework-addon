<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;

/**
 * @internal
 * @codeCoverageIgnore
 */
class Settings
{

    /**
     * 
     * @param App $app
     * @param \BearCMS $bearCMS
     * @param App\Request $request
     * @return App\Response|null
     */
    public static function handleRedirectRequest(App $app, \BearCMS $bearCMS, App\Request $request): ?App\Response
    {
        $settings = $bearCMS->data->settings->get();
        $path = strtolower($request->path->get());
        $redirects = $settings->redirects;
        $location = null;
        if (isset($redirects[$path])) {
            $location = $redirects[$path];
        } elseif (isset($redirects[$path . '/'])) {
            $location = $redirects[$path . '/'];
        }
        if ($location !== null) {
            $location = '/' . ltrim($location, '/');
            if (strpos($location, '://') === false) {
                $location = $app->urls->get($location);
            }
            return new App\Response\PermanentRedirect($location);
        }
        return null;
    }
}
