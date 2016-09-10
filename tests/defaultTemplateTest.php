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
class DefaultTemplateTest extends BearFrameworkAddonTestCase
{

    /**
     * 
     */
    public function testBlogPostsElement()
    {
        $app = $this->getApp();
        $context = $app->getContext(\BearFramework\Addons::getDir('bearcms/bearframework-addon') . '/index.php');
        $result = $app->components->process('<component src="file:' . $context->dir . '/components/bearcms-default-template-1.php" />');
        //echo $result;exit;
        //$this->assertTrue($settings['title'] === '');
    }

}
