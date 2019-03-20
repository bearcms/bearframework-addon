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
class TextElementTest extends BearCMSTestCase
{

    /**
     * 
     */
    public function testOutput()
    {
        $app = $this->getApp();

        $text = 'This is a <a href="#">some html code</a>.';
        
        $result = $app->components->process('<component src="bearcms-text-element" text="' . htmlentities($text) . '"/>');
        $this->assertTrue(strpos($result, $text) !== false);
        
        $result = $app->components->process('<bearcms-text-element text="' . htmlentities($text) . '"/>');
        $this->assertTrue(strpos($result, $text) !== false);
    }

}
