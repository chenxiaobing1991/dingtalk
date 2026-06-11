<?php


namespace Cxb\DingTalk\Event;

/**
 * Class BaseEvent
 * @package Cxb\DingTalk\Event
 */
abstract class BaseEvent
{
    /**
     * BaseEvent constructor.
     * @param object $model
     * @param bool $propagationStopped
     */
    public function __construct(protected object $model, private bool $propagationStopped = false)
    {

    }

    /**
     * @return object
     */
    final public function getModel(): object
    {
        return $this->model;
    }

    /**
     *终止后续标识
     */
    final public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }

    /**
     * @return bool
     */
    final public function isStopPropagation(): bool
    {
        return $this->propagationStopped;
    }
}