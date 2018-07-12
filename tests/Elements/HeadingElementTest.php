<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

/**
 * @runTestsInSeparateProcesses
 */
class HeadingElementTest extends BearCMSTestCase
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

        $result = $app->components->process('<component src="bearcms-heading-element" id="sample-element-1" editable="true"/>');
        $this->requireEditable($result);
    }

    /**
     * 
     */
    public function testNotEditable()
    {
        $app = $this->getApp();

        $result = $app->components->process('<component src="bearcms-heading-element"/>');
        $this->requireNotEditable($result);

        $result = $app->components->process('<component src="bearcms-heading-element" id="sample-element-1" editable="true"/>');
        $this->requireNotEditable($result);
    }

    /**
     * 
     */
    public function testOutput()
    {
        $app = $this->getApp();

        $text = 'Hello';

        $result = $app->components->process('<component src="bearcms-heading-element" text="' . htmlentities($text) . '" size="large"/>');
        $this->assertTrue(strpos($result, '>' . $text . '</h1>') !== false);

        $result = $app->components->process('<component src="bearcms-heading-element" text="' . htmlentities($text) . '" size="medium"/>');
        $this->assertTrue(strpos($result, '>' . $text . '</h2>') !== false);

        $result = $app->components->process('<component src="bearcms-heading-element" text="' . htmlentities($text) . '" size="small"/>');
        $this->assertTrue(strpos($result, '>' . $text . '</h3>') !== false);
    }

}
