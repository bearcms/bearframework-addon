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
class ImageGalleryElementTest extends BearFrameworkAddonTestCase
{

    private function requireEditable($result)
    {
        $this->assertTrue(strpos($result, '<div id="brelc') !== false);
    }

    private function requireNotEditable($result)
    {
        $this->assertTrue(strpos($result, '<div id="brelc') === false);
    }

    /**
     * 
     */
    public function testEditable()
    {
        $app = $this->getApp();
        $this->createAndLoginUser();

        $result = $app->components->process('<component src="bearcms-image-gallery-element" id="sample-element-1" editable="true"/>');
        $this->requireEditable($result);
    }

    /**
     * 
     */
    public function testNotEditable()
    {
        $app = $this->getApp();

        $result = $app->components->process('<component src="bearcms-image-gallery-element"/>');
        $this->requireNotEditable($result);

        $result = $app->components->process('<component src="bearcms-image-gallery-element" id="sample-element-1" editable="true"/>');
        $this->requireNotEditable($result);
    }

    /**
     * 
     */
    public function testOutput()
    {
        $app = $this->getApp();
        $this->createSampleFile($app->config->appDir . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'file1.jpg', 'jpg');
        $this->createSampleFile($app->config->appDir . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'file2.jpg', 'jpg');
        $this->createSampleFile($app->config->appDir . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'file3.jpg', 'jpg');
        $app->assets->addDir($app->config->appDir . DIRECTORY_SEPARATOR . 'assets');
        $result = $app->components->process('<component src="bearcms-image-gallery-element">'
                . '<file filename="app:assets' . DIRECTORY_SEPARATOR . 'file1.jpg"/>'
                . '<file filename="app:assets' . DIRECTORY_SEPARATOR . 'file2.jpg"/>'
                . '<file filename="app:assets' . DIRECTORY_SEPARATOR . 'file3.jpg"/>'
                . '</component>');
        $this->assertTrue(strpos($result, 'file1.jpg') !== false);
        $this->assertTrue(strpos($result, 'file2.jpg') !== false);
        $this->assertTrue(strpos($result, 'file3.jpg') !== false);
    }

}
