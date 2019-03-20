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
class VideoElementTest extends BearCMSTestCase
{

    /**
     * 
     */
    public function testOutput()
    {
        $app = $this->getApp();

        $result = $app->components->process('<component src="bearcms-video-element" url="https://www.youtube.com/watch?v=Pwe-pA6TaZk" />');
        $this->assertTrue(strpos($result, 'www.youtube.com/embed/Pwe-pA6TaZk') !== false);
        $this->assertTrue(strpos($result, '<div class="bearcms-video-element"') !== false);

        $result = $app->components->process('<bearcms-video-element url="https://www.youtube.com/watch?v=Pwe-pA6TaZk" />');
        $this->assertTrue(strpos($result, 'www.youtube.com/embed/Pwe-pA6TaZk') !== false);
        $this->assertTrue(strpos($result, '<div class="bearcms-video-element"') !== false);

//        $app->assets->addDir($app->config->appDir . '/assets/');
//        $this->makeFile($app->config->appDir . '/assets/file1.mp4', 'content');
//        $result = $app->components->process('<component src="bearcms-video-element" filename="app:assets/file1.mp4" />');
//        $this->assertTrue(strpos($result, 'file1.mp4') !== false);
//        $this->assertTrue(strpos($result, '<div class="bearcms-video-element"') !== false);

        $result = $app->components->process('<component src="bearcms-video-element" url="https://wrong.url/" />');
        $this->assertTrue(strpos($result, '<div class="bearcms-elements-element-container"></div></body>') !== false);
    }

}
