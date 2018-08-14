<?php

namespace Sigmapix\ProcessEventBundle\Event;

use Sigmapix\ProcessEventBundle\Entity\ProcessEventEntity;

/**
 * Interface ProcessEventInterface
 * @package Sigmapix\ProcessEventBundle\Event
 */
interface ProcessEventInterface
{
    const NEW = 'newProcessEvent';

    /**
     * @param ProcessEventEntity $processEventEntity
     * @return bool
     */
    public function run(ProcessEventEntity $processEventEntity);

    /**
     * @return string
     */
    public function getName();
}