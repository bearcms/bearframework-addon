<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Themes\Theme;

use BearCMS\Internal\Themes;
use BearCMS\Internal;
use IvoPetkov\HTML5DOMDocument;

/**
 * 
 */
class Customizations
{

    /**
     * Options values
     * 
     * @var array 
     */
    private $values = [];

    /**
     *
     * @var string 
     */
    private $html = '';

    /**
     * 
     * @param array
     */
    private $details = [];

    /**
     * 
     * @param array $values
     * @param string $html
     * @param array $details
     */
    public function __construct(array $values = [], string $html = '', array $details = [])
    {
        $this->values = $values;
        $this->html = $html;
        $this->details = $details;
    }

    /**
     * 
     * @param string $name
     * @return mixed|null
     */
    public function getValue(string $name)
    {
        return isset($this->values[$name]) ? $this->values[$name] : null;
    }

    /**
     * 
     * @param string $name
     * @param array $details Available values: defaultValue, states, responsiveAttributes=>callback
     * @return array
     */
    public function getValueDetails(string $name, array $details = []): array
    {
        $valueDetails = Themes::getValueDetails($this->getValue($name));
        foreach ($details as $detail) {
            if ($detail === 'defaultValue') {
                $result[$detail] = $valueDetails['value'];
            } else if ($detail === 'states') {
                $states = [];
                foreach ($valueDetails['states'] as $stateData) {
                    $states[] = ['name' => $stateData[0], 'value' => $stateData[1]];
                }
                $result[$detail] = $states;
            }
        }
        return $result;
    }

    /**
     * 
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * 
     * @return string
     */
    public function getHTML(): string
    {
        return $this->html;
    }

    /**
     * 
     * @param string $key
     * @param array $details Available values: filename, width, height
     * @return array
     */
    public function getAssetDetails(string $key, array $details = []): array
    {
        $result = [];
        foreach ($details as $detail) {
            if ($detail === 'filename') {
                $result[$detail] = Internal\Data::getRealFilename($key);
            } else {
                $result[$detail] = isset($this->details['assets'], $this->details['assets'][$key], $this->details['assets'][$key][$detail]) ? $this->details['assets'][$key][$detail] : null;
            }
        }
        return $result;
    }

    /**
     * 
     * @param string $content
     * @return string
     */
    public function apply(string $content): string
    {
        $htmlData = [
            'html' => $this->html,
            'details' => $this->details
        ];
        $html = Internal\Themes::processOptionsHTMLData($htmlData);
        $document = new HTML5DOMDocument();
        $document->loadHTML($content, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
        $document->insertHTML($html);
        return $document->saveHTML();
    }
}
