<?php
declare(strict_types=1);

namespace Sigmapix\ProcessEventBundle\Controller;

use Sigmapix\ProcessEventBundle\Entity\ProcessEntity;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class ProcessEntityCRUDController
 * @package Sigmapix\ProcessEventBundle\Controller
 */
class ProcessEntityCRUDController extends Controller
{
    /**
     * @return BinaryFileResponse
     */
    public function downloadAction(): BinaryFileResponse
    {
        /** @var ProcessEntity $processEntity */
        $processEntity = $this->admin->getSubject();

        $filePath = $processEntity->getFilePath();

        return $this->file($filePath);
    }
}