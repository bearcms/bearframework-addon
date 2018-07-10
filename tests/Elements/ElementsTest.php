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
class ElementsTest extends BearFrameworkAddonTestCase
{

    /**
     * 
     */
    public function testElementsComponent()
    {
        $app = $this->getApp();
        $result = $app->components->process('<component src="bearcms-elements" id="dummy" />');
        $this->assertTrue(strpos($result, '<div class="bearcms-elements') !== false);
    }

}
