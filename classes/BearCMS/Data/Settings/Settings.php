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
 * @property array $languages
 * @property string|null $icon Will be removed in v2
 * @property array $icons
 * @property boolean $externalLinks
 * @property boolean $allowSearchEngines
 * @property boolean $allowCommentsInBlogPosts
 * @property boolean $showRelatedBlogPosts
 * @property boolean $disabled
 * @property string|null $disabledText
 * @property boolean $enableRSS
 * @property string|null $rssType
 * @property array $translations
 * @property string|null $globalHTML
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
                'init' => function () {
                    return 'en';
                }
            ])
            ->defineProperty('languages', [
                'type' => 'array',
            ])
            ->defineProperty('icon', [
                'type' => '?string'
            ])
            ->defineProperty('icons', [
                'type' => 'array'
            ])
            ->defineProperty('externalLinks', [
                'type' => 'bool',
                'init' => function () {
                    return false;
                }
            ])
            ->defineProperty('allowSearchEngines', [
                'type' => 'bool',
                'init' => function () {
                    return false;
                }
            ])
            ->defineProperty('allowCommentsInBlogPosts', [
                'type' => 'bool',
                'init' => function () {
                    return false;
                }
            ])
            ->defineProperty('showRelatedBlogPosts', [
                'type' => 'bool',
                'init' => function () {
                    return false;
                }
            ])
            ->defineProperty('disabled', [
                'type' => 'bool',
                'init' => function () {
                    return false;
                }
            ])
            ->defineProperty('disabledText', [
                'type' => '?string'
            ])
            ->defineProperty('enableRSS', [
                'type' => 'bool',
                'init' => function () {
                    return true;
                }
            ])
            ->defineProperty('rssType', [
                'type' => '?string',
                'init' => function () {
                    return 'contentSummary';
                }
            ])
            ->defineProperty('translations', [
                'type' => 'array',
            ])
            ->defineProperty('globalHTML', [
                'type' => '?string'
            ]);
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
        if (!isset($data['languages'])) {
            $data['languages'] = [];
        }
        if (isset($data['language']) && strlen($data['language']) > 0) {
            if (empty($data['languages'])) {
                $data['languages'][] = $data['language'];
            }
        }
        if (!empty($data['languages']) && (!isset($data['language']) || strlen($data['language']) === 0)) {
            $data['language'] = $data['languages'][0];
        }
        return parent::fromArray($data);
    }

    public function getTitle(string $language)
    {
        return $this->getPropertyForLanguage($language, 'title');
    }

    public function getDescription(string $language)
    {
        return $this->getPropertyForLanguage($language, 'description');
    }

    private function getPropertyForLanguage(string $language, string $property)
    {
        if (strlen($language) > 0 && isset($this->translations[$language], $this->translations[$language][$property])) {
            return $this->translations[$language][$property];
        }
        return $this->$property;
    }
}
