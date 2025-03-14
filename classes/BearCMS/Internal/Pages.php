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
use BearCMS\Internal\Data\Elements as InternalDataElements;
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
                    $tempRequest = clone ($request);
                    $tempRequest->path->set($path . '/');
                    return new App\Response\PermanentRedirect($tempRequest->getURL());
                }
            }
        }
        if ($pageID !== false) {

            $settings = $bearCMS->data->settings->get();
            $applyContext = $bearCMS->makeApplyContext();
            $contextLanguage = (string)$request->path->getSegment(0);
            if (isset($contextLanguage[0]) && array_search($contextLanguage, $settings->languages) !== false) {
                $applyContext->language = $contextLanguage;
            } else {
                $contextLanguage = '';
            }

            $title = '';
            $description = '';
            $keywords = '';
            $tags = [];
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
                $tags = $page->tags;
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
                if ($pageID === 'home') {
                    $pageType = 'home';
                } elseif ($contextLanguage !== '' && $request->path->getSegment(1) === null) {
                    $pageType = 'language-home';
                } else {
                    $pageType = 'other';
                }
                $content = '<html data-bearcms-page-type="' . $pageType . '"' . ($bearCMS->currentUser->exists() ? ' data-bearcms-editor-page="page:' . $pageID . '"' : '') . '' . (empty($tags) ? '' : ' data-bearcms-tags="' . implode(' ', $tags) . '"') . '><head>';
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

    /**
     * 
     * @param \BearCMS\Internal\Sitemap\Sitemap $sitemap
     * @return void
     */
    public static function addSitemapItems(\BearCMS\Internal\Sitemap\Sitemap $sitemap): void
    {
        $list = Internal\Data\Pages::getPathsList('public');
        if (Config::$autoCreateHomePage) {
            $list['home'] = '/';
        }
        foreach ($list as $pageID => $path) {
            $sitemap->addItem($path, function () use ($pageID) {
                $app = App::get();
                $dates = [];
                $date = ElementsDataHelper::getLastChangeTime('bearcms-page-' . $pageID);
                if ($date !== null) {
                    $dates[] = $date;
                }
                $page = $app->bearCMS->data->pages->get($pageID);
                if ($page !== null && strlen((string)$page->lastChangeTime) > 0) {
                    $dates[] = (int)$page->lastChangeTime;
                }
                return empty($dates) ? null : max($dates);
            });
        }
    }

    /**
     * 
     * @param string $pageID
     * @return void
     */
    static function createNewPageHeadingElement(string $pageID): void
    {
        $app = App::get();
        $page = $app->bearCMS->data->pages->get($pageID);
        if ($page === null) {
            return;
        }
        $containerID = 'bearcms-page-' . $pageID;
        $containerData = InternalDataElements::getContainer($containerID, true);
        if (empty($containerData['elements'])) {
            $containerData['id'] = $containerID;
            $elementType = 'heading';
            $elementData = [
                'text' => $page->name,
                'size' => 'large'
            ];
            ElementsDataHelper::addElement($elementType, $elementData, $containerID, ['insideContainer'], ['containerData' => $containerData]);
        }
    }

    /**
     * 
     * @param string|null $pageID
     * @return void
     */
    static function setCommentsLocations(?string $pageID = null): void
    {
        $app = App::get();
        $pages = $app->bearCMS->data->pages;
        if ($pageID !== null) {
            $page = $pages->get($pageID);
            $list = $page !== null ? [$page] : [];
        } else {
            $list = $pages->getList();
        }
        $result = [];
        foreach ($list as $page) {
            $urlPath = $page->path;
            $containerID = 'bearcms-page-' . $page->id;
            $containerElementIDs = Internal\ElementsDataHelper::getContainerElementsIDs($containerID, 'nonStructural');
            $elementsRawData = InternalDataElements::getElementsRawData($containerElementIDs);
            foreach ($elementsRawData as $elementRawData) {
                if ($elementRawData === null) {
                    continue;
                }
                $elementData = InternalDataElements::decodeElementRawData($elementRawData);
                if (is_array($elementData) && $elementData['type'] === 'comments') {
                    if (isset($elementData['data']['threadID'])) {
                        $result[$elementData['data']['threadID']] = $urlPath;
                    }
                }
            }
        }
        CommentsLocations::setLocations($result);
    }

    /**
     * 
     * @param string $pageID
     * @return void
     */
    static function addUpdateCommentsLocationsTask(string $pageID): void
    {
        $app = App::get();
        $app->tasks->add('bearcms-page-comments-locations-update', $pageID, [
            'id' => 'bearcms-page-comments-locations-update-' . md5($pageID),
            'priority' => 4,
            'ignoreIfExists' => true
        ]);
    }
}
