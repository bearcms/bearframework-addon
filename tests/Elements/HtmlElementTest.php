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
class HTMLElementTest extends BearCMSTestCase
{

    /**
     * 
     */
    public function testOutput()
    {
        $app = $this->getApp();

        $code = 'This is a <a href="#">some html code</a>.';
        $result = $app->components->process('<component src="bearcms-html-element" code="' . htmlentities($code) . '"/>');
        $this->assertTrue(strpos($result, $code) !== false);
    }

}
