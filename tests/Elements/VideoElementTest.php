<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

/**
 * @runTestsInSeparateProcesses
 */
class VideoElementTest extends BearFrameworkAddonTestCase
{

    private function requireEditable($result)
    {
        $this->assertTrue(strpos($result, '<div id="brelc') !== false);
    }

    private function requireNotEditable($result)
    {
        $this->assertTrue(strpos($result, '<div id="brelc') === false);
    }

    private function requireValidFilenameHTML($result)
    {
        $this->assertTrue(strpos($result, '<video') !== false);
        $this->assertTrue(strpos($result, 'file1.mp4') !== false);
        $this->assertTrue(strpos($result, '<div class="bearcms-video-element"') !== false);
    }

    private function requireValidUrlHTML($result)
    {
//        $this->assertTrue(strpos($result, '<iframe') !== false);
//        $this->assertTrue(strpos($result, 'https://www.youtube.com/embed/Pwe-pA6TaZk?feature=oembed') !== false);
//        $this->assertTrue(strpos($result, '<div class="bearcms-video-element"') !== false);
    }

    private function createSampleVideoFiles()
    {
        $app = $this->getApp();
        $this->createDir($app->config->appDir . '/assets/');
        $app->assets->addDir($app->config->appDir . '/assets/');
        $this->createFile($app->config->appDir . '/assets/file1.mp4', 'content');
    }

    /**
     * 
     */
    public function testEditable()
    {
        $app = $this->getApp();
        $this->createAndLoginUser();

        $result = $app->components->process('<component src="bearcms-video-element" id="sample-element-1" editable="true"/>');
        $this->requireEditable($result);
    }

    /**
     * 
     */
    public function testNotEditable()
    {
        $app = $this->getApp();

        $result = $app->components->process('<component src="bearcms-video-element"/>');
        $this->requireNotEditable($result);

        $result = $app->components->process('<component src="bearcms-video-element" id="sample-element-1" editable="true"/>');
        $this->requireNotEditable($result);
    }

    /**
     * 
     */
    public function testEditableWithFilename()
    {
        $app = $this->getApp();
        $this->createAndLoginUser();
        $this->createSampleVideoFiles();

        $result = $app->components->process('<component src="bearcms-video-element" id="sample-element-1" editable="true" filename="app:assets/file1.mp4" />');
        $this->requireEditable($result);
        $this->requireValidFilenameHTML($result);
    }

    /**
     * 
     */
    public function testNotEditableWithFilename()
    {
        $app = $this->getApp();
        $this->createSampleVideoFiles();

        $result = $app->components->process('<component src="bearcms-video-element" filename="app:assets/file1.mp4" />');
        $this->requireNotEditable($result);
        $this->requireValidFilenameHTML($result);

        $result = $app->components->process('<component src="bearcms-video-element" id="sample-element-1" editable="true" filename="app:assets/file1.mp4" />');
        $this->requireNotEditable($result);
        $this->requireValidFilenameHTML($result);
    }

    /**
     * Missing file
     */
    public function testInvalidFilename1()
    {
        $app = $this->getApp();

        $this->setExpectedException('Exception');
        $app->components->process('<component src="bearcms-video-element" filename="app:assets/file2.mp4" />');
    }

    /**
     * Not registered assets dir
     */
    public function testInvalidFilename2()
    {
        $app = $this->getApp();
        $this->createFile($app->config->appDir . '/assets/file1.mp4', 'content');

        $this->setExpectedException('Exception');
        $app->components->process('<component src="bearcms-video-element" filename="app:assets/file1.mp4" />');
    }

    /**
     * 
     */
    public function testEditableWithUrl()
    {
        $app = $this->getApp();
        $this->createAndLoginUser();

        $result = $app->components->process('<component src="bearcms-video-element" id="sample-element-1" editable="true" url="https://www.youtube.com/watch?v=Pwe-pA6TaZk" />');
        $this->requireEditable($result);
        $this->requireValidUrlHTML($result);
    }

    /**
     * 
     */
    public function testNotEditableWithUrl()
    {
        $app = $this->getApp();

        $result = $app->components->process('<component src="bearcms-video-element" url="https://www.youtube.com/watch?v=Pwe-pA6TaZk" />');
        $this->requireNotEditable($result);
        $this->requireValidUrlHTML($result);

        $result = $app->components->process('<component src="bearcms-video-element" id="sample-element-1" editable="true" url="https://www.youtube.com/watch?v=Pwe-pA6TaZk" />');
        $this->requireNotEditable($result);
        $this->requireValidUrlHTML($result);
    }

    /**
     * 
     */
    public function testInvalidUrl()
    {
        $app = $this->getApp();

        $result = $app->components->process('<component src="bearcms-video-element" url="https://wrong.url/" />');
        $this->assertTrue(strpos($result, '<div></div></body>') !== false);
    }

}
