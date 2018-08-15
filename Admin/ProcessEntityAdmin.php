<?php

namespace Sigmapix\ProcessEventBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Class ProcessEntityAdmin
 * @package Sigmapix\ProcessEventBundle\Admin
 */
class ProcessEntityAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('className')
            ->add('command')
            ->add('pid')
            ->add('filePath')
            ->add('output')
            ->add('errorOutput')
            ->add('status')
            ->add('progress');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $actions = [
            'show' => [],
            'download' => [
                'template' => 'SigmapixProcessEventBundle:Admin/Button:list__action_download.html.twig',
            ]
        ];

        $listMapper
            ->add('name')
            ->add('status')
            ->add('progress')
            ->add('_action', null, ['actions' => $actions]);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('className')
            ->add('command')
            ->add('pid')
            ->add('filePath')
            ->add('output')
            ->add('errorOutput')
            ->add('status')
            ->add('progress');
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('download', 'download');
    }
}
