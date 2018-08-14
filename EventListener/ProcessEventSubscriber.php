<?php

namespace Sigmapix\ProcessEventBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Sigmapix\ProcessEventBundle\Command\ProcessEventCommand;
use Sigmapix\ProcessEventBundle\Entity\ProcessEventEntity;
use Sigmapix\ProcessEventBundle\Event\ProcessEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Class ProcessEventSubscriber
 * @package Sigmapix\ProcessEventBundle\EventListener
 */
class ProcessEventSubscriber implements EventSubscriberInterface
{
    const TIMEOUT = 86400; // 1 day
    const TIMEOUT_CHECK_PERIOD = 200000; // microseconds
    // TODO handle limit max async processes (const MAX_PROCESSES = 4;)

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
     * ProcessEventSubscriber constructor.
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
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ProcessEvent::NEW => ProcessEvent::NEW,
        ];
    }

    /**
     * @param Event $event
     * @throws \Doctrine\ORM\ORMException
     */
    public function newProcessEvent(ProcessEvent $event)
    {
        $processEventEntity = new ProcessEventEntity();
        $processEventEntity->setClassName(get_class($event));
        $processEventEntity->setArgs($event->getArgs());
        $processEventEntity->setName($event->getName());
        $this->em->persist($processEventEntity);
        $this->em->flush();

        $process = new Process([$this->php, '-d memory_limit=2G', 'bin/console', ProcessEventCommand::NAME, '--id', $processEventEntity->getId()]);
        $process->setEnv(['prod']);
        $process->setWorkingDirectory($this->container->get('kernel')->getRootDir() . '/../');
        $process->setTimeout(self::TIMEOUT);

        try {
            // Starting process
            $process->start();
        } catch (RuntimeException $exception) {
            $processEventEntity->setErrorOutput($process->getErrorOutput().PHP_EOL.PHP_EOL.$exception->getMessage());
            $processEventEntity->setStatus(ProcessEventEntity::FAILED);
        }
        // Updating command and pid
        $processEventEntity->setOutput($process->getOutput());
        $processEventEntity->setCommand($process->getCommandLine());
        $processEventEntity->setPid($process->getPid());
        $this->em->flush();
    }
}
