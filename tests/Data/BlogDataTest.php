<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

/**
 * @runTestsInSeparateProcesses
 */
class BlogDataTest extends BearFrameworkAddonTestCase
{

    /**
     * 
     */
    public function testUsers()
    {
        $app = $this->getApp();
        $blogPostsDir = $app->config->dataDir . '/objects/bearcms/blog/post/';
        mkdir($blogPostsDir, 0777, true);

        $this->assertFalse($app->bearCMS->data->users->hasUsers());

        file_put_contents($blogPostsDir . md5('cd763a6f1574e1d326c511a2fda878d1') . '.json', '{
    "id": "cd763a6f1574e1d326c511a2fda878d1",
    "title": "My blog post 1",
    "slug": "",
    "createdTime": 1477377111,
    "status": "published",
    "publishedTime": 1477377121,
    "images": [],
    "titleTagContent": "",
    "descriptionTagContent": "",
    "keywordsTagContent": "",
    "lastChangeTime": 1477377111
}');

        file_put_contents($blogPostsDir . md5('cd763a6f1574e1d326c511a2fda878d2') . '.json', '{
    "id": "cd763a6f1574e1d326c511a2fda878d2",
    "title": "My blog post 2",
    "slug": "",
    "createdTime": 1477377112,
    "status": "draft",
    "images": [],
    "titleTagContent": "",
    "descriptionTagContent": "",
    "keywordsTagContent": "",
    "lastChangeTime": 1477377112
}');

        file_put_contents($blogPostsDir . md5('cd763a6f1574e1d326c511a2fda878d3') . '.json', '{
    "id": "cd763a6f1574e1d326c511a2fda878d3",
    "title": "My blog post 3",
    "slug": "",
    "createdTime": 1477377113,
    "status": "published",
    "publishedTime": 1477377123,
    "images": [],
    "titleTagContent": "",
    "descriptionTagContent": "",
    "keywordsTagContent": "",
    "lastChangeTime": 1477377113
}');

        file_put_contents($blogPostsDir . md5('cd763a6f1574e1d326c511a2fda878d4') . '.json', '{
    "id": "cd763a6f1574e1d326c511a2fda878d4",
    "title": "My blog post 4",
    "slug": "",
    "createdTime": 1477377114,
    "status": "trashed",
    "trashedTime": 1477377124,
    "images": [],
    "titleTagContent": "",
    "descriptionTagContent": "",
    "keywordsTagContent": "",
    "lastChangeTime": 1477377114
}');

        $list = $app->bearCMS->data->blogPosts->getList()
                ->sortBy('createdTime');
        $this->assertTrue($list[0]->id === 'cd763a6f1574e1d326c511a2fda878d1');
        $this->assertTrue($list[1]->id === 'cd763a6f1574e1d326c511a2fda878d2');
        $this->assertTrue($list[2]->id === 'cd763a6f1574e1d326c511a2fda878d3');
        $this->assertTrue($list[3]->id === 'cd763a6f1574e1d326c511a2fda878d4');

        $list->filterBy('status', 'published');
        $this->assertTrue($list[0]->id === 'cd763a6f1574e1d326c511a2fda878d1');
        $this->assertTrue($list[1]->id === 'cd763a6f1574e1d326c511a2fda878d3');

        $blogPost = $app->bearCMS->data->blogPosts->get('cd763a6f1574e1d326c511a2fda878d4');
        $this->assertTrue($blogPost->id === 'cd763a6f1574e1d326c511a2fda878d4');
    }

}
