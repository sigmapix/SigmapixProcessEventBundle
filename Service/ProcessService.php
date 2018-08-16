<?php
declare(strict_types=1);

namespace Sigmapix\ProcessEventBundle\Service;

use Doctrine\ORM\EntityManager;
use Sigmapix\ProcessEventBundle\Entity\ProcessEntity;
use Sigmapix\ProcessEventBundle\Process\Process;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ProcessService
 * @package Sigmapix\ProcessEventBundle\Service
 */
class ProcessService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * ProcessService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    /**
     * @param Process $process
     * @throws \Doctrine\ORM\ORMException
     */
    public function enqueue(Process $process)
    {
        $processEntity = new ProcessEntity();
        $processEntity->setClassName(\get_class($process));
        $processEntity->setArgs($process->getArgs());
        $processEntity->setName($process->getName());
        $this->em->persist($processEntity);
        $this->em->flush();
    }
}
