<?php
declare(strict_types=1);

namespace Sigmapix\ProcessEventBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class SigmapixProcessEventExtension.
 */
class SigmapixProcessEventExtension extends Extension
{
    /**
     * {@inheritdoc}
     * @throws \Exception
     * @throws \Exception
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('admin.yml');
        $loader->load('config.yml');
    }
}
