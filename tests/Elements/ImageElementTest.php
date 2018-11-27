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
class ImageElementTest extends BearCMSTestCase
{

    /**
     * 
     */
    public function testOutput()
    {
        $app = $this->getApp();
        $this->makeSampleFile($app->config->appDir . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'file1.jpg', 'jpg');
        $app->assets->addDir($app->config->appDir . DIRECTORY_SEPARATOR . 'assets');
        $result = $app->components->process('<component src="bearcms-image-element" filename="app:assets' . DIRECTORY_SEPARATOR . 'file1.jpg"/>');
        $this->assertTrue(strpos($result, 'file1.jpg') !== false);
    }

}
