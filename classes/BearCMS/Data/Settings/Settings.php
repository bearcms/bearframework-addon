<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data\Settings;

/**
 * 
 * @property string|null $title
 * @property string|null $description
 * @property string|null $pageTitleFormat
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
 * @property array $redirects
 * @property array $fonts
 */
class Settings extends \BearFramework\Models\Model
{

    /**
     *
     */
    function __construct()
    {
        $this
            ->defineProperty('title', [
                'type' => '?string'
            ])
            ->defineProperty('description', [
                'type' => '?string'
            ])
            ->defineProperty('pageTitleFormat', [
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
            ])
            ->defineProperty('redirects', [
                'type' => 'array',
            ])
            ->defineProperty('fonts', [
                'type' => 'array'
            ]);
    }

    /**
     * 
     * @param array $data
     * @return self
     */
    static function fromArray(array $data): self
    {
        if (!isset($data['icons'])) {
            $data['icons'] = [];
        }
        if (isset($data['icon']) && strlen($data['icon']) > 0) {
            $icon = \BearCMS\Internal\Data::getRealFilename($data['icon'], true);
            if ($icon !== null) {
                $data['icon'] = $icon;
            }
            if (empty($data['icons'])) {
                $data['icons'][] = ['filename' => $data['icon']];
            }
        }
        if (!isset($data['languages'])) {
            $data['languages'] = [];
            if (!isset($data['language'])) {
                $data['language'] = 'en';
            }
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

    /**
     * 
     * @param string $language
     * @return string|null
     */
    public function getTitle(string $language): ?string
    {
        return $this->getPropertyForLanguage($language, 'title');
    }

    /**
     * 
     * @param string $language
     * @return string|null
     */
    public function getDescription(string $language): ?string
    {
        return $this->getPropertyForLanguage($language, 'description');
    }

    /**
     * 
     * @param string $language
     * @return string|null
     */
    public function getPageTitleFormat(string $language): ?string
    {
        return $this->getPropertyForLanguage($language, 'pageTitleFormat');
    }

    /**
     * 
     * @param string $language
     * @param string $property
     * @return string|null
     */
    private function getPropertyForLanguage(string $language, string $property): ?string
    {
        if (strlen($language) > 0 && isset($this->translations[$language], $this->translations[$language][$property])) {
            return $this->translations[$language][$property];
        }
        return $this->$property;
    }
}
