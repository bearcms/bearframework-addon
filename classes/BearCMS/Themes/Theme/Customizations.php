<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Themes\Theme;

use BearCMS\Internal2;

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
     * @param array $values
     * @param string $html
     * @param array $assetsDetails
     */
    private $assetsDetails = [];

    /**
     * 
     * @param array $values
     * @param string $html
     * @param array $assetsDetails
     */
    public function __construct(array $values = [], string $html = '', array $assetsDetails = [])
    {
        $this->values = $values;
        $this->html = $html;
        $this->assetsDetails = $assetsDetails;
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
     * @param array $details
     * @return array
     */
    public function getAssetDetails(string $key, array $details = []): array
    {
        $result = [];
        foreach ($details as $detail) {
            if ($detail === 'filename') {
                $result[$detail] = Internal\Data::getRealFilename($key);
            } else {
                $result[$detail] = isset($this->assetsDetails[$key], $this->assetsDetails[$key][$detail]) ? $this->assetsDetails[$key][$detail] : null;
            }
        }
        return $result;
    }
}
