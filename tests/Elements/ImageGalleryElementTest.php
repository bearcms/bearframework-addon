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
class ImageGalleryElementTest extends BearCMSTestCase
{

    /**
     * 
     */
    public function testOutput()
    {
        $app = $this->getApp();
        $tempDir = $this->getTempDir();
        $this->makeSampleFile($tempDir . '/assets/file1.jpg', 'jpg');
        $this->makeSampleFile($tempDir . '/assets/file2.jpg', 'jpg');
        $this->makeSampleFile($tempDir . '/assets/file3.jpg', 'jpg');
        $app->assets->addDir($tempDir . '/assets');
        
        $result = $app->components->process('<component src="bearcms-image-gallery-element">'
                . '<file filename="' . $tempDir . '/assets/file1.jpg"/>'
                . '<file filename="' . $tempDir . '/assets/file2.jpg"/>'
                . '<file filename="' . $tempDir . '/assets/file3.jpg"/>'
                . '</component>');
        $this->assertTrue(strpos($result, 'file1.jpg') !== false);
        $this->assertTrue(strpos($result, 'file2.jpg') !== false);
        $this->assertTrue(strpos($result, 'file3.jpg') !== false);
        
        $result = $app->components->process('<bearcms-image-gallery-element>'
                . '<file filename="' . $tempDir . '/assets/file1.jpg"/>'
                . '<file filename="' . $tempDir . '/assets/file2.jpg"/>'
                . '<file filename="' . $tempDir . '/assets/file3.jpg"/>'
                . '</bearcms-image-gallery-element>');
        $this->assertTrue(strpos($result, 'file1.jpg') !== false);
        $this->assertTrue(strpos($result, 'file2.jpg') !== false);
        $this->assertTrue(strpos($result, 'file3.jpg') !== false);
    }

}
