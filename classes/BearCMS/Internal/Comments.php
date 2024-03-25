<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;
use BearCMS\Internal;
use BearCMS\Internal2;

/**
 * @internal
 * @codeCoverageIgnore
 */
class Comments
{

    /**
     *
     * @param array $data
     * @return string|null
     */
    public static function handleLoadMoreServerRequest(array $data): ?string
    {
        if (isset($data['serverData'], $data['count'])) {
            $app = App::get();
            $context = $app->contexts->get(__DIR__);
            $count = (int) $data['count'];
            $serverData = Internal\TempClientData::get($data['serverData']);
            if (is_array($serverData) && isset($serverData['threadID'])) {
                $threadID = $serverData['threadID'];
                $html = $app->components->process('<component src="file:' . $context->dir . '/components/bearcmsCommentsElement/commentsList.php" count="' . htmlentities($count) . '" threadID="' . htmlentities($threadID) . '" />');
                return json_encode(['html' => $html], JSON_THROW_ON_ERROR);
            }
        }
        return null;
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    public static function sendNewCommentNotification(array $data): void
    {
        $app = App::get();
        $threadID = $data['threadID'];
        $commentID = $data['commentID'];
        if ($app->bearCMS->hasEventListeners('internalBeforeSendNewCommentNotification')) {
            $eventDetails = new \BearCMS\Internal\BeforeSendNewCommentNotificationEventDetails($threadID, $commentID);
            $app->bearCMS->dispatchEvent('internalBeforeSendNewCommentNotification', $eventDetails);
            if ($eventDetails->cancel) {
                return;
            }
        }
        $comments = Internal2::$data2->comments->getList()
            ->filterBy('threadID', $threadID)
            ->filterBy('id', $commentID);
        if (isset($comments[0])) {
            $comment = $comments[0];
            $comments = Internal2::$data2->comments->getList()
                ->filterBy('status', 'pendingApproval');
            $pendingApprovalCount = $comments->count();
            $profile = Internal\PublicProfile::getFromAuthor($comment->author);
            Internal\Data::sendNotification('comments', $comment->status, $profile->name, $comment->text, $pendingApprovalCount);
        }
    }
}
