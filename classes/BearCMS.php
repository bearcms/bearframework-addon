<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
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
 * Contains references to all BearCMS related objects.
 * 
 * @property-read \BearCMS\Data $data A reference to the data related objects
 * @property-read \BearCMS\CurrentUser $currentUser Information about the current loggedin user
 * @property-read \BearCMS\Themes $themes
 * @property-read \BearCMS\Addons $addons
 */
class BearCMS
{

    use \IvoPetkov\DataObjectTrait;

    /**
     * Addon version
     */
    const VERSION = '0.5.0';

    /**
     * The constructor
     */
    function __construct()
    {
        $this
                ->defineProperty('data', [
                    'init' => function() {
                        return new \BearCMS\Data();
                    },
                    'readonly' => true
                ])
                ->defineProperty('currentUser', [
                    'init' => function() {
                        return new \BearCMS\CurrentUser();
                    },
                    'readonly' => true
                ])
                ->defineProperty('themes', [
                    'init' => function() {
                        return new \BearCMS\Themes();
                    },
                    'readonly' => true
                ])
                ->defineProperty('addons', [
                    'init' => function() {
                        return new \BearCMS\Addons();
                    },
                    'readonly' => true
                ])
        ;
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

        if (!\BearCMS\Internal\Options::$whitelabel) {
            $html .= '<meta name="generator" content="BearCMS (powered by Bear Framework)"/>';
        }
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
            $this->currentUser->getSessionKey(),
            $this->currentUser->getPermissions(),
            get_class_vars('\BearCMS\Internal\Options'),
            $serverCookies
        ]);

        $adminUIData = Server::call('adminui', $requestArguments, true, $cacheKey);
        if (is_array($adminUIData) && isset($adminUIData['result'])) {
            if ($adminUIData['result'] === 'noUser') { // The user does not exists on the server
                $this->currentUser->logout();
                return;
            }
            if (is_array($adminUIData['result']) && isset($adminUIData['result']['content']) && strlen($adminUIData['result']['content']) > 0) {
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
            }
        }
    }

    public function applyTheme(Response $response): void
    {
        $app = App::get();
        $currentThemeID = CurrentTheme::getID();
        $currentUserID = $app->bearCMS->currentUser->exists() ? $app->bearCMS->currentUser->getID() : null;
        $currentThemeOptions = $this->themes->getOptionsValues($currentThemeID, $currentUserID);
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

        if (!\BearCMS\Internal\Options::$whitelabel) {
            $domDocument = $getDocument();
            $logoSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="75.93" height="45.65" viewBox="0 0 75.929546 45.649438"><path fill="#666" d="M62.2 0c1.04-.02 2.13.8 2.55 2.14.15.56.1 1.3.43 1.6 2.02 1.88 5.34 1.64 6.04 4.9.12.75 2 2.3 2.92 3.2.8.77 2 2.13 1.76 2.86-.5 1.66-1.16 3.65-3.65 3.6-3.64-.06-7.3-.04-10.94 0-4.66.04-7.44 2.82-7.5 7.53-.05 3.8.07 7.63-.03 11.46-.08 3 1.25 4.67 4.18 5.35.93.24 1.5 1.1.84 1.9-.8 1-4.3 1-4.4 1-2.8.33-6.5-.7-8.78-6.4-1.3 1.7-2.2 2.56-3.4 2.94-.7.22-4.17 1.1-4.3.3-.25-1.44 3.9-5.03 4.07-6.5.3-2.84-2.18-3.9-5.05-4.6-2.9-.74-6 .57-7.3 1.95-1.8 1.9-1.7 7.77-.76 8.26.5.26 1.46.8 1.5 1.6 0 .6-.76 1.5-1.2 1.5-2.5.17-5.03.26-7.48-.05-.65-.08-1.6-1.66-1.6-2.54.04-2.87-5.5-7.9-6.4-6.6-1.52 2.16-6.04 3.23-5.5 6.04.34 1.8 3.9.6 4.25 2 .76 3.2-6.8 2.1-9.87 1.7-2.58-.33-3.63-1.83-1.32-6.9 2.8-5.1 3.23-10.4 2.75-16.17C3.08 9.6 11.53.97 24.08 1.3c10.9.24 21.9-.2 32.7 1.3 6.1.82 2.72.1 3.77-1.6.42-.67 1.03-1 1.65-1z"/></svg>';
            $codeToInsert = '<div style=background-color:#000;padding:15px;width:100%;text-align:center;"><a href="https://bearcms.com/" target="_blank" rel="noopener" title="This website is powered by Bear CMS" style="width:40px;height:40px;display:inline-block;background-size:80%;background-repeat:no-repeat;background-position:center center;background-image:url(data:image/svg+xml;base64,' . base64_encode($logoSvg) . ');"></a></div>';
            $html = '<body><script>document.body.insertAdjacentHTML("beforeend",' . json_encode($codeToInsert) . ');</script></body>';
            $domDocument->insertHTML($html);
            $hasChange = true;
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
        $currentUserExists = Options::hasServer() && (Options::hasFeature('USERS') || Options::hasFeature('USERS_LOGIN_*')) ? $this->currentUser->exists() : false;
        $settings = $this->data->settings->get();
        $isDisabled = !$currentUserExists && $settings->disabled;
        if ($isDisabled) {
            return new App\Response\TemporaryUnavailable(htmlspecialchars($settings->disabledText));
        }
        return null;
    }

}
