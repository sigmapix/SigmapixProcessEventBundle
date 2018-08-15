<?php

namespace Sigmapix\ProcessEventBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use Sigmapix\ProcessEventBundle\Entity\ProcessEntity;
use Sigmapix\ProcessEventBundle\Process\Process;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * TODO : no more --id, should be moved in crontab and will execute all executable Processes
 * Class ProcessCommand
 * @package Sigmapix\ProcessEventBundle\Command
 */
class ProcessCommand extends ContainerAwareCommand
{
    const NAME = 'sigmapix:sonata:process';
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
    private $processRepo;

    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription('This command handles a given Process id using Symfony Process feature. Default behavior is Asynchronous.') // TODO update
            ->addOption( // TODO remove
                'id',
                'id',
                InputOption::VALUE_REQUIRED,
                'Process id to execute (required)'
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
        $this->processRepo = $this->em->getRepository(ProcessEntity::class);
        $this->out = $output;
        $this->jobId = substr(sha1(uniqid(mt_rand(), true)), 0, 10);
        // TODO adapt
        $this->log('*** starting ***', true);
        $processEntityId = $input->getOption('id');
        if ($processEntityId !== null && is_numeric($processEntityId)) {
            $this->log('** handling process id=' . $processEntityId, true);
            /** @var ProcessEntity $processEntity */
            $processEntity = $this->processRepo->find((int)$processEntityId);
            if ($processEntity) {
                $className = $processEntity->getClassName();
                /** @var Process $process */
                $process = new $className($processEntity->getArgs());
                $process->setContainer($this->getContainer());
                $process->run($processEntity);
                $this->log('  > run ended');
            } else {
                $this->log('!! ERROR: no process found with given identifier');
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
