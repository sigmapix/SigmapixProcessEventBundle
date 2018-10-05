<?php
declare(strict_types=1);

namespace Sigmapix\ProcessEventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * ProcessEntity
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class ProcessEntity
{
    use TimestampableEntity;

    const NEW = 1;
    const RUNNING = 2;
    const SUCCEED = 3;
    const FAILED = 4;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $className;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $progress;

    /**
     * @var array
     *
     * @ORM\Column(type="array", nullable=false)
     */
    private $args;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filePath;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $output;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $errorOutput;

    public function __construct()
    {
        $this->status = self::NEW;
        $this->progress = 0;
        $this->args = [];
        $this->name = 'New process';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s', $this->getName());
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     */
    public function setFilePath(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param string $className
     */
    public function setClassName(string $className)
    {
        $this->className = $className;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param string $output
     */
    public function setOutput(string $output)
    {
        $this->output = $output;
    }

    /**
     * @return string
     */
    public function getErrorOutput()
    {
        return $this->errorOutput;
    }

    /**
     * @param string $errorOutput
     */
    public function setErrorOutput(string $errorOutput)
    {
        $this->errorOutput = $errorOutput;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getProgress(): int
    {
        return $this->progress;
    }

    /**
     * @param int $progress
     */
    public function setProgress(int $progress)
    {
        $this->progress = $progress;
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @param array $args
     */
    public function setArgs(array $args)
    {
        $this->args = $args;
    }
}
