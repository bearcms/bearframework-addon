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
 * @property string $pageID
 */
class MakePageResponseEventDetails
{

    use \IvoPetkov\DataObjectTrait;

    /**
     * 
     * @param \BearFramework\App\Response $response
     * @param string $pageID
     */
    public function __construct(\BearFramework\App\Response $response, string $pageID)
    {
        $this
                ->defineProperty('response', [
                    'type' => \BearFramework\App\Response::class
                ])
                ->defineProperty('pageID', [
                    'type' => 'string'
                ])
        ;
        $this->response = $response;
        $this->pageID = $pageID;
    }

}
