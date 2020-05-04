<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

/**
 * @runTestsInSeparateProcesses
 */
class BlogPostsTest extends BearCMSTestCase
{

    /**
     * 
     */
    public function testData()
    {
        $app = $this->getApp();

        $this->assertEquals($app->bearCMS->data->blogPosts->get('blogpostid'), null);
        $this->assertEquals($app->bearCMS->data->blogPosts->getList()->count(), 0);

        $app->data->setValue('bearcms/blog/post/' . md5('blogpostid') . '.json', json_encode([
            'id' => 'blogpostid',
            'title' => 'My latest post!'
        ]));

        $blogPost = $app->bearCMS->data->blogPosts->get('blogpostid');
        $this->assertEquals($blogPost->id, 'blogpostid');
        $this->assertEquals($blogPost->title, 'My latest post!');
        $this->assertEquals($blogPost->slug, null);
        $this->assertEquals($blogPost->createdTime, null);
        $this->assertEquals($blogPost->status, null);
        $this->assertEquals($blogPost->publishedTime, null);
        //$this->assertEquals($blogPost->trashedTime, null);
        $this->assertEquals($blogPost->categoriesIDs, []);
        $this->assertEquals($blogPost->titleTagContent, null);
        $this->assertEquals($blogPost->descriptionTagContent, null);
        $this->assertEquals($blogPost->keywordsTagContent, null);
        $this->assertEquals($blogPost->lastChangeTime, null);

        $this->assertEquals($app->bearCMS->data->blogPosts->getList()->count(), 1);
    }

}
