<?php

// Legend
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{rna_legend},rna_nc_registration_notification,rna_nc_activation_notification,rna_nc_profile_update_notification';

// Ensure options are populated even if options_callback fails or returns empty
$GLOBALS['TL_DCA']['tl_settings']['config']['onload_callback'][] = [\Websailing\RegistrationNotifyAdminBundle\Service\SettingsOptions::class, 'onLoad'];

$GLOBALS['TL_DCA']['tl_settings']['fields']['rna_nc_registration_notification'] = [
    'label' => ['Notification (Registrierung)', 'Admin-Benachrichtigung bei neuer Registrierung.'],
    'exclude' => true,
    'inputType' => 'select',
    'options_callback' => static function () {
        try {
            $container = \Contao\System::getContainer();
            /** @var \Terminal42\NotificationCenterBundle\NotificationCenter $nc */
            $nc = $container->get(\Terminal42\NotificationCenterBundle\NotificationCenter::class);
            $list = $nc->getNotificationsForNotificationType(\Terminal42\NotificationCenterBundle\NotificationType\MemberRegistrationNotificationType::NAME);
            if (!\is_array($list) || !\count($list)) {
                // Fallback: list all notifications if none of matching type exist
                try {
                    $db = $container->get('database_connection');
                    $list = $db->createQueryBuilder()->select('id','title')->from('tl_nc_notification')->orderBy('title','ASC')->executeQuery()->fetchAllKeyValue();
                } catch (\Throwable $e2) {}
            }
            return $list ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    },
    'eval' => ['includeBlankOption'=>true, 'chosen'=>true, 'tl_class'=>'w50'],
    'sql'  => "int(10) unsigned NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['rna_nc_activation_notification'] = [
    'label' => ['Notification (Aktivierung)', 'Admin-Benachrichtigung bei Aktivierung.'],
    'exclude' => true,
    'inputType' => 'select',
    'options_callback' => static function () {
        try {
            $container = \Contao\System::getContainer();
            $nc = $container->get(\Terminal42\NotificationCenterBundle\NotificationCenter::class);
            $list = $nc->getNotificationsForNotificationType(\Terminal42\NotificationCenterBundle\NotificationType\MemberActivationNotificationType::NAME);
            if (!\is_array($list) || !\count($list)) {
                try {
                    $db = $container->get('database_connection');
                    $list = $db->createQueryBuilder()->select('id','title')->from('tl_nc_notification')->orderBy('title','ASC')->executeQuery()->fetchAllKeyValue();
                } catch (\Throwable $e2) {}
            }
            return $list ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    },
    'eval' => ['includeBlankOption'=>true, 'chosen'=>true, 'tl_class'=>'w50'],
    'sql'  => "int(10) unsigned NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['rna_nc_profile_update_notification'] = [
    'label' => ['Notification (Profiländerung)', 'Admin-Benachrichtigung, wenn ein Mitglied seine Daten ändert.'],
    'exclude' => true,
    'inputType' => 'select',
    'options_callback' => static function () {
        try {
            $container = \Contao\System::getContainer();
            $nc = $container->get(\Terminal42\NotificationCenterBundle\NotificationCenter::class);
            $list = $nc->getNotificationsForNotificationType(\Terminal42\NotificationCenterBundle\NotificationType\MemberPersonalDataNotificationType::NAME);
            if (!\is_array($list) || !\count($list)) {
                try {
                    $db = $container->get('database_connection');
                    $list = $db->createQueryBuilder()->select('id','title')->from('tl_nc_notification')->orderBy('title','ASC')->executeQuery()->fetchAllKeyValue();
                } catch (\Throwable $e2) {}
            }
            return $list ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    },
    'eval' => ['includeBlankOption'=>true, 'chosen'=>true, 'tl_class'=>'w50'],
    'sql'  => "int(10) unsigned NOT NULL default '0'",
];
