<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

require __DIR__ . '/../vendor/autoload.php';

//$test = new class extends BearCMSTestCase {
//
//    public function getApp(): \BearFramework\App
//    {
//        $app = parent::getApp();
//        return $app;
//    }
//
//    public function setUp()
//    {
//        parent::setUp();
//        $this->createAndLoginUser();
//        $this->logoutUser();
//    }
//};
//
//$test->setUp();
//$app = $test->getApp();
//
//$app->data->setValue('bearcms/pages/page/' . md5('pageid') . '.json', json_encode([
//    'id' => 'pageid',
//    'name' => 'Page 1'
//]));
//$app->data->setValue('bearcms/pages/structure.json', json_encode([
//    ['id' => 'pageid']
//]));
//
//print_r($app->bearCMS->data->pages->getList()->count());
