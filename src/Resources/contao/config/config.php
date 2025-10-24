<?php

$GLOBALS['TL_HOOKS']['createNewUser'][] = [\Websailing\RegistrationNotifyAdminBundle\Service\Notify::class, 'onCreate'];
$GLOBALS['TL_HOOKS']['activateAccount'][] = [\Websailing\RegistrationNotifyAdminBundle\Service\Notify::class, 'onActivate'];
// updatePersonalData handled by service-annotated listener
