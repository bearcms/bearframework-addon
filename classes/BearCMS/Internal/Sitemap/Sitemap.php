<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Sitemap;

/**
 * 
 */
class Sitemap
{

    private $data = [];

    /**
     * 
     * @param string $locationPath
     * @param string|callable $lastModified
     * @param string $changeFrequency
     * @param float $priority
     * @return self Returns a reference to itself.
     */
    public function addItem(string $locationPath, $lastModified = null, ?string $changeFrequency = null, ?float $priority = null): self
    {
        $this->data[] = [
            'locationPath' => $locationPath,
            'lastModified' => $lastModified,
            'changeFrequency' => $changeFrequency,
            'priority' => $priority
        ];
        return $this;
    }

    /**
     * 
     * @return \BearFramework\DataList
     */
    public function getList(): \BearFramework\DataList
    {
        return new \BearFramework\DataList($this->data);
    }
}
