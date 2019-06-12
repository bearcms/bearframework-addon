<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data\Settings;

use BearCMS\Internal2;

/**
 * 
 * @property string|null $title
 * @property string|null $description
 * @property string|null $keywords
 * @property string|null $language
 * @property string|null $icon Will be removed in v2
 * @property array $icons
 * @property boolean $externalLinks
 * @property boolean $allowSearchEngines
 * @property boolean $allowCommentsInBlogPosts
 * @property boolean $disabled
 * @property string|null $disabledText
 * @property boolean $enableRSS
 * @property string|null $rssType
 */
class Settings extends \BearFramework\Models\Model
{

    function __construct()
    {
        $this
                ->defineProperty('title', [
                    'type' => '?string'
                ])
                ->defineProperty('description', [
                    'type' => '?string'
                ])
                ->defineProperty('keywords', [
                    'type' => '?string'
                ])
                ->defineProperty('language', [
                    'type' => '?string',
                    'init' => function() {
                        return 'en';
                    }
                ])
                ->defineProperty('icon', [
                    'type' => '?string'
                ])
                ->defineProperty('icons', [
                    'type' => 'array'
                ])
                ->defineProperty('externalLinks', [
                    'type' => 'bool',
                    'init' => function() {
                        return false;
                    }
                ])
                ->defineProperty('allowSearchEngines', [
                    'type' => 'bool',
                    'init' => function() {
                        return false;
                    }
                ])
                ->defineProperty('allowCommentsInBlogPosts', [
                    'type' => 'bool',
                    'init' => function() {
                        return false;
                    }
                ])
                ->defineProperty('disabled', [
                    'type' => 'bool',
                    'init' => function() {
                        return false;
                    }
                ])
                ->defineProperty('disabledText', [
                    'type' => '?string'
                ])
                ->defineProperty('enableRSS', [
                    'type' => 'bool',
                    'init' => function() {
                        return true;
                    }
                ])
                ->defineProperty('rssType', [
                    'type' => '?string',
                    'init' => function() {
                        return 'contentSummary';
                    }
                ])
        ;
    }

    static function fromArray(array $data)
    {
        if (!isset($data['icons'])) {
            $data['icons'] = [];
        }
        if (isset($data['icon']) && strlen($data['icon']) > 0) {
            $icon = Internal2::$data2->getRealFilename($data['icon']);
            if ($icon !== null) {
                $data['icon'] = $icon;
            }
            if (empty($data['icons'])) {
                $data['icons'][] = ['filename' => $data['icon']];
            }
        }
        return parent::fromArray($data);
    }

}
