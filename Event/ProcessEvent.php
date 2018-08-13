<?php
declare(strict_types=1);

namespace Sigmapix\ProcessEventBundle\Event;

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
    protected $args;

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
}
