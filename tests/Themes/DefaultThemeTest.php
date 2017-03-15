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
class DefaultThemeTest extends BearFrameworkAddonTestCase
{

    /**
     * 
     */
    public function testBlogPostsElement()
    {
        $app = $this->getApp();
        $context = $app->context->get(\BearFramework\Addons::get('bearcms/bearframework-addon')['dir'] . '/index.php');
        $result = $app->components->process('<component src="file:' . $context->dir . '/themes/theme1/components/defaultTemplate.php" />');
        //echo $result;exit;
        //$this->assertTrue($settings['title'] === '');
    }

}
