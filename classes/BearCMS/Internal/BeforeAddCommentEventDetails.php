<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

/**
 * @property array $author
 * @property string $text
 * @property array $files
 * @property string $status
 * @property bool $cancel
 * @property string $cancelMessage
 * @internal
 * @codeCoverageIgnore
 */
class BeforeAddCommentEventDetails
{

    use \IvoPetkov\DataObjectTrait;

    /**
     * 
     * @param array $author
     * @param string $text
     * @param string $status
     * @param array $files
     */
    public function __construct(array $author, string $text, string $status, array $files = [])
    {
        $this
            ->defineProperty('author', [
                'type' => 'array'
            ])
            ->defineProperty('text', [
                'type' => 'string'
            ])
            ->defineProperty('files', [
                'type' => 'array'
            ])
            ->defineProperty('status', [
                'type' => 'string'
            ])
            ->defineProperty('cancel', [
                'type' => 'bool',
                'init' => function () {
                    return false;
                }
            ])
            ->defineProperty('cancelMessage', [
                'type' => 'string'
            ]);
        $this->author = $author;
        $this->text = $text;
        $this->files = $files;
        $this->status = $status;
    }
}
