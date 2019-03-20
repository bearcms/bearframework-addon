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
        $tempDir = $this->getTempDir();
        $this->makeSampleFile($tempDir . '/assets/file1.jpg', 'jpg');
        $app->assets->addDir($tempDir . '/assets');
        
        $result = $app->components->process('<component src="bearcms-image-element" filename="' . $tempDir . '/assets/file1.jpg"/>');
        $this->assertTrue(strpos($result, 'file1.jpg') !== false);
        
        $result = $app->components->process('<bearcms-image-element filename="' . $tempDir . '/assets/file1.jpg"/>');
        $this->assertTrue(strpos($result, 'file1.jpg') !== false);
    }

}
