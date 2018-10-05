<?php
declare(strict_types=1);

namespace Sigmapix\ProcessEventBundle\Process;

use Sigmapix\ProcessEventBundle\Entity\ProcessEntity;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Class Process
 * @package Sigmapix\ProcessEventBundle\Process
 */
abstract class Process implements ProcessInterface
{
    use ContainerAwareTrait;

    /**
     * @var ParameterBag
     */
    private $args;

    /**
     * Process constructor.
     * @param array $args
     */
    public function __construct(array $args = null)
    {
        $this->args = new ParameterBag();
        foreach ($args as $key => $value) {
            if (\is_int($value) || \is_string($value) || $value === null) {
                $this->args->set($key, $value);
            } else {
                trigger_error('Process expected $args to be integer or string', E_USER_ERROR);
            }
        }
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args->all();
    }

    /**
     * @param string $name
     * @return int|string|null
     */
    public function get(string $name)
    {
        return $this->args->get($name);
    }

    /**
     * @param string $message
     */
    public function log(string $message)
    {
        echo $message.PHP_EOL;
    }

    /**
     * @param bool $value
     * @return int
     */
    public function getFinalStatus(bool $value = true)
    {
        return $value ? ProcessEntity::SUCCEED : ProcessEntity::FAILED;
    }
}
