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
class PagesTest extends BearCMSTestCase
{

    /**
     * 
     */
    public function testData()
    {
        $app = $this->getApp();

        $this->assertEquals($app->bearCMS->data->pages->get('pageid'), null);
        $this->assertEquals($app->bearCMS->data->pages->getList()->count(), 0);

        $app->data->setValue('bearcms/pages/page/' . md5('pageid') . '.json', json_encode([
            'id' => 'pageid',
            'name' => 'Page 1'
        ]));
        $app->data->setValue('bearcms/pages/structure.json', json_encode([
            ['id' => 'pageid']
        ]));

        $page = $app->bearCMS->data->pages->get('pageid');
        $this->assertEquals($page->id, 'pageid');
        $this->assertEquals($page->name, 'Page 1');
        $this->assertEquals($page->parentID, null);
        $this->assertEquals($page->status, null);
        $this->assertEquals($page->slug, null);
        $this->assertEquals($page->path, null);
        $this->assertEquals($page->titleTagContent, null);
        $this->assertEquals($page->descriptionTagContent, null);
        $this->assertEquals($page->keywordsTagContent, null);
        $this->assertEquals($page->children->count(), 0);

        $this->assertEquals($app->bearCMS->data->pages->getList()->count(), 1);
    }

}
