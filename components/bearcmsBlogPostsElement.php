<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use \BearFramework\App;
use BearCMS\Internal\ElementsHelper;
use BearCMS\Internal\Options;

$app = App::get();
$context = $app->context->get(__FILE__);

$list = $app->bearCMS->data->blogPosts->getList()
        ->filterBy('status', 'published')
        ->sortBy('publishedTime', 'desc');

$type = 'full';
if (strlen($component->type) > 0) {
    if (array_search($component->type, ['summary', 'full', 'titles']) !== false) {
        $type = $component->type;
    }
}

$showDate = $component->showDate === 'true';
$limit = (int) $component->limit;
if ($limit < 1) {
    $limit = 5;
}

if ($list->length === 0) {
    $content = '';
} else {
    $content = '<div class="bearcms-blog-posts-element">';
    $content .= '<div class="bearcms-blog-posts-element-posts">';

    $counter = 0;
    foreach ($list as $blogPost) {
        $counter++;
        $title = isset($blogPost['title']) ? $blogPost['title'] : 'Unknown';
        $url = $app->request->base . Options::$blogPagesPathPrefix . (isset($blogPost['slug']) ? $blogPost['slug'] : 'unknown') . '/';
        $publishedTime = isset($blogPost['publishedTime']) ? $blogPost['publishedTime'] : '';

        $content .= '<div class="bearcms-blog-posts-element-post">';

        $content .= '<div class="bearcms-blog-posts-element-post-title-container">';
        $content .= '<a title="' . htmlentities($title) . '" class="bearcms-blog-posts-element-post-title" href="' . htmlentities($url) . '">' . htmlspecialchars($title) . '</a>';
        $content .= '</div>';
        if ($showDate) {
            $content .= '<div class="bearcms-blog-posts-element-post-date-container">';
            $content .= '<span class="bearcms-blog-posts-element-post-date">';
            $content .= \BearCMS\Internal\Localization::getDate($publishedTime);
            $content .= '</span>';
            $content .= '</div>';
        }
        if ($type === 'summary' || $type === 'full') {
            $containerID = 'bearcms-blogpost-' . $blogPost['id'];
            $content .= '<div class="bearcms-blog-posts-element-post-content">';
            if ($type === 'summary') {
                $containerData = ElementsHelper::getContainerData($containerID);
                $textElementData = null;
                $imageElementData = null;

                $walkElements = function($elementID) use (&$textElementData, &$imageElementData) {
                    $data = ElementsHelper::getElementsRawData([$elementID]);
                    $elementData = json_decode($data[$elementID], true);
                    if (isset($elementData['type'])) {
                        if ($textElementData === null && $elementData['type'] === 'text') {
                            $textElementData = $elementData;
                        }
                        if ($imageElementData === null && $elementData['type'] === 'image') {
                            $imageElementData = $elementData;
                        }
                    }
                    return $textElementData !== null && $imageElementData !== null;
                };

                foreach ($containerData['elements'] as $elementContainerData) {
                    if (isset($elementContainerData['data'], $elementContainerData['data']['type']) && ($elementContainerData['data']['type'] === 'column' || $elementContainerData['data']['type'] === 'columns')) {
                        $columnsSizes = explode(':', $elementContainerData['data']['mode']);
                        $columnsCount = sizeof($columnsSizes);
                        $break = false;
                        for ($i = 0; $i < $columnsCount; $i++) {
                            if (isset($elementContainerData['data']['elements'], $elementContainerData['data']['elements'][$i])) {
                                $elementsInColumn = $elementContainerData['data']['elements'][$i];
                                if (!empty($elementsInColumn)) {
                                    foreach ($elementsInColumn as $elementInColumnContainerData) {
                                        if ($walkElements($elementInColumnContainerData['id'])) {
                                            $break = true;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        if ($break) {
                            break;
                        }
                    } else {
                        if ($walkElements($elementContainerData['id'])) {
                            break;
                        }
                    }
                }
                if ($imageElementData !== null) {
                    $content .= '<component src="bearcms-image-element" bearcms-internal-attribute-raw-data="' . htmlentities(json_encode($imageElementData)) . '"/>';
                }
                if ($textElementData !== null) {
                    $content .= '<component src="bearcms-text-element" bearcms-internal-attribute-raw-data="' . htmlentities(json_encode($textElementData)) . '"/>';
                    $readMoreText = '<a href="' . htmlentities($url) . '">' . __('bearcms.blogPosts.Read more') . '</a>';
                    $content .= '<component src="bearcms-text-element" text="' . htmlentities($readMoreText) . '"/>';
                }
            } else {
                $content .= '<component src="bearcms-elements" id="' . $containerID . '"/>';
            }
            $content .= '</div>';
        }

        $content .= '</div>';
        if ($counter >= $limit) {
            break;
        }
    }
    if ($list->length > $limit) {
        $content .= '<div class="bearcms-blog-posts-element-show-more-button-container">';
        $component->limit = (string) ($limit + 10);
        $loadMoreData = [
            'serverData' => \BearCMS\Internal\TempClientData::set(['componentHTML' => (string) $component])
        ];
        $onClick = 'bearCMS.blogPostsElement.loadMore(event,' . json_encode($loadMoreData) . ');';
        $content .= '<a class="bearcms-blog-posts-element-show-more-button" href="javascript:void(0);" onclick="' . htmlentities($onClick) . '">' . __('bearcms.blogPosts.Show more posts') . '</a>';
        $content .= '</div>';
    }
    $content .= '</div>';
}
?><html>
    <head><?php
        if ($list->length > $limit) {
            echo '<script src="' . htmlentities($context->assets->getUrl('components/bearcmsBlogPostsElement/assets/blogPostsElement.js', ['cacheMaxAge' => 999999, 'version' => 1])) . '"></script>';
            echo '<script src="' . htmlentities($context->assets->getUrl('assets/HTML5DOMDocument.js', ['cacheMaxAge' => 999999, 'version' => 1])) . '"></script>';
        }
        ?></head>
    <body><?= $content ?></body>
</html>