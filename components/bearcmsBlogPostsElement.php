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

$list = $app->bearCMS->data->blog->getList(['PUBLISHED_ONLY', 'SORT_BY_PUBLISHED_TIME_DESC']);

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

if (empty($list)) {
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
            $content .= date('F j, Y', $publishedTime);
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
                    if (isset($elementContainerData['data'], $elementContainerData['data']['type']) && $elementContainerData['data']['type'] === 'column') {
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
                }
            } else {
                $content .= '<component src="bearcms-elements" id="' . $containerID . '"/>';
            }
            $content .= '</div>';
        }
        //}
//    if (isset($postContent{0})) {
//        $content .= '<div class="bearcms-blog-posts-element-post-more-link-container">';
//        $content .= '<a title="' . htmlentities($title) . '" class="bearcms-blog-posts-element-post-more-link" href="' . htmlentities($url) . '">read more</a>';
//        $content .= '</div>';
//    }
        $content .= '</div>';
        if ($counter >= $limit) {
            break;
        }
    }
    $content .= '</div>';
}

$content = \BearCMS\Internal\ElementsHelper::getElementComponentContent($component, 'blogPosts', $content);
?><html>
    <body><?= $content ?></body>
</html>