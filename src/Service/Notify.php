<?php

namespace Websailing\RegistrationNotifyAdminBundle\Service;

use Contao\Frontend;
use Contao\Environment;
use Contao\MemberModel;
use Contao\System;

class Notify extends Frontend
{
    public function onCreate(int $id, array $data, $module): void
    {
        // Read notification ID from settings; prefer NC if configured
        $notificationId = (int) (\Contao\Config::get('rna_nc_registration_notification') ?: 0);
        if ($notificationId <= 0) {
            return;
        }
        $tokens = [
            'domain' => Environment::get('host'),
            'member' => $data,
            'admin_email' => ($GLOBALS['TL_ADMIN_EMAIL'] ?? ('noreply@'.Environment::get('host'))),
        ];
        $this->sendNc($notificationId, $tokens);
    }

    public function onActivate($user, $registration): void
    {
        $notificationId = (int) (\Contao\Config::get('rna_nc_activation_notification') ?: 0);
        if ($notificationId <= 0) {
            return;
        }
        $row = [];
        try {
            if ($user instanceof MemberModel) {
                $row = $user->row();
            } elseif (\is_array($user) && isset($user[0]) && $user[0] instanceof MemberModel) {
                $row = $user[0]->row();
            } elseif (\is_array($user)) {
                $row = $user;
            }
        } catch (\Throwable $e) {}
        $tokens = [
            'domain' => Environment::get('host'),
            'member' => $row,
            'admin_email' => ($GLOBALS['TL_ADMIN_EMAIL'] ?? ('noreply@'.Environment::get('host'))),
        ];
        $this->sendNc($notificationId, $tokens);
    }

    private function sendNc(int $notificationId, array $tokens): void
    {
        try {
            /** @var \Terminal42\NotificationCenterBundle\NotificationCenter $nc */
            $nc = System::getContainer()->get(\Terminal42\NotificationCenterBundle\NotificationCenter::class);
            $nc->sendNotification($notificationId, $tokens);
        } catch (\Throwable $e) {}
    }
}
