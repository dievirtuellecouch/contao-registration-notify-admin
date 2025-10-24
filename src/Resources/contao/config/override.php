<?php

// Override options_callback to avoid empty dropdowns if type-specific list is empty
$GLOBALS['TL_DCA']['tl_settings']['fields']['rna_nc_registration_notification']['options_callback'] = [\Websailing\RegistrationNotifyAdminBundle\Service\SettingsOptions::class, 'registration'];
$GLOBALS['TL_DCA']['tl_settings']['fields']['rna_nc_activation_notification']['options_callback'] = [\Websailing\RegistrationNotifyAdminBundle\Service\SettingsOptions::class, 'activation'];
$GLOBALS['TL_DCA']['tl_settings']['fields']['rna_nc_profile_update_notification']['options_callback'] = [\Websailing\RegistrationNotifyAdminBundle\Service\SettingsOptions::class, 'profileUpdate'];

