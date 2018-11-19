<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;

final class PublicProfile
{

    static function getFromAuthor($author)
    {
        $data = [];
        if (isset($author['type']) && $author['type'] === 'user') {
            $app = App::get();
            $user = $app->users->getUser($author['provider'], $author['id']);
            $data['name'] = $user->name;
            $data['url'] = $user->url;
            $data['imageSmall'] = $user->getImageUrl(200);
            $data['imageLarge'] = $user->getImageUrl(1000);
        }
        if (!isset($data['name']) || strlen($data['name']) === 0) {
            $data['name'] = 'Anonymous';
        }
        if (!isset($data['url'])) {
            $data['url'] = '';
        }
        if (!isset($data['imageSmall']) || strlen($data['imageSmall']) === 0) {
            $data['imageSmall'] = '';
        }
        if (!isset($data['imageLarge']) || strlen($data['imageLarge']) === 0) {
            $data['imageLarge'] = '';
        }
        return new \BearCMS\DataObject($data);
    }

}
