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
class ForumPostsElementTest extends BearCMSTestCase
{

    /**
     * 
     */
    public function testOutput()
    {
        $app = $this->getApp();
        $result = $app->components->process('<component src="bearcms-forum-posts-element"/>');
        $this->assertTrue(strpos($result, 'class="bearcms-forum-posts-element"') !== false);
    }

}
