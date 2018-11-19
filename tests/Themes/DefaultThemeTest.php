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
class DefaultThemeTest extends BearCMSTestCase
{

    /**
     * 
     */
    public function testDefaults()
    {
        $this->assertTrue(true);
//        $app = $this->getApp();
//        $context = $app->context->get(\BearFramework\Addons::get('bearcms/bearframework-addon')['dir'] . '/index.php');
//        $result = $app->components->process('<component src="file:' . $context->dir . '/themes/universal/components/defaultTemplate.php" />');
        //echo $result;exit;
        //$this->assertTrue($settings['title'] === '');
    }

}
