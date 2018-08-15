<?php

namespace Sigmapix\ProcessEventBundle\Service;

use Doctrine\ORM\EntityManager;
use Sigmapix\ProcessEventBundle\Command\ProcessCommand;
use Sigmapix\ProcessEventBundle\Entity\ProcessEntity;
use Sigmapix\ProcessEventBundle\Process\Process;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process as SymfonyProcess;

/**
 * Class ProcessService
 * @package Sigmapix\ProcessEventBundle\Service
 */
class ProcessService
{
    const TIMEOUT = 86400; // 1 day
    const TIMEOUT_CHECK_PERIOD = 200000; // microseconds

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EntityManager
     */
    private $em;

    /*
     * @var string
     */
    private $php;

    /**
     * ProcessService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $phpBinaryFinder = new PhpExecutableFinder();
        $this->php = $phpBinaryFinder->find();
        $this->em = $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * @param Process $process
     * @throws \Doctrine\ORM\ORMException
     */
    public function enqueue(Process $process)
    {
        $processEntity = new ProcessEntity();
        $processEntity->setClassName(get_class($process));
        $processEntity->setArgs($process->getArgs());
        $processEntity->setName($process->getName());
        $this->em->persist($processEntity);
        $this->em->flush();

        // TODO TEMP, shouldn't use symfony Processes anymore :'-(
        {
            $symfonyProcess = new SymfonyProcess([$this->php, '-d memory_limit=2G', 'bin/console', ProcessCommand::NAME, '--id', $processEntity->getId()]);
            $symfonyProcess->setEnv(['prod']);
            $symfonyProcess->setWorkingDirectory($this->container->get('kernel')->getRootDir() . '/../');
            $symfonyProcess->setTimeout(self::TIMEOUT);
            try {
                // Starting process
                $symfonyProcess->start();
                $symfonyProcess->wait();
            } catch (RuntimeException $exception) {
                $processEntity->setErrorOutput($symfonyProcess->getErrorOutput() . PHP_EOL . PHP_EOL . $exception->getMessage());
                $processEntity->setStatus(ProcessEntity::FAILED);
            }
            // Updating command and pid
            $processEntity->setOutput($symfonyProcess->getOutput());
            $processEntity->setCommand($symfonyProcess->getCommandLine());
            $processEntity->setPid($symfonyProcess->getPid());
        }
        $this->em->flush();
    }
}
