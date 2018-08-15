<?php
namespace Sigmapix\ProcessEventBundle\Controller;

use Sigmapix\ProcessEventBundle\Entity\ProcessEntity;

use Sonata\AdminBundle\Controller\CRUDController as Controller;

/**
 * Class ProcessEntityCRUDController
 * @package Sigmapix\ProcessEventBundle\Controller
 */
class ProcessEntityCRUDController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadAction()
    {
        /** @var ProcessEntity $processEntity */
        $processEntity = $this->admin->getSubject();

        $filePath = $processEntity->getFilePath();

        return $this->file($filePath);
    }
}