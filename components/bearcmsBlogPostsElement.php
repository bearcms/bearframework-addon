<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal2;
use BearCMS\Internal\ElementsHelper;
use BearCMS\Internal\Data\Elements as InternalDataElements;

$app = App::get();
$context = $app->contexts->get(__DIR__);

$outputType = (string) $component->getAttribute('output-type');
$outputType = isset($outputType[0]) ? $outputType : 'full-html';
$isFullHtmlOutputType = $outputType === 'full-html';

$source = 'allPosts';
$componentSource = (string)$component->source;
if (strlen($componentSource) > 0 && array_search($componentSource, ['allPosts', 'postsInCategories']) !== false) {
    $source = $componentSource;
}

$list = $app->bearCMS->data->blogPosts->getList()
    ->filterBy('status', 'published')
    ->sortBy('publishedTime', 'desc');

if ($source === 'postsInCategories') {
    $componentSourceCategoriesIDs = (string)$component->sourceCategoriesIDs;
    $categoriesIDs = strlen($componentSourceCategoriesIDs) > 0 ? explode(';', $componentSourceCategoriesIDs) : [];
    $list->filter(function ($blogPost) use ($categoriesIDs) {
        if (isset($blogPost->categoriesIDs)) {
            foreach ($blogPost->categoriesIDs as $categoryID) {
                if (array_search($categoryID, $categoriesIDs) !== false) {
                    $blogCategory = Internal2::$data2->blogCategories->get($categoryID);
                    if ($blogCategory !== null && $blogCategory->status === 'published') {
                        return true;
                    }
                }
            }
        }
        return false;
    });
}

$type = 'full';
$componentType = (string)$component->type;
if (strlen($componentType) > 0 && array_search($componentType, ['summary', 'full', 'titles']) !== false) {
    $type = $componentType;
}

$spacing = '';
$componentSpacing = (string)$component->spacing;
if (strlen($componentSpacing) > 0) {
    $spacing = $componentSpacing;
}

$showDate = $component->showDate === 'true';
$limit = (int) $component->limit;
if ($limit < 1) {
    $limit = 5;
}

$showSummaryReadMoreButtonAttributeValue = $component->showSummaryReadMoreButton;
$showSummaryReadMoreButton = $type === 'summary' && ($showSummaryReadMoreButtonAttributeValue === 'true' || $showSummaryReadMoreButtonAttributeValue === '');

$showLoadMoreButtonAttributeValue = $component->showLoadMoreButton;
$showLoadMoreButton = $showLoadMoreButtonAttributeValue === 'true' || $showLoadMoreButtonAttributeValue === '';

