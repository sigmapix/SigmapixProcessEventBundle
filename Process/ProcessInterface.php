<?php
declare(strict_types=1);

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
    public function run(ProcessEntity $processEntity): bool;

    /**
     * @return string
     */
    public function getName(): string;
}