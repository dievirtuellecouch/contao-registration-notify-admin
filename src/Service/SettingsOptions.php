<?php

namespace Websailing\RegistrationNotifyAdminBundle\Service;

use Contao\System;

class SettingsOptions
{
    public static function onLoad(): void
    {
        try {
            $container = System::getContainer();
            $db = $container->get('database_connection');
            // Load all notifications with type for clearer labels
            $rows = $db->createQueryBuilder()
                ->select('id','title','type')
                ->from('tl_nc_notification')
                ->orderBy('title','ASC')
                ->executeQuery()
                ->fetchAllAssociative();
            $map = [];
            foreach ($rows as $r) {
                $label = (string) ($r['title'] ?? '') . ' [' . (string) ($r['type'] ?? '') . ']';
                $map[(int) $r['id']] = $label;
            }
            // Set options explicitly to avoid empty selects
            $GLOBALS['TL_DCA']['tl_settings']['fields']['rna_nc_registration_notification']['options'] = $map ?: [];
            $GLOBALS['TL_DCA']['tl_settings']['fields']['rna_nc_activation_notification']['options'] = $map ?: [];
            $GLOBALS['TL_DCA']['tl_settings']['fields']['rna_nc_profile_update_notification']['options'] = $map ?: [];
            // Ensure callbacks do not override the explicit options
            unset($GLOBALS['TL_DCA']['tl_settings']['fields']['rna_nc_registration_notification']['options_callback']);
            unset($GLOBALS['TL_DCA']['tl_settings']['fields']['rna_nc_activation_notification']['options_callback']);
            unset($GLOBALS['TL_DCA']['tl_settings']['fields']['rna_nc_profile_update_notification']['options_callback']);
            // no debug
        } catch (\Throwable $e) {}
    }
    public static function registration(): array
    {
        return self::allWithLog('registration');
    }

    public static function activation(): array
    {
        return self::allWithLog('activation');
    }

    public static function profileUpdate(): array
    {
        return self::allWithLog('profile_update');
    }

    private static function allWithLog(string $context): array
    {
        try {
            $container = System::getContainer();
            $db = $container->get('database_connection');
            $list = $db->createQueryBuilder()
                ->select('id','title')
                ->from('tl_nc_notification')
                ->orderBy('title','ASC')
                ->executeQuery()
                ->fetchAllKeyValue();

            // no debug

            return $list ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }
}