$content = '<div' . ($isFullHtmlOutputType ? ' class="bearcms-blog-posts-element"' : '') . '>';
$content .= '<div' . ($isFullHtmlOutputType ? ' class="bearcms-blog-posts-element-posts"' : '') . '>';
if ($list->count() > 0) {
    $counter = 0;
    foreach ($list as $blogPost) {
        $counter++;
        $blogPostTitle = (string)$blogPost->title;
        $title = strlen($blogPostTitle) > 0 ? $blogPostTitle : 'Unknown';
        $url = $blogPost->getURL();
        $publishedTime = $blogPost->publishedTime;

        $content .= '<div' . ($isFullHtmlOutputType ? ' class="bearcms-blog-posts-element-post"' : '') . '>';

        $content .= '<div' . ($isFullHtmlOutputType ? '  class="bearcms-blog-posts-element-post-title-container"' : '') . '>';
        $content .= '<a title="' . htmlentities($title) . '"' . ($isFullHtmlOutputType ? '  class="bearcms-blog-posts-element-post-title"' : '') . ' href="' . htmlentities($url) . '">' . htmlspecialchars($title) . '</a>';
        $content .= '</div>';
        if ($showDate) {
            $content .= '<div' . ($isFullHtmlOutputType ? '  class="bearcms-blog-posts-element-post-date-container"' : '') . '>';
            $content .= '<span' . ($isFullHtmlOutputType ? '  class="bearcms-blog-posts-element-post-date"' : '') . '>';
            $content .= $app->localization->formatDate($publishedTime, ['date']);
            $content .= '</span>';
            $content .= '</div>';
        }
        if ($type === 'summary' || $type === 'full') {
            $containerID = 'bearcms-blogpost-' . $blogPost->id;
            $content .= '<div' . ($isFullHtmlOutputType ? '  class="bearcms-blog-posts-element-post-content"' : '') . '>';
            if ($type === 'summary') {
                $elementsIDs = ElementsHelper::getContainerElementsIDs($containerID);
                $textElementData = null;
                $imageElementData = null;
                foreach ($elementsIDs as $elementID) {
                    $elementData = InternalDataElements::getElement($elementID);
                    if ($elementData === null) {
                        continue;
                    }
                    if ($textElementData === null && $elementData['type'] === 'text') {
                        $textElementData = $elementData;
                    }
                    if ($imageElementData === null && $elementData['type'] === 'image') {
                        $imageElementData = $elementData;
                    }
                    if ($textElementData !== null && $imageElementData !== null) {
                        break;
                    }
                }

                $hasImage = $imageElementData !== null;
                $hasText = $textElementData !== null;
                if ($hasImage && $hasText) {
                    $content .= ElementsHelper::renderFloatingBox([
                        'type' => 'floatingBox',
                        'elements' => [
                            'inside' => [
                                ['id' => $imageElementData['id']]
                            ],
                            'outside' => [
                                ['id' => $textElementData['id']]
                            ]
                        ],
                        'style' => [
                            'position' => 'left',
                            'width' => '33%',
                        ]
                    ], false, [
                        'spacing' => $spacing,
                        'width' => '100%',
                        'color' => '#000',
                        'inElementsContainer' => true
                    ], true, $outputType);
                } elseif ($hasImage) {
                    $content .= '<component output-type="' . $outputType . '" src="bearcms-image-element" bearcms-internal-attribute-raw-data="' . htmlentities(json_encode($imageElementData)) . '"/>';
                } elseif ($hasText) {
                    $content .= '<component output-type="' . $outputType . '" src="bearcms-text-element" bearcms-internal-attribute-raw-data="' . htmlentities(json_encode($textElementData)) . '"/>';
                }
                if ($showSummaryReadMoreButton && ($hasImage || $hasText)) {
                    $readMoreText = '<a href="' . htmlentities($url) . '">' . __('bearcms.blogPosts.Read more') . '</a>';
                    $content .= '<component output-type="' . $outputType . '" src="bearcms-text-element" text="' . htmlentities($readMoreText) . '"/>';
                }
            } else {
                $content .= '<component output-type="' . $outputType . '" src="bearcms-elements" id="' . $containerID . '"/>';
            }
            $content .= '</div>';
        }

        $content .= '</div>';
        if ($counter >= $limit) {
            break;
        }
    }
    if ($showLoadMoreButton && $isFullHtmlOutputType && $list->count() > $limit) {
        $content .= '<div class="bearcms-blog-posts-element-show-more-button-container">';
        $component->limit = (string) ($limit + 10);
        $loadMoreData = [
            'serverData' => \BearCMS\Internal\TempClientData::set(['componentHTML' => (string) $component])
        ];
        $onClick = 'bearCMS.blogPostsElement.loadMore(event,' . json_encode($loadMoreData) . ');';
        $content .= '<a class="bearcms-blog-posts-element-show-more-button" href="javascript:void(0);" onclick="' . htmlentities($onClick) . '">' . __('bearcms.blogPosts.Show more posts') . '</a>';
        $content .= '</div>';
    }
}
$content .= '</div>';
$content .= '</div>';

echo '<html>';

echo '<head>';
if ($isFullHtmlOutputType) {
    echo '<style>.bearcms-blog-posts-element-post-title{word-break:break-word;}</style>';
    if ($list->count() > $limit) {
        echo '<link rel="client-packages-embed" name="-bearcms-blog-posts-element">';
    }
}
echo '</head>';

echo '<body>';
echo $content;
echo '</body>';

echo '</html>';
