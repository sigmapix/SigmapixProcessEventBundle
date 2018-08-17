<?php
declare(strict_types=1);

namespace Sigmapix\ProcessEventBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Sigmapix\ProcessEventBundle\Entity\ProcessEntity;
use Sigmapix\ProcessEventBundle\Process\Process;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
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
            ->setDescription('This command handles persisted Processes with status set to ProcessEntity::NEW.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $this->processRepo = $this->em->getRepository(ProcessEntity::class);
        $this->out = $output;
        $this->jobId = substr(sha1(uniqid((string)mt_rand(), true)), 0, 10);

        $this->log('*** starting ***', true);
        /** @var ProcessEntity $processEntity */
        $processEntity = $this->processRepo->findOneBy(['status' => ProcessEntity::NEW]);
        if ($processEntity && $processEntity instanceof ProcessEntity) {
            // Immediately locking Process
            $this->log('** locking process id=' . $processEntity->getId(), true);
            $processEntity->setStatus(ProcessEntity::RUNNING);
            $processEntity->setErrorOutput(''); // clearprevious errors
            $processEntity->setOutput(''); // clear previous output
            $processEntity->setProgress(0); // reset progression
            $this->em->flush();
            $this->log('** handling process id=' . $processEntity->getId(), true);
            $className = $processEntity->getClassName();
            try {
                /** @var Process $process */
                $process = new $className($processEntity->getArgs());
                $process->setContainer($this->getContainer());
                ob_start();
                $value = $process->run($processEntity);
                $processEntity->setProgress(100);
                $processEntity->setStatus($value ? ProcessEntity::SUCCEED : ProcessEntity::FAILED);
            } catch (\Exception $e) {
                $processEntity->setErrorOutput($e->getMessage());
                $processEntity->setStatus(ProcessEntity::FAILED);
            } finally {
                $stdout = ob_get_contents();
                $processEntity->setOutput($stdout);
                $this->em->flush();
                ob_end_flush();
                $this->log('  > run ended' . PHP_EOL);
            }
        }
        $this->log('*** finished **');
    }

    /**
     * Logger function that prefix all log messages with jobId and datetime
     *
     * @param string $message
     * @param bool $newline
     */
    private function log(string $message = null, bool $newline = null)
    {
        $prefix = '[job:' . $this->jobId . '] (' . (new \DateTime())->format('d-m-Y H:i:s') . ') ';
        if ($newline === true) {
            $this->out->writeln($prefix);
        }
        $this->out->writeln($prefix . $message);
    }
}
