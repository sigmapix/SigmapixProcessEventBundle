<?php
declare(strict_types=1);

namespace Sigmapix\ProcessEventBundle\Process;

use Sigmapix\ProcessEventBundle\Entity\ProcessEntity;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Process
 * @package Sigmapix\ProcessEventBundle\Process
 */
abstract class Process implements ProcessInterface
{
    use ContainerAwareTrait;

    /**
     * @var array
     */
    protected $args; // TODO should be a ParametersBag (string|int values only)

    /**
     * ReplyReceivedEvent constructor.
     * @param Request $request
     */
    public function __construct(array $args = [])
    {
        $this->args = $args;
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @param ProcessEntity $processEntity
     * @return void
     * @throws \Doctrine\ORM\ORMException
     */
    public function run(ProcessEntity $processEntity)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        $processEntity->setStatus(ProcessEntity::RUNNING);
        $em->persist($processEntity);
        $em->flush();
        try {
            $value = $this->execute($processEntity);
            $processEntity->setProgress(100);
        } catch (\Exception $exception) {
            $value = false;
            $processEntity->setErrorOutput($processEntity->getErrorOutput().PHP_EOL.PHP_EOL.$exception->getMessage());
        }
        $processEntity->setStatus($value ? ProcessEntity::SUCCEED : ProcessEntity::FAILED);
        $em->persist($processEntity);
        $em->flush();
    }

    /**
     * @param ProcessEntity $processEntity
     * @return bool
     */
    protected function execute(ProcessEntity $processEntity)
    {
        // ...
        return true;
    }
}
