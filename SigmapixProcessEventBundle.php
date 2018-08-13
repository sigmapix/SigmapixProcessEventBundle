<?php

namespace Sigmapix\ProcessEventBundle;

use Sigmapix\ProcessEventBundle\DependencyInjection\Compiler\OverrideServiceCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class SigmapixProcessEventBundle
 * @package Sigmapix\Sonata\ImportBundle
 */
class SigmapixProcessEventBundle extends Bundle
{
    /**
     * Builds the bundle.
     *
     * It is only ever called once when the cache is empty.
     *
     * This method can be overridden to register compilation passes,
     * other extensions, ...
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new OverrideServiceCompilerPass());
    }
}
