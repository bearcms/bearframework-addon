<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;
use BearCMS\Internal;
use BearCMS\Internal\Data\Settings;

/**
 * @internal
 * @codeCoverageIgnore
 */
class Pages
{

    /**
     * 
     * @param \BearCMS $bearCMS
     * @param App\Request $request
     * @return App\Response|null
     */
    public static function handlePageRequest(\BearCMS $bearCMS, App\Request $request): ?App\Response
    {
        $app = App::get();
        $path = $request->path->get();
        if ($path === '/') {
            if (Config::$autoCreateHomePage) {
                $pageID = 'home';
            } else {
                $pageID = false;
            }
        } else {
            $hasSlash = substr($path, -1) === '/';
            $pathsList = Internal\Data\Pages::getPathsList((Config::hasFeature('USERS') || Config::hasFeature('USERS_LOGIN_*')) && $bearCMS->currentUser->exists() ? 'all' : 'publicOrSecret');
            if ($hasSlash) {
                $pageID = array_search($path, $pathsList);
            } else {
                $pageID = array_search($path . '/', $pathsList);
                if ($pageID !== false) {
                    return new App\Response\PermanentRedirect($request->getURL() . '/');
                }
            }
        }
        if ($pageID !== false) {

            $settings = $bearCMS->data->settings->get();
            $applyContext = $bearCMS->makeApplyContext();
            $potentialLanguage = (string)$request->path->getSegment(0);
            if (strlen($potentialLanguage) > 0 && array_search($potentialLanguage, $settings->languages) !== false) {
                $applyContext->language = $potentialLanguage;
            }

            $title = '';
            $description = '';
            $keywords = '';
            $status = null;
            $found = false;
            $page = $bearCMS->data->pages->get($pageID);
            if ($page !== null) {
                $title = isset($page->titleTagContent) ? trim($page->titleTagContent) : '';
                if (!isset($title[0])) {
                    $title = isset($page->name) ? trim($page->name) : '';
                    if ($pageID !== 'home') {
                        $title = Settings::applyPageTitleFormat($title, (string)$applyContext->language);
                    }
                }
                $description = isset($page->descriptionTagContent) ? trim($page->descriptionTagContent) : '';
                $keywords = isset($page->keywordsTagContent) ? trim($page->keywordsTagContent) : '';
                $found = true;
                $status = $page->status;
            }
            if ($pageID === 'home') {
                if (!isset($title[0])) {
                    $title = trim((string)$settings->title);
                }
                if (!isset($description[0])) {
                    $description = trim((string)$settings->description);
                }
                $found = true;
                $status = 'public';
            }
            if ($found) {
                $content = '<html><head>';
                if (isset($title[0])) {
                    $content .= '<title>' . htmlspecialchars($title) . '</title>';
                }
                if (isset($description[0])) {
                    $content .= '<meta name="description" content="' . htmlentities($description) . '"/>';
                }
                if (isset($keywords[0])) {
                    $content .= '<meta name="keywords" content="' . htmlentities($keywords) . '"/>';
                }
                $content .= '</head><body>';
                $content .= '<bearcms-elements id="bearcms-page-' . $pageID . '" editable="true"/>';
                $content .= '</body></html>';

                $response = new App\Response\HTML($content);
                if ($bearCMS->hasEventListeners('internalMakePageResponse')) {
                    $eventDetails = new \BearCMS\Internal\MakePageResponseEventDetails($response, $pageID);
                    $bearCMS->dispatchEvent('internalMakePageResponse', $eventDetails);
                }

                $bearCMS->apply($response, $applyContext);
                if ($status !== 'public') {
                    $response->headers->set($response->headers->make('Cache-Control', 'no-cache, no-store, must-revalidate, private, max-age=0'));
                    $response->headers->set($response->headers->make('X-Robots-Tag', 'noindex, nofollow'));
                }
                return $response;
            }
        }
        return null;
    }
}
