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
class HeadingElementTest extends BearCMSTestCase
{

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
