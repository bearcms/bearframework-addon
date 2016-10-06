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
class ImageElementTest extends BearFrameworkAddonTestCase
{

    private function requireEditable($result)
    {
        $this->assertTrue(strpos($result, '<body><div id="brelc') !== false);
    }

    private function requireNotEditable($result)
    {
        $this->assertTrue(strpos($result, '<body><div id="brelc') === false);
    }

    /**
     * 
     */
    public function testEditable()
    {
        $app = $this->getApp();
        $this->createAndLoginUser();

        $result = $app->components->process('<component src="bearcms-image-element" id="sample-element-1" editable="true"/>');
        $this->requireEditable($result);
    }

    /**
     * 
     */
    public function testNotEditable()
    {
        $app = $this->getApp();

        $result = $app->components->process('<component src="bearcms-image-element"/>');
        $this->requireNotEditable($result);

        $result = $app->components->process('<component src="bearcms-image-element" id="sample-element-1" editable="true"/>');
        $this->requireNotEditable($result);
    }

    /**
     * 
     */
    public function testOutput()
    {
        $app = $this->getApp();
        $this->createSampleFile($app->config->appDir . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'file1.jpg', 'jpg');
        $app->assets->addDir($app->config->appDir . DIRECTORY_SEPARATOR . 'assets');
        $result = $app->components->process('<component src="bearcms-image-element" filename="app:assets' . DIRECTORY_SEPARATOR . 'file1.jpg"/>');
        $this->assertTrue(strpos($result, 'file1.jpg') !== false);
    }

}
