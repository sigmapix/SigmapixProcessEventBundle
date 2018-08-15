<?php

namespace Sigmapix\ProcessEventBundle\Process;

use Sigmapix\ProcessEventBundle\Entity\ProcessEntity;

/**
 * Interface ProcessInterface
 * @package Sigmapix\ProcessEventBundle\Process
 */
interface ProcessInterface
{
    /**
     * @param ProcessEntity $processEntity
     * @return bool
     */
    public function run(ProcessEntity $processEntity);

    /**
     * @return string
     */
    public function getName();
}