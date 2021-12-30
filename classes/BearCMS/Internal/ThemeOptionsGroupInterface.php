<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

/**
 * @internal
 * @codeCoverageIgnore
 */
interface ThemeOptionsGroupInterface
{
    public function add($optionOrGroup);

    public function addOption(string $id, string $type, string $name, array $details = []);

    public function addGroup(string $name, string $description = '', array $details = []);

    public function addElementsGroup(string $idPrefix, string $parentSelector);

    public function addElements(string $idPrefix, string $parentSelector);

    public function addPagesGroup();

    public function addPages();

    public function addCustomCSS(string $id = 'customCSS');

    public function getList();
}
