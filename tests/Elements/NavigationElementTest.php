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
class NavigationElementTest extends BearCMSTestCase
{

    /**
     * 
     */
    public function testOutput()
    {
        $app = $this->getApp();

        $result = $app->components->process('<component src="bearcms-navigation-element"><ul><li><a href="#">Link 1</a></li></ul></component>');
        $this->assertTrue(strpos($result, '<a href="#">Link 1</a>') !== false);
        
        $result = $app->components->process('<bearcms-navigation-element><ul><li><a href="#">Link 1</a></li></ul></bearcms-navigation-element>');
        $this->assertTrue(strpos($result, '<a href="#">Link 1</a>') !== false);
    }

}
