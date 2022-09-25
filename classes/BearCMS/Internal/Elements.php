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

/**
 * @internal
 * @codeCoverageIgnore
 */
class Elements
{

    /**
     *
     * @param array $data
     * @return string|null
     */
    public static function handleLoadMoreServerRequest(array $data): ?string
    {
        if (isset($data['serverData'])) {
            $app = App::get();
            $serverData = Internal\TempClientData::get($data['serverData']);
            if (is_array($serverData) && isset($serverData['componentHTML'])) {
                $content = $app->components->process($serverData['componentHTML']);
                $content = $app->clientPackages->process($content);
                $editorContent = Internal\ElementsHelper::getEditableElementsHTML();
                return json_encode([
                    'content' => $content,
                    'editorContent' => (isset($editorContent[0]) ? $editorContent : ''),
                    'nextLazyLoadData' => (string) Internal\ElementsHelper::$lastLoadMoreServerData
                ], JSON_THROW_ON_ERROR);
            }
        }
        return null;
    }
}
