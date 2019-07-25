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

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $processedAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $finishedAt;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array")
     */
    private $finishedAtSteps;

    public function __construct()
    {
        $this->status = self::NEW;
        $this->progress = 0;
        $this->args = [];
        $this->finishedAtSteps = [];
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
    public function getName()
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
    public function getClassName()
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
     * @return array
     */
    public function getStatusList(): array
    {
        return [
            self::NEW => 'NEW',
            self::RUNNING => 'RUNNING',
            self::SUCCEED => 'SUCCEED',
            self::FAILED => 'FAILED'
        ];
    }

    /**
     * @return string
     */
    public function getStatusName(): string
    {
        return $this->getStatusList()[$this->getStatus()];
    }

    /**
     * @param int $status
     *
     * @throws \Exception
     */
    public function setStatus(int $status)
    {
        $this->status = $status;

        if ($status === self::RUNNING) { // sets processedAt datetime when process starts RUNNING
            $this->processedAt = new \DateTime();
        } else if ($status !== self::NEW) { // sets a finishedAt datetime step as process ended
            $this->addFinishedAtStep($this->getClassName());
        }
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

    /**
     * @return \DateTime|null
     */
    public function getProcessedAt()
    {
        return $this->processedAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getFinishedAt()
    {
        return $this->finishedAt;
    }

    /**
     * @return array
     */
    public function getFinishedAtSteps(): array
    {
        return $this->finishedAtSteps;
    }

    /**
     * @return string
     */
    public function getFinishedAtStepsAsString(): string
    {
        $map = array_map(function($v) {
            /** @var \DateTime $dateTime */
            $dateTime = new \DateTime($v['finishedAt']['date']);
            return $v['initiator'] . ' : ' . $dateTime->format('d M Y H:i:s');
        }, $this->finishedAtSteps);
        return implode(PHP_EOL, $map);
    }

    /**
     * @param string $initiator
     * @return array
     * @throws \Exception
     */
    public function addFinishedAtStep(string $initiator): array
    {
        $this->finishedAt = new \DateTime();
        $this->finishedAtSteps[] = ['initiator' => $initiator, 'finishedAt' => $this->finishedAt];

        return $this->finishedAtSteps;
    }
}
