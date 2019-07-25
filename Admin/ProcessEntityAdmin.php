<?php
declare(strict_types=1);

namespace Sigmapix\ProcessEventBundle\Admin;

use Symfony\Component\Form\CallbackTransformer;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

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
            ->add('filePath')
            ->add('output')
            ->add('errorOutput')
            ->add('status')
            ->add('processedAt')
            ->add('finishedAt')
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
                'template' => 'SigmapixProcessEventBundle:Admin/Button:list__action_download.html.twig'
            ]
        ];

        $listMapper
            ->add('name')
            ->add('statusName', null, ['label' => 'status'])
            ->add('processedAt')
            ->add('finishedAt')
            ->add('progress', null, ['template' => 'SigmapixProcessEventBundle:Admin:list__progress.html.twig'])
            ->add('_action', null, ['actions' => $actions]);
    }

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('name')
            ->add('className')
            ->add('status')
            ->add('processedAt', null, ['disabled' => true, 'date_widget' => 'single_text', 'time_widget' => 'single_text'])
            ->add('finishedAt', null, ['disabled' => true, 'date_widget' => 'single_text', 'time_widget' => 'single_text'])
            ->add('finishedAtStepsAsString', TextareaType::class, ['disabled' => true, 'attr' => ['rows' => 3]])
            ->add('args', TextareaType::class)
           ;
        $form->getFormBuilder()->get('args')->addModelTransformer(new CallbackTransformer(
                function ($tagsAsArray) {
                    if (empty($tagsAsArray)) {
                        return '';
                    }
                    return json_encode($tagsAsArray);
                },
                function ($tagsAsString) {
                    // transform the string back to an array
                    return json_decode(trim(preg_replace('/\s\s+/', ' ', $tagsAsString)), true);
                }
            ))
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
            ->add('args')
            ->add('filePath')
            ->add('output')
            ->add('errorOutput')
            ->add('statusName')
            ->add('processedAt')
            ->add('finishedAt')
            ->add('finishedAtStepsAsString')
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
