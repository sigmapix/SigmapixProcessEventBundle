<?php

namespace Sigmapix\ProcessEventBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use Sigmapix\ProcessEventBundle\Entity\ProcessEventEntity;
use Sigmapix\ProcessEventBundle\Event\ProcessEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ProcessEventCommand
 * @package Sigmapix\ProcessEventBundle\Command
 */
class ProcessEventCommand extends ContainerAwareCommand
{
    const NAME = 'process:event:execute';
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var OutputInterface
     */
    private $out;

    /**
     * @var string
     */
    private $jobId;

    /**
     * @var EntityRepository
     */
    private $processEventRepo;

    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription('This command handles a given ProcessEvent id using Symfony Process feature. Default behavior is Asynchronous.')
            ->addOption(
                'id',
                'id',
                InputOption::VALUE_REQUIRED,
                'ProcessEvent id to execute (required)'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws ORMException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $this->processEventRepo = $this->em->getRepository(ProcessEventEntity::class);
        $this->out = $output;
        $this->jobId = substr(sha1(uniqid(mt_rand(), true)), 0, 10);

        $this->log('*** starting ***', true);
        $processEventEntityId = $input->getOption('id');
        if ($processEventEntityId !== null && is_numeric($processEventEntityId)) {
            $this->log('** handling processEvent id=' . $processEventEntityId, true);
            /** @var ProcessEventEntity $processEventEntity */
            $processEventEntity = $this->processEventRepo->find((int)$processEventEntityId);
            if ($processEventEntity) {
                $className = $processEventEntity->getClassName();
                /** @var ProcessEvent $processEvent */
                $processEvent = new $className($processEventEntity->getArgs());
                $processEvent->setContainer($this->getContainer());
                $processEvent->run($processEventEntity);
                $this->log('  > run ended');
            } else {
                $this->log('!! ERROR: no processEvent found with given identifier');
            }
            $this->log();
        } else {
            $this->log('!! ERROR: given options are invalid');
        }
        $this->log('*** finished **');
    }

    /**
     * Logger function that prefix all log messages with jobId and datetime
     *
     * @param string $message
     * @param bool $newline
     */
    private function log($message = '', $newline = false)
    {
        $prefix = '[job:' . $this->jobId . '] (' . (new \DateTime())->format('d-m-Y H:i:s') . ') ';
        if ($newline === true) {
            $this->out->writeln($prefix);
        }
        $this->out->writeln($prefix . $message);
    }
}
