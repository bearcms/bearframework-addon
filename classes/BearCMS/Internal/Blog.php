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
class Blog
{

    /**
     * 
     * @param \BearCMS $bearCMS
     * @param App\Request $request
     * @return App\Response|null
     */
    public static function handleBlogPostPageRequest(\BearCMS $bearCMS, App\Request $request): ?App\Response
    {
        $app = App::get();
        $slug = (string) $request->path->getSegment(1);
        $slugsList = Internal\Data\BlogPosts::getSlugsList('published');
        $blogPostID = array_search($slug, $slugsList);
        if ($blogPostID === false && substr($slug, 0, 1) === '-') {
            $blogPost = $bearCMS->data->blogPosts->get(substr($slug, 1));
            if ($blogPost !== null) {
                $status = $blogPost->status;
                if ($status === 'published') {
                    return new App\Response\PermanentRedirect($blogPost->getURL());
                } elseif ($status === 'draft') {
                    // allow access
                } else { // private
                    if (!((Config::hasFeature('USERS') || Config::hasFeature('USERS_LOGIN_*')) && $bearCMS->currentUser->exists())) {
                        return null;
                    }
                }
                $blogPostID = $blogPost->id;
            }
        }
        if ($blogPostID !== false) {
            $blogPost = $bearCMS->data->blogPosts->get($blogPostID);
            if ($blogPost !== null) {
                $path = $request->path->get();
                $hasSlash = substr($path, -1) === '/';
                if (!$hasSlash) {
                    return new App\Response\PermanentRedirect($request->getURL() . '/');
                }
                $applyContext = $bearCMS->makeApplyContext();
                $blogPostLanguage = (string)$blogPost->language;
                if (isset($blogPostLanguage[0])) {
                    $applyContext->language = $blogPostLanguage;
                }
                $content = '<html data-bearcms-page-type="blogPost"><head>';
                $title = isset($blogPost->titleTagContent) ? trim($blogPost->titleTagContent) : '';
                if (!isset($title[0])) {
                    $title = isset($blogPost->title) ? trim($blogPost->title) : '';
                    $title = Settings::applyPageTitleFormat($title, (string)$applyContext->language);
                }
                $description = isset($blogPost->descriptionTagContent) ? trim($blogPost->descriptionTagContent) : '';
                $keywords = isset($blogPost->keywordsTagContent) ? trim($blogPost->keywordsTagContent) : '';
                if (isset($title[0])) {
                    $content .= '<title>' . htmlspecialchars($title) . '</title>';
                }
                if (isset($description[0])) {
                    $content .= '<meta name="description" content="' . htmlentities($description) . '"/>';
                }
                if (isset($keywords[0])) {
                    $content .= '<meta name="keywords" content="' . htmlentities($keywords) . '"/>';
                }
                $content .= '<style>'
                    . '.bearcms-blogpost-page-title-container{word-break:break-word;}'
                    . '.bearcms-blogpost-page-content{word-break:break-word;}'
                    . '</style>';
                $content .= '</head><body>';
                $content .= '<div class="bearcms-blogpost-page-title-container"><h1 class="bearcms-blogpost-page-title">' . htmlspecialchars($blogPost->title) . '</h1></div>';
                $content .= '<div class="bearcms-blogpost-page-date-container"><div class="bearcms-blogpost-page-date">' . ($blogPost->status === 'published' ? $app->localization->formatDate($blogPost->publishedTime, ['date']) : ($blogPost->status === 'draft' ? __('bearcms.blogPost.draft') : __('bearcms.blogPost.private'))) . '</div></div>';
                $content .= '<div class="bearcms-blogpost-page-content"><bearcms-elements id="bearcms-blogpost-' . $blogPostID . '"/></div>';
                $settings = $bearCMS->data->settings->get();
                if ($settings->allowCommentsInBlogPosts) {
                    $content .= '<div class="bearcms-blogpost-page-comments-block-separator"><component src="bearcms-separator-element" size="large"/></div>';
                    $content .= '<div class="bearcms-blogpost-page-comments-title-container"><component src="bearcms-heading-element" text="' . __('bearcms.pages.blogPost.Comments') . '" size="small"/></div>';
                    $content .= '<div class="bearcms-blogpost-page-comments-container"><component src="bearcms-comments-element" threadID="bearcms-blogpost-' . $blogPost->id . '"/></div>';
                }
                $categoriesIDs = $blogPost->categoriesIDs;
                if ($settings->showRelatedBlogPosts && !empty($categoriesIDs)) {
                    $links = [];
                    $relatedBlogPosts = $bearCMS->data->blogPosts->getList()
                        ->filterBy('status', 'published')
                        ->sortBy('publishedTime', 'desc');
                    foreach ($relatedBlogPosts as $relatedBlogPost) {
                        if ($blogPost->id === $relatedBlogPost->id || sizeof(array_intersect($categoriesIDs, $relatedBlogPost->categoriesIDs)) === 0) {
                            continue;
                        }
                        $relatedBlogTitle = strlen($relatedBlogPost->title) > 0 ? $relatedBlogPost->title : 'Unknown';
                        $relatedBlogURL = $relatedBlogPost->getURL();
                        $links[] = '<a href="' . htmlentities($relatedBlogURL) . '" title="' . htmlentities($relatedBlogTitle) . '">' . htmlspecialchars($relatedBlogTitle) . '</a>';
                        if (sizeof($links) >= 5) {
                            break;
                        }
                    }
                    if (!empty($links)) {
                        $content .= '<div class="bearcms-blogpost-page-related-block-separator"><component src="bearcms-separator-element" size="large"/></div>';
                        $content .= '<div class="bearcms-blogpost-page-related-title-container"><component src="bearcms-heading-element" text="' . __('bearcms.pages.blogPost.Continue reading') . '" size="small"/></div>';
                        $content .= '<div class="bearcms-blogpost-page-related-container"><component src="bearcms-text-element" text="' . htmlentities(implode('<br>', $links)) . '"/></div>';
                    }
                }
                $content .= '</body></html>';

                $response = new App\Response\HTML($content);
                if ($bearCMS->hasEventListeners('internalMakeBlogPostPageResponse')) {
                    $eventDetails = new \BearCMS\Internal\MakeBlogPostPageResponseEventDetails($response, $blogPostID);
                    $bearCMS->dispatchEvent('internalMakeBlogPostPageResponse', $eventDetails);
                }
                $bearCMS->apply($response, $applyContext);
                if ($blogPost->status !== 'published') {
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
     * @param array $data
     * @return string|null
     */
    public static function handleLoadMoreServerRequest(array $data): ?string
    {
        if (isset($data['serverData'], $data['serverData'])) {
            $app = App::get();
            $serverData = Internal\TempClientData::get($data['serverData']);
            if (is_array($serverData) && isset($serverData['componentHTML'])) {
                $content = $app->components->process($serverData['componentHTML']);
                return json_encode([
                    'content' => $content
                ]);
            }
        }
        return null;
    }

    /**
     * 
     * @param \BearCMS\Internal\ThemeOptionsGroupInterface $options
     * @param array $details
     * @return void
     */
    public static function addThemesPageOptions(\BearCMS\Internal\ThemeOptionsGroupInterface $options, array $details = []): void
    {
        $group = $options->addGroup(__("bearcms.themes.options.Blog post page"));

        $groupTitle = $group->addGroup(__("bearcms.themes.options.Title"));
        $groupTitle->addOption("blogPostPageTitleCSS", "css", '', [
            "cssOptions" => array_diff(isset($details['cssOptions']) ? $details['cssOptions'] : [], ["*/focusState"]),
            "cssOutput" => [
                ["rule", ".bearcms-blogpost-page-title", "box-sizing:border-box;font-weight:normal;padding:0;margin:0;"],
                ["selector", ".bearcms-blogpost-page-title"]
            ]
        ]);

        $groupTitleContainer = $groupTitle->addGroup(__("bearcms.themes.options.Container"));
        $groupTitleContainer->addOption("blogPostPageTitleContainerCSS", "css", '', [
            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
            "cssOptions" => array_diff(isset($details['cssOptions']) ? $details['cssOptions'] : [], ["*/focusState"]),
            "cssOutput" => [
                ["rule", ".bearcms-blogpost-page-title-container", "box-sizing:border-box;"],
                ["selector", ".bearcms-blogpost-page-title-container"]
            ]
        ]);

        $groupDate = $group->addGroup(__("bearcms.themes.options.Date"));
        $groupDate->addOption("blogPostPageDateVisibility", "list", __('bearcms.themes.options.Visibility'), [
            "values" => [
                [
                    "value" => "1",
                    "name" => __('bearcms.themes.options.Visible')
                ],
                [
                    "value" => "0",
                    "name" => __('bearcms.themes.options.Hidden')
                ]
            ],
            "defaultValue" => "1"
        ]);
        $groupDate->addOption("blogPostPageDateCSS", "css", '', [
            "cssTypes" => ["cssText", "cssTextShadow"],
            "cssOptions" => array_diff(isset($details['cssOptions']) ? $details['cssOptions'] : [], ["*/focusState"]),
            "cssOutput" => [
                ["selector", ".bearcms-blogpost-page-date"]
            ]
        ]);
        $groupDateContainer = $groupDate->addGroup(__("bearcms.themes.options.Container"));
        $groupDateContainer->addOption("blogPostPageDateContainerCSS", "css", '', [
            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
            "cssOptions" => array_diff(isset($details['cssOptions']) ? $details['cssOptions'] : [], ["*/focusState"]),
            "cssOutput" => [
                ["rule", ".bearcms-blogpost-page-date-container", "box-sizing:border-box;"],
                ["selector", ".bearcms-blogpost-page-date-container"]
            ]
        ]);

        $groupContent = $group->addGroup(__("bearcms.themes.options.Content"));
        $groupContent->addOption("blogPostPageContentCSS", "css", '', [
            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
            "cssOptions" => array_diff(isset($details['cssOptions']) ? $details['cssOptions'] : [], ["*/focusState"]),
            "cssOutput" => [
                ["rule", ".bearcms-blogpost-page-content", "box-sizing:border-box;"],
                ["selector", ".bearcms-blogpost-page-content"]
            ]
        ]);

        $groupComments = $group->addGroup(__("bearcms.themes.options.Comments"));
        $groupCommentsSeparator = $groupComments->addGroup(__("bearcms.themes.options.Separator"));
        $groupCommentsSeparator->addOption("blogPostPageCommentsBlockSeparatorCSS", "css", '', [
            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
            "cssOptions" => array_diff(isset($details['cssOptions']) ? $details['cssOptions'] : [], ["*/focusState"]),
            "cssOutput" => [
                ["rule", ".bearcms-blogpost-page-comments-block-separator", "box-sizing:border-box;"],
                ["selector", ".bearcms-blogpost-page-comments-block-separator"]
            ]
        ]);
        $groupCommentsTitle = $groupComments->addGroup(__("bearcms.themes.options.Title"));
        $groupCommentsTitle->addOption("blogPostPageCommentsTitleContainerCSS", "css", '', [
            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
            "cssOptions" => array_diff(isset($details['cssOptions']) ? $details['cssOptions'] : [], ["*/focusState"]),
            "cssOutput" => [
                ["rule", ".bearcms-blogpost-page-comments-title-container", "box-sizing:border-box;"],
                ["selector", ".bearcms-blogpost-page-comments-title-container"]
            ]
        ]);
        $groupCommentsContainer = $groupComments->addGroup(__("bearcms.themes.options.Comments"));
        $groupCommentsContainer->addOption("blogPostPageCommentsContainerCSS", "css", '', [
            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
            "cssOptions" => array_diff(isset($details['cssOptions']) ? $details['cssOptions'] : [], ["*/focusState"]),
            "cssOutput" => [
                ["rule", ".bearcms-blogpost-page-comments-container", "box-sizing:border-box;"],
                ["selector", ".bearcms-blogpost-page-comments-container"]
            ]
        ]);

        $groupRelated = $group->addGroup(__("bearcms.themes.options.Related posts"));
        $groupRelatedSeparator = $groupRelated->addGroup(__("bearcms.themes.options.Separator"));
        $groupRelatedSeparator->addOption("blogPostPageRelatedBlockSeparatorCSS", "css", '', [
            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
            "cssOptions" => array_diff(isset($details['cssOptions']) ? $details['cssOptions'] : [], ["*/focusState"]),
            "cssOutput" => [
                ["rule", ".bearcms-blogpost-page-related-block-separator", "box-sizing:border-box;"],
                ["selector", ".bearcms-blogpost-page-related-block-separator"]
            ]
        ]);
        $groupRelatedTitle = $groupRelated->addGroup(__("bearcms.themes.options.Title"));
        $groupRelatedTitle->addOption("blogPostPageRelatedTitleContainerCSS", "css", '', [
            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
            "cssOptions" => array_diff(isset($details['cssOptions']) ? $details['cssOptions'] : [], ["*/focusState"]),
            "cssOutput" => [
                ["rule", ".bearcms-blogpost-page-related-title-container", "box-sizing:border-box;"],
                ["selector", ".bearcms-blogpost-page-related-title-container"]
            ]
        ]);
        $groupRelatedContainer = $groupRelated->addGroup(__("bearcms.themes.options.Related list"));
        $groupRelatedContainer->addOption("blogPostPageRelatedContainerCSS", "css", '', [
            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
            "cssOptions" => array_diff(isset($details['cssOptions']) ? $details['cssOptions'] : [], ["*/focusState"]),
            "cssOutput" => [
                ["rule", ".bearcms-blogpost-page-related-container", "box-sizing:border-box;"],
                ["selector", ".bearcms-blogpost-page-related-container"]
            ]
        ]);
    }

    /**
     * 
     * @param \BearCMS\Internal\Sitemap\Sitemap $sitemap
     * @return void
     */
    public static function addSitemapItems(\BearCMS\Internal\Sitemap\Sitemap $sitemap): void
    {
        $list = Internal\Data\BlogPosts::getSlugsList('published');
        foreach ($list as $blogPostID => $slug) {
            $sitemap->addItem(Config::$blogPagesPathPrefix . $slug . '/', function () use ($blogPostID) {
                $app = App::get();
                $dates = [];
                $date = ElementsDataHelper::getLastChangeTime('bearcms-blogpost-' . $blogPostID);
                if ($date !== null) {
                    $dates[] = $date;
                }
                $blogPost = $app->bearCMS->data->blogPosts->get($blogPostID);
                if ($blogPost !== null && strlen((string)$blogPost->lastChangeTime) > 0) {
                    $dates[] = (int)$blogPost->lastChangeTime;
                }
                return empty($dates) ? null : max($dates);
            });
        }
    }

    /**
     * 
     * @param string|null $blogPostID
     * @return void
     */
    static function setCommentsLocations(string $blogPostID = null): void
    {
        $app = App::get();
        $blogPosts = $app->bearCMS->data->blogPosts;
        if ($blogPostID !== null) {
            $blogPost = $blogPosts->get($blogPostID);
            $list = $blogPost !== null ? [$blogPost] : [];
        } else {
            $list = $blogPosts->getList();
        }
        $result = [];
        foreach ($list as $blogPost) {
            $urlPath = $blogPost->getURLPath();
            $threadID = 'bearcms-blogpost-' . $blogPost->id;
            $result[$threadID] = $urlPath;
        }
        CommentsLocations::setLocations($result);
    }

    /**
     * 
     * @param string $blogPostID
     * @return void
     */
    static function addUpdateCommentsLocationsTask(string $blogPostID): void
    {
        $app = App::get();
        $app->tasks->add('bearcms-blog-comments-locations-update', $blogPostID, [
            'id' => 'bearcms-blog-comments-locations-update-' . md5($blogPostID),
            'priority' => 4,
            'ignoreIfExists' => true
        ]);
    }
}
