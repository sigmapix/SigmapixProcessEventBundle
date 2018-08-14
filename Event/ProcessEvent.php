<?php
declare(strict_types=1);

namespace Sigmapix\ProcessEventBundle\Event;

use Sigmapix\ProcessEventBundle\Entity\ProcessEventEntity;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ProcessEvent
 * @package AnnonceBundle\Event
 */
abstract class ProcessEvent extends Event implements ProcessEventInterface
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
     * @param ProcessEventEntity $processEventEntity
     * @return void
     * @throws \Doctrine\ORM\ORMException
     */
    public function run(ProcessEventEntity $processEventEntity)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        $processEventEntity->setStatus(ProcessEventEntity::RUNNING);
        $em->persist($processEventEntity);
        $em->flush();
        try {
            $value = $this->execute($processEventEntity);
            $processEventEntity->setProgress(100);
        } catch (\Exception $exception) {
            $value = false;
            $processEventEntity->setErrorOutput($processEventEntity->getErrorOutput().PHP_EOL.PHP_EOL.$exception->getMessage());
        }
        $processEventEntity->setStatus($value ? ProcessEventEntity::SUCCEED : ProcessEventEntity::FAILED);
        $em->persist($processEventEntity);
        $em->flush();
    }

    /**
     * @param ProcessEventEntity $processEventEntity
     * @return bool
     */
    protected function execute(ProcessEventEntity $processEventEntity)
    {
        // ...
        return true;
    }
}
