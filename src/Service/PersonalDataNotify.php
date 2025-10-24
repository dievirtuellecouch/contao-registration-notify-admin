<?php

namespace Websailing\RegistrationNotifyAdminBundle\Service;

use Contao\Frontend;

class PersonalDataNotify extends Frontend
{
    // Legacy no-op methods: debugging removed; hook handled by UpdatePersonalDataListener
    public function onInitDebug(): void {}

    public function onUpdate($user, $submitted, $module, $files): void {}

    public function onUpdateFromOnsubmit($dc = null): void {}
}
