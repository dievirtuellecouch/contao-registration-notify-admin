<?php

namespace Websailing\RegistrationNotifyAdminBundle\ContaoManager;

use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Websailing\RegistrationNotifyAdminBundle\RegistrationNotifyAdminBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(RegistrationNotifyAdminBundle::class)
                ->setLoadAfter(['Contao\CoreBundle\ContaoCoreBundle'])
        ];
    }
}

