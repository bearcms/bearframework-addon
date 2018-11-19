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
class PagesDataTest extends BearCMSTestCase
{

    /**
     * 
     */
    public function testPages()
    {
        $app = $this->getApp();

        $app->data->setValue('bearcms/pages/page/' . md5('ec983ebad290fc046a7308661627fef1') . '.json', '{
    "id": "ec983ebad290fc046a7308661627fef1",
    "name": "Products",
    "slug": "products",
    "parentID": "",
    "path": "\/products\/",
    "status": "published",
    "titleTagContent": "",
    "descriptionTagContent": "",
    "keywordsTagContent": ""
}');

        $app->data->setValue('bearcms/pages/page/' . md5('ec983ebad290fc046a7308661627fef2') . '.json', '{
    "id": "ec983ebad290fc046a7308661627fef2",
    "name": "Printers",
    "slug": "printers",
    "parentID": "ec983ebad290fc046a7308661627fef1",
    "path": "\/products\/printers\/",
    "status": "published",
    "titleTagContent": "",
    "descriptionTagContent": "",
    "keywordsTagContent": ""
}');

        $app->data->setValue('bearcms/pages/page/' . md5('ec983ebad290fc046a7308661627fef3') . '.json', '{
    "id": "ec983ebad290fc046a7308661627fef3",
    "name": "Laptops",
    "slug": "laptops",
    "parentID": "ec983ebad290fc046a7308661627fef1",
    "path": "\/products\/laptops\/",
    "status": "published",
    "titleTagContent": "",
    "descriptionTagContent": "",
    "keywordsTagContent": ""
}');

        $app->data->setValue('bearcms/pages/page/' . md5('ec983ebad290fc046a7308661627fef4') . '.json', '{
    "id": "ec983ebad290fc046a7308661627fef4",
    "name": "Contacts",
    "slug": "contacts",
    "parentID": "",
    "path": "\/contacts\/",
    "status": "published",
    "titleTagContent": "",
    "descriptionTagContent": "",
    "keywordsTagContent": ""
}');

        $app->data->setValue('bearcms/pages/page/' . md5('ec983ebad290fc046a7308661627fef5') . '.json', '{
    "id": "ec983ebad290fc046a7308661627fef5",
    "name": "Services",
    "slug": "services",
    "parentID": "",
    "path": "\/services\/",
    "status": "notPublished",
    "titleTagContent": "",
    "descriptionTagContent": "",
    "keywordsTagContent": ""
}');

        $app->data->setValue('bearcms/pages/structure.json', '[
    {
        "id": "ec983ebad290fc046a7308661627fef1",
        "children": [
            {
                "id": "ec983ebad290fc046a7308661627fef2"
            },
            {
                "id": "ec983ebad290fc046a7308661627fef3"
            }
        ]
    },
    {
        "id": "ec983ebad290fc046a7308661627fef4"
    },
    {
        "id": "ec983ebad290fc046a7308661627fef5"
    }
]');

        $list = $app->bearCMS->data->pages->getList()
                ->sortBy('id');
        $this->assertTrue($list[0]->id === 'ec983ebad290fc046a7308661627fef1');
        $this->assertTrue($list[1]->id === 'ec983ebad290fc046a7308661627fef2');
        $this->assertTrue($list[2]->id === 'ec983ebad290fc046a7308661627fef3');
        $this->assertTrue($list[3]->id === 'ec983ebad290fc046a7308661627fef4');
        $this->assertTrue($list[4]->id === 'ec983ebad290fc046a7308661627fef5');

        $list = $app->bearCMS->data->pages->getList() //default sort
                ->filterBy('parentID', null)
                ->filterBy('status', 'published');
        $this->assertTrue($list[0]->id === 'ec983ebad290fc046a7308661627fef1');
        $this->assertTrue($list[1]->id === 'ec983ebad290fc046a7308661627fef4');

        $user = $app->bearCMS->data->pages->get('ec983ebad290fc046a7308661627fef1');
        $this->assertTrue($user->id === 'ec983ebad290fc046a7308661627fef1');
    }

}
