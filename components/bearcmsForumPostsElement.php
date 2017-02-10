<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use \BearFramework\App;

$forumCategoryID = '1213123123';

$app = App::get();
$context = $app->context->get(__FILE__);

$posts = $app->bearCMS->data->forumPosts->getList()->filterBy('categoryID', $forumCategoryID);

$content = '';
$newPostUrl = $app->request->base . '/f/' . $forumCategoryID . '/';
$content = '<a href="' . htmlentities($newPostUrl) . '">New post<a><br>';
foreach ($posts as $post) {
    $postUrl = $app->request->base . '/f/' . $post->id . '/' . $post->id . '/';
    $content .= '<a href="' . htmlentities($postUrl) . '">' . htmlspecialchars($post->title) . '<a><br>';
}

?><html>
    <body><?= $content ?></body>
</html>