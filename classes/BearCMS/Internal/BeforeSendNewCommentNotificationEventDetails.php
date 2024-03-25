<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

/**
 * @property string $threadID
 * @property string $commentID
 * @property bool $cancel
 * @internal
 * @codeCoverageIgnore
 */
class BeforeSendNewCommentNotificationEventDetails
{

    use \IvoPetkov\DataObjectTrait;

    /**
     * 
     * @param string $threadID
     * @param string $commentID
     */
    public function __construct(string $threadID, string $commentID)
    {
        $this
            ->defineProperty('threadID', [
                'type' => 'string'
            ])
            ->defineProperty('commentID', [
                'type' => 'string'
            ])
            ->defineProperty('cancel', [
                'type' => 'bool',
                'init' => function () {
                    return false;
                }
            ]);
        $this->threadID = $threadID;
        $this->commentID = $commentID;
    }
}
