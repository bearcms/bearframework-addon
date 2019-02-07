<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

/**
 * @property \BearFramework\App\Response $response
 * @property string $blogPostID
 */
class MakeBlogPostPageResponseEventDetails
{

    use \IvoPetkov\DataObjectTrait;

    /**
     * 
     * @param \BearFramework\App\Response $response
     * @param string $blogPostID
     */
    public function __construct(\BearFramework\App\Response $response, string $blogPostID)
    {
        $this
                ->defineProperty('response', [
                    'type' => \BearFramework\App\Response::class
                ])
                ->defineProperty('blogPostID', [
                    'type' => 'string'
                ])
        ;
        $this->response = $response;
        $this->blogPostID = $blogPostID;
    }

}
