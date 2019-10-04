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
class SeparatorElementTest extends BearCMSTestCase
{

    /**
     * 
     */
    public function testOutput()
    {
        $app = $this->getApp();

        $result = $app->components->process('<component src="bearcms-separator-element" size="large"/>');
        $this->assertTrue(strpos($result, 'class="bearcms-separator-element-large"></div>') !== false);

        $result = $app->components->process('<bearcms-separator-element size="large"/>');
        $this->assertTrue(strpos($result, 'class="bearcms-separator-element-large"></div>') !== false);

        $result = $app->components->process('<component src="bearcms-separator-element" size="medium"/>');
        $this->assertTrue(strpos($result, 'class="bearcms-separator-element-medium"></div>') !== false);

        $result = $app->components->process('<bearcms-separator-element size="medium"/>');
        $this->assertTrue(strpos($result, 'class="bearcms-separator-element-medium"></div>') !== false);

        $result = $app->components->process('<component src="bearcms-separator-element" size="small"/>');
        $this->assertTrue(strpos($result, 'class="bearcms-separator-element-small"></div>') !== false);

        $result = $app->components->process('<bearcms-separator-element size="small"/>');
        $this->assertTrue(strpos($result, 'class="bearcms-separator-element-small"></div>') !== false);
    }
}
