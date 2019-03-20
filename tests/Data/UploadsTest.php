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
class UploadsTest extends BearCMSTestCase
{

    /**
     * 
     */
    public function testData()
    {
        $app = $this->getApp();
        $this->assertEquals($app->bearCMS->data->getUploadsSize(), 0);
    }

}
