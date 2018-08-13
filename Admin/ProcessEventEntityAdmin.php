<?php

namespace Sigmapix\ProcessEventBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Class ProcessEventEntityAdmin
 * @package Sigmapix\ProcessEventBundle\Admin
 */
class ProcessEventEntityAdmin extends AbstractAdmin
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
            ->add('progress')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name')
            ->add('status')
            ->add('progress')
            ->add('_action', null, [
                'actions' => [
                    'show' => []
                    // TODO 'download' $filePath and 'output'/'errors' actions (dynamically)
                ]
            ])
        ;
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
            ->add('progress')
        ;
    }
}
