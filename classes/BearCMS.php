<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal\Options;
use BearFramework\App\Response;
use IvoPetkov\HTML5DOMDocument;
use BearCMS\Internal\Cookies;
use BearCMS\Internal\ElementsHelper;
use BearCMS\Internal\Server;
use BearCMS\Internal\CurrentTheme;
use BearCMS\Internal\Themes as InternalThemes;

/**
 * Contains references to all Bear CMS related objects.
 * 
 * @property-read \BearCMS\Data $data A reference to the data related objects
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

    public function apply(Response $response): void
    {
        $this->applyDefaults($response);
        $this->applyTheme($response);
        $this->applyAdminUI($response);
    }

    public function applyDefaults(Response $response): void
    {
        $app = App::get();

        if (!$response->headers->exists('Cache-Control')) {
            $response->headers->set($response->headers->make('Cache-Control', 'private, max-age=0, no-cache, no-store'));
        }

        $currentUserExists = Options::hasServer() && (Options::hasFeature('USERS') || Options::hasFeature('USERS_LOGIN_*')) ? $this->currentUser->exists() : false;
        $settings = $this->data->settings->get();

        $document = new HTML5DOMDocument();
        $document->loadHTML($response->content);

        if (isset($settings['language']) && strlen($settings['language']) > 0) {
            $html = '<html lang="' . htmlentities($settings['language']) . '">';
        } else {
            $html = '<html>';
        }
        $html .= '<head>';

        $title = '';
        $titleElement = $document->querySelector('title');
        if ($titleElement !== null && strlen($titleElement->innerHTML) > 0) {
            $title = html_entity_decode($titleElement->innerHTML);
        } else {
            $h1Element = $document->querySelector('h1');
            if ($h1Element !== null) {
                $innerHTML = $h1Element->innerHTML;
                if (isset($innerHTML{0})) {
                    $title = $innerHTML;
                    $html .= '<title>' . $innerHTML . '</title>';
                }
            }
        }

        $strlen = function(string $string) {
            return function_exists('mb_strlen') ? mb_strlen($string) : strlen($string);
        };

        $substr = function(string $string, int $start, int $length = null) {
            return function_exists('mb_substr') ? mb_substr($string, $start, $length) : substr($string, $start, $length);
        };

        $strtolower = function(string $string) {
            return function_exists('mb_strtolower') ? mb_strtolower($string) : strtolower($string);
        };

        $metaElements = $document->querySelectorAll('meta');
        $generateDescriptionMetaTag = true;
        $generateKeywordsMetaTag = true;
        foreach ($metaElements as $metaElement) {
            $metaElementName = $metaElement->getAttribute('name');
            if ($metaElementName === 'description' && $strlen($metaElement->getAttribute('content')) > 0) {
                $generateDescriptionMetaTag = false;
            } elseif ($metaElementName === 'keywords' && $strlen($metaElement->getAttribute('content')) > 0) {
                $generateKeywordsMetaTag = false;
            }
        }

        if ($generateDescriptionMetaTag || $generateKeywordsMetaTag) {
            $bodyElement = $document->querySelector('body');
            if ($bodyElement !== null) {
                $textContent = $bodyElement->innerHTML;

                $textContent = preg_replace('/<script.*?<\/script>/', '', $textContent);
                $textContent = preg_replace('/<.*?>/', ' $0 ', $textContent);
                $textContent = preg_replace('/\s/', ' ', $textContent);
                $textContent = strip_tags($textContent);
                while (strpos($textContent, '  ') !== false) {
                    $textContent = str_replace('  ', ' ', $textContent);
                }

                $textContent = html_entity_decode(trim($textContent));

                if (isset($textContent{0})) {
                    if ($generateDescriptionMetaTag) {
                        $description = $substr($textContent, 0, 150);
                        $html .= '<meta name="description" content="' . htmlentities($description . ' ...') . '"/>';
                    }
                    $wordsText = str_replace(['.', ',', '/', '\\'], '', $strtolower($textContent));
                    $words = explode(' ', $wordsText);
                    $wordsCount = array_count_values($words);
                    arsort($wordsCount);
                    $selectedWords = [];
                    foreach ($wordsCount as $word => $wordCount) {
                        $wordLength = $strlen($word);
                        if ($wordLength >= 3 && !is_numeric($word)) {
                            $selectedWords[] = $word;
                            if (sizeof($selectedWords) === 7) {
                                break;
                            }
                        }
                    }
                    $html .= '<meta name="keywords" content="' . htmlentities(implode(', ', $selectedWords)) . '"/>';
                }
            }
        }

        $html .= '<meta name="generator" content="Bear Framework v' . App::VERSION . ', Bear CMS v' . \BearCMS::VERSION . '"/>';
        $icon = $settings['icon'];
        if (isset($icon{0})) {
            $baseUrl = $app->urls->get();
            $html .= '<link rel="apple-touch-icon" sizes="57x57" href="' . htmlentities($baseUrl . '-link-rel-icon-57') . '">';
            $html .= '<link rel="apple-touch-icon" sizes="60x60" href="' . htmlentities($baseUrl . '-link-rel-icon-60') . '">';
            $html .= '<link rel="apple-touch-icon" sizes="72x72" href="' . htmlentities($baseUrl . '-link-rel-icon-72') . '">';
            $html .= '<link rel="apple-touch-icon" sizes="76x76" href="' . htmlentities($baseUrl . '-link-rel-icon-76') . '">';
            $html .= '<link rel="apple-touch-icon" sizes="114x114" href="' . htmlentities($baseUrl . '-link-rel-icon-114') . '">';
            $html .= '<link rel="apple-touch-icon" sizes="120x120" href="' . htmlentities($baseUrl . '-link-rel-icon-120') . '">';
            $html .= '<link rel="apple-touch-icon" sizes="144x144" href="' . htmlentities($baseUrl . '-link-rel-icon-144') . '">';
            $html .= '<link rel="apple-touch-icon" sizes="152x152" href="' . htmlentities($baseUrl . '-link-rel-icon-152') . '">';
            $html .= '<link rel="apple-touch-icon" sizes="180x180" href="' . htmlentities($baseUrl . '-link-rel-icon-180') . '">';
            $html .= '<link rel="icon" sizes="32x32" href="' . htmlentities($baseUrl . '-link-rel-icon-32') . '">';
            $html .= '<link rel="icon" sizes="192x192" href="' . htmlentities($baseUrl . '-link-rel-icon-192') . '">';
            $html .= '<link rel="icon" sizes="96x96" href="' . htmlentities($baseUrl . '-link-rel-icon-96') . '">';
            $html .= '<link rel="icon" sizes="16x16" href="' . htmlentities($baseUrl . '-link-rel-icon-16') . '">';
        } else if ($currentUserExists) {
            $html .= '<link rel="apple-touch-icon" sizes="57x57" href="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==">';
            $html .= '<link rel="apple-touch-icon" sizes="60x60" href="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==">';
            $html .= '<link rel="apple-touch-icon" sizes="72x72" href="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==">';
            $html .= '<link rel="apple-touch-icon" sizes="76x76" href="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==">';
            $html .= '<link rel="apple-touch-icon" sizes="114x114" href="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==">';
            $html .= '<link rel="apple-touch-icon" sizes="120x120" href="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==">';
            $html .= '<link rel="apple-touch-icon" sizes="144x144" href="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==">';
            $html .= '<link rel="apple-touch-icon" sizes="152x152" href="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==">';
            $html .= '<link rel="apple-touch-icon" sizes="180x180" href="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==">';
            $html .= '<link rel="icon" sizes="32x32" href="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==">';
            $html .= '<link rel="icon" sizes="192x192" href="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==">';
            $html .= '<link rel="icon" sizes="96x96" href="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==">';
            $html .= '<link rel="icon" sizes="16x16" href="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==">';
        }
        if (empty($settings['allowSearchEngines'])) {
            $html .= '<meta name="robots" content="noindex">';
        }
        $html .= '<link rel="canonical" href="' . htmlentities(rtrim($app->request->base . $app->request->path, '/') . '/') . '"/>';
        if ($settings['enableRSS']) {
            $html .= '<link rel="alternate" type="application/rss+xml" title="' . (isset($settings['title']) ? htmlentities(trim($settings['title'])) : '') . '" href="' . $app->request->base . '/rss.xml" />';
        }
        $html .= '</head><body>';

        if ($response instanceof Response\HTML) { // is not temporary disabled
            $externalLinksAreEnabled = !empty($settings['externalLinks']);
            if ($externalLinksAreEnabled || $currentUserExists) {
                $context = $app->context->get(__FILE__);
                $html .= '<script id="bearcms-bearframework-addon-script-10" src="' . htmlentities($context->assets->getUrl('assets/externalLinks.min.js', ['cacheMaxAge' => 999999999, 'version' => 1])) . '" async onload="bearCMS.externalLinks.initialize(' . ($externalLinksAreEnabled ? 1 : 0) . ',' . ($currentUserExists ? 1 : 0) . ');"></script>';
            }
        }
        $html .= '</body></html>';
        $document->insertHTML($html);

        if (strlen($title) > 0) {
            $imageElements = $document->querySelectorAll('img');
            foreach ($imageElements as $imageElement) {
                if (strlen($imageElement->getAttribute('alt')) === 0) {
                    $imageElement->setAttribute('alt', $title);
                }
            }
        }

        $response->content = $document->saveHTML();

        $app->users->applyUI($response);
    }

    public function applyAdminUI(Response $response): void
    {
        $currentUserExists = Options::hasServer() && (Options::hasFeature('USERS') || Options::hasFeature('USERS_LOGIN_*')) ? $this->currentUser->exists() : false;
        if (!$currentUserExists) {
            return;
        }

        $app = App::get();
        $context = $app->context->get(__FILE__);

        $settings = $this->data->settings->get();

        $serverCookies = Cookies::getList(Cookies::TYPE_SERVER);
        if (!empty($serverCookies['tmcs']) || !empty($serverCookies['tmpr'])) {
            ElementsHelper::$editorData = [];
        }

        $requestArguments = [];
        $requestArguments['hasEditableElements'] = empty(ElementsHelper::$editorData) ? '0' : '1';
        $requestArguments['hasEditableContainers'] = '0';
        $requestArguments['isDisabled'] = $settings->disabled ? '1' : '0';
        foreach (ElementsHelper::$editorData as $itemData) {
            if ($itemData[0] === 'container') {
                $requestArguments['hasEditableContainers'] = '1';
            }
        }

        $cacheKey = json_encode([
            'adminUI',
            $app->request->base,
            $requestArguments,
            $this->currentUser->getSessionKey(),
            $this->currentUser->getPermissions(),
            get_class_vars('\BearCMS\Internal\Options'),
            $serverCookies
        ]);

        $adminUIData = $app->cache->getValue($cacheKey);
        if (!is_array($adminUIData)) {
            $adminUIData = Server::call('adminui', $requestArguments, true);
            $cacheItem = $app->cache->make($cacheKey, $adminUIData);
            $cacheItem->ttl = is_array($adminUIData) && isset($adminUIData['result']) ? 99999 : 10;
            $app->cache->set($cacheItem);
        }
        // The user does not exists on the server
        if (is_array($adminUIData) && isset($adminUIData['result']) && $adminUIData['result'] === 'noUser') {
            $this->currentUser->logout();
            return;
        }

        if (is_array($adminUIData) && isset($adminUIData['result']) && is_array($adminUIData['result']) && isset($adminUIData['result']['content']) && strlen($adminUIData['result']['content']) > 0) {
            $content = $adminUIData['result']['content'];
            $content = Server::updateAssetsUrls($content, false);
            $document = new HTML5DOMDocument();
            $htmlToInsert = [];
            if (strpos($content, '{body}')) {
                $content = str_replace('{body}', (string) $document->createInsertTarget('body'), $content);
                $htmlToInsert[] = ['source' => $response->content, 'target' => 'body'];
            } elseif (strpos($content, '{jsonEncodedBody}')) {
                $content = str_replace('{jsonEncodedBody}', json_encode($app->components->process($response->content)), $content);
            }
            $document->loadHTML($content);
            $elementsHtml = ElementsHelper::getEditableElementsHtml();
            if (isset($elementsHtml[0])) {
                $htmlToInsert[] = ['source' => $elementsHtml];
            }
            $htmlToInsert[] = ['source' => '<html><body><script id="bearcms-bearframework-addon-script-4" src="' . htmlentities($context->assets->getUrl('assets/HTML5DOMDocument.min.js', ['cacheMaxAge' => 999999999, 'version' => 1])) . '" async></script></body></html>'];
            $document->insertHTMLMulti($htmlToInsert);
            $response->content = $document->saveHTML();
        } else {
            //$response = new App\Response\TemporaryUnavailable();
        }
    }

    public function applyTheme(Response $response): void
    {
        $app = App::get();
        $currentThemeID = CurrentTheme::getID();
        $currentThemeOptions = CurrentTheme::getOptions();
        if ($app->hooks->exists('bearCMSThemeApply')) {
            $app->hooks->execute('bearCMSThemeApply', $currentThemeID, $response, $currentThemeOptions);
        }

        $content = $response->content;

        $hasChange = false;
        $domDocument = null;
        $getDocument = function() use ($content, &$domDocument) {
            if ($domDocument === null) {
                $domDocument = new HTML5DOMDocument();
                $domDocument->loadHTML($content);
            }
            return $domDocument;
        };
        if (strpos($content, 'class="bearcms-blogpost-page-date-container"') !== false && $currentThemeOptions['blogPostPageDateVisibility'] === '0') {
            $domDocument = $getDocument();
            $element = $domDocument->querySelector('div.bearcms-blogpost-page-date-container');
            if ($element) {
                $element->parentNode->removeChild($element);
                $hasChange = true;
            }
        }
        if ($hasChange) {
            $response->content = $domDocument->saveHTML();
        }

        if (isset(InternalThemes::$list[$currentThemeID], InternalThemes::$list[$currentThemeID]['apply'])) {
            $callback = InternalThemes::$list[$currentThemeID]['apply'];
            if (is_callable($callback)) {
                call_user_func($callback, $response, $currentThemeOptions);
            }
        }
        if ($app->hooks->exists('bearCMSThemeApplied')) {
            $app->hooks->execute('bearCMSThemeApplied', $currentThemeID, $response, $currentThemeOptions);
        }
    }

    public function disabledCheck(): ?Response
    {
        $app = App::get();
        $currentUserExists = Options::hasServer() && (Options::hasFeature('USERS') || Options::hasFeature('USERS_LOGIN_*')) ? $this->currentUser->exists() : false;
        $settings = $this->data->settings->get();
        $isDisabled = !$currentUserExists && $settings->disabled;
        if ($isDisabled) {
            return new App\Response\TemporaryUnavailable(htmlspecialchars($settings->disabledText));
        }
        return null;
    }

}
