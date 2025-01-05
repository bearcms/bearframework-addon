<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\ImportExport;

/**
 * @internal
 * @codeCoverageIgnore
 */
class ImportContext
{

    /**
     * 
     * @var string
     */
    private $mode = null;

    /**
     * 
     * @var array
     */
    private $changes = [];

    /**
     * 
     * @var callable|null
     */
    private $getValueCallback = null;

    /**
     * 
     * @var ImportContext|null
     */
    private $parentContext = null;

    /**
     * 
     * @param string $mode
     * @param callable|null $getValueCallback
     * @param ImportContext|null $parentContext
     */
    public function __construct(string $mode, ?callable $getValueCallback = null, ?ImportContext $parentContext = null)
    {
        $this->mode = $mode;
        $this->getValueCallback = $getValueCallback;
        $this->parentContext = $parentContext;
    }

    /**
     * 
     * @param callable $getValueCallback
     * @return ImportContext
     */
    public function makeGetValueContext(callable $getValueCallback): ImportContext
    {
        return new ImportContext($this->mode, function (string $key) use ($getValueCallback) {
            return call_user_func($getValueCallback, $key);
        }, $this);
    }

    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function getValue(string $key)
    {
        return call_user_func($this->getValueCallback, $key);
    }

    /**
     * 
     * @return boolean
     */
    public function isExecuteMode(): bool
    {
        return $this->mode === 'execute';
    }

    /**
     * 
     * @param string $type
     * @param mixed $data
     * @return self
     */
    public function logChange(string $type, $data): self
    {
        if ($this->parentContext !== null) {
            $this->parentContext->logChange($type, $data);
            return $this;
        }
        if (!isset($this->changes[$type])) {
            $this->changes[$type] = [];
        }
        $this->changes[$type][] = $data;
        return $this;
    }

    /**
     * 
     * @param string $text
     * @param mixed|null $data
     * @return self
     */
    public function logWarning(string $text, $data = null): self
    {
        return $this->logChange('_warnings', ['text' => $text, 'data' => $data]);
    }

    /**
     * 
     * @return array
     */
    public function getChanges(): array
    {
        if ($this->parentContext !== null) {
            return $this->parentContext->getChanges();
        }
        return $this->changes;
    }
}
