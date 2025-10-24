<?php

namespace Websailing\RegistrationNotifyAdminBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class RegistrationNotifyAdminExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        // Load our service definitions so annotations (e.g. @Hook) apply to real services
        if (is_file(__DIR__.'/../Resources/config/services.yaml')) {
            $loader->load('services.yaml');
        }
    }
}

