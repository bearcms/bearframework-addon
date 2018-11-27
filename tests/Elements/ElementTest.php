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
class ElementTest extends BearCMSTestCase
{

    /**
     * 
     * @param string $html
     */
    private function assertEditableElement(string $html)
    {
        $app = $this->getApp();
        $this->createAndLoginUser();

        $result = $app->components->process($html);
        $this->assertTrue(strpos($result, '<div id="brelc') !== false);
    }

    /**
     * 
     * @param string $html
     */
    private function assertNotEditableElement(string $html)
    {
        $app = $this->getApp();
        $this->logoutUser();

        $result = $app->components->process($html);
        if (!(strpos($result, '<div id="brelc') === false)) {
            echo $result . "\n\n";
            exit;
        }
        $this->assertTrue(strpos($result, '<div id="brelc') === false);
    }

    /**
     * 
     */
    public function testElementEditable()
    {
        $this->assertEditableElement('<component src="bearcms-blog-posts-element" id="sample-element-1" editable="true"/>');
        $this->assertNotEditableElement('<component src="bearcms-blog-posts-element"/>');
        $this->assertNotEditableElement('<component src="bearcms-blog-posts-element" id="sample-element-1" editable="true"/>');
    }

}
