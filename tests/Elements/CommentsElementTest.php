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
class CommentsElementTest extends BearCMSTestCase
{

    /**
     * 
     */
    public function testOutput()
    {
        $app = $this->getApp();
        $result = $app->components->process('<component src="bearcms-comments-element" threadID="123"/>');
        $this->assertTrue(strpos($result, 'class="bearcms-comments-element"') !== false);
    }

}
