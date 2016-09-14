<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
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
        //echo $result;exit;
        //$this->assertTrue($settings['title'] === '');
    }

    /**
     * 
     */
    public function testBlogPostsElementComponent()
    {
        $app = $this->getApp();
        $result = $app->components->process('<component src="bearcms-blog-posts-element" />');
        //echo $result;exit;
        //$this->assertTrue($settings['title'] === '');
    }

    /**
     * 
     */
    public function testHeadingElementComponent()
    {
        $app = $this->getApp();
        $result = $app->components->process('<component src="bearcms-heading-element" />');
        //echo $result;exit;
        //$this->assertTrue($settings['title'] === '');
    }

    /**
     * 
     */
    public function testHTMLElementComponent()
    {
        $app = $this->getApp();
        $result = $app->components->process('<component src="bearcms-html-element" />');
        //echo $result;exit;
        //$this->assertTrue($settings['title'] === '');
    }

    /**
     * 
     */
    public function testImageElementComponent()
    {
        $app = $this->getApp();
        $result = $app->components->process('<component src="bearcms-image-element" />');
        //echo $result;exit;
        //$this->assertTrue($settings['title'] === '');
    }

    /**
     * 
     */
    public function testImageGalleryElementComponent()
    {
        $app = $this->getApp();
        $result = $app->components->process('<component src="bearcms-image-gallery-element" />');
        //echo $result;exit;
        //$this->assertTrue($settings['title'] === '');
    }

    /**
     * 
     */
    public function testLinkElementComponent()
    {
        $app = $this->getApp();
        $result = $app->components->process('<component src="bearcms-link-element" />');
        //echo $result;exit;
        //$this->assertTrue($settings['title'] === '');
    }

    /**
     * 
     */
    public function testNavigationElementComponent()
    {
        $app = $this->getApp();
        $result = $app->components->process('<component src="bearcms-navigation-element" />');
        //echo $result;exit;
        //$this->assertTrue($settings['title'] === '');
    }

    /**
     * 
     */
    public function testTextElementComponent()
    {
        $app = $this->getApp();

        $result = $app->components->process('<component src="bearcms-text-element" text="Hello" />');
        $expectedResult = '<!DOCTYPE html><html><head></head><body><div><div class="bearcms-text-element">Hello</div></div></body></html>';
        $this->assertTrue($result === $expectedResult);

        $text = 'This is a <a href="#">some html code</a>.';
        $result = $app->components->process('<component src="bearcms-text-element" text="' . htmlentities($text) . '" />');
        $expectedResult = '<!DOCTYPE html><html><head></head><body><div><div class="bearcms-text-element">' . $text . '</div></div></body></html>';
        $this->assertTrue($result === $expectedResult);
    }

}
