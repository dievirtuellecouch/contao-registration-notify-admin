<?php

namespace Websailing\RegistrationNotifyAdminBundle\EventListener;

use Psr\Log\LoggerInterface;
use Terminal42\NotificationCenterBundle\NotificationCenter;
use Contao\Database;
use Contao\StringUtil;
use Contao\FilesModel;
use Contao\Dbafs;
use Contao\System;
use Contao\CoreBundle\ServiceAnnotation\Hook;

/**
 * @Hook("updatePersonalData")
 */
class UpdatePersonalDataListener
{
    public function __construct(private LoggerInterface $logger, private NotificationCenter $notificationCenter)
    {
    }

    /**
     * Contao hook signature: updatePersonalData($user, array $submitted, $module, array $files)
     * $user can be Contao\FrontendUser or an array.
     */
    public function __invoke($user, array $submitted, $module, array $files): void
    {
        // Normalize user to array
        if ($user instanceof \Contao\FrontendUser) {
            // Load full member via model to get an array
            try {
                $model = \Contao\MemberModel::findByPk((int) $user->id);
                $userArr = $model ? $model->row() : ['id' => (int) $user->id];
            } catch (\Throwable $e) {
                $userArr = ['id' => (int) $user->id];
            }
        } elseif (\is_object($user) && method_exists($user, 'row')) {
            $userArr = $user->row();
        } elseif (\is_array($user)) {
            $userArr = $user;
        } else {
            $userArr = [];
        }

        $memberId = (string) ($userArr['id'] ?? '');
        // Handle FE avatar from hook parameter $files (preferred, since ModulePersonalData already processed uploads)
        try {
            $newUuid = null;
            if ($memberId !== '' && isset($files['avatar']) && $files['avatar'] !== null && $files['avatar'] !== '') {
                $val = $files['avatar'];
                if (is_string($val)) {
                    if (strlen($val) === 16) {
                        $newUuid = StringUtil::binToUuid($val);
                    } elseif (preg_match('/^[0-9a-fA-F-]{36}$/', $val)) {
                        $newUuid = strtolower($val);
                    } elseif (str_starts_with($val, 'files/')) {
                        if ($fm = FilesModel::findByPath($val)) { $newUuid = $fm->uuid; }
                    }
                } elseif (is_array($val)) {
                    // Common keys: uuid, path
                    if (!empty($val['uuid']) && preg_match('/^[0-9a-fA-F-]{36}$/', (string)$val['uuid'])) {
                        $newUuid = strtolower((string) $val['uuid']);
                    } elseif (!empty($val['path']) && is_string($val['path'])) {
                        if ($fm = FilesModel::findByPath($val['path'])) { $newUuid = $fm->uuid; }
                    }
                }
            }
            if ($newUuid) {
                Database::getInstance()
                    ->prepare('UPDATE tl_member SET avatar=? WHERE id=?')
                    ->execute(StringUtil::uuidToBin($newUuid), (int) $memberId);
                $userArr['avatar'] = StringUtil::uuidToBin($newUuid);
                $submitted['avatar'] = 1;
                // Update current FE user object so subsequent rendering (e.g., menu) sees new avatar immediately
                try {
                    $fe = \Contao\FrontendUser::getInstance();
                    if (isset($fe->id) && (int) $fe->id === (int) $memberId) {
                        $fe->avatar = StringUtil::uuidToBin($newUuid);
                    }
                } catch (\Throwable $e) {}
                // no debug
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // Optionally send a notification if configured in tl_settings
        try {
            $notificationId = (int) (\Contao\Config::get('rna_nc_profile_update_notification') ?: 0);
            if ($notificationId > 0) {
                $tokens = [
                    'domain' => (string) (\Contao\Environment::get('host') ?: ''),
                    'member' => $userArr,
                    'member_old' => $userArr,
                    'changed' => array_fill_keys(array_keys($submitted), 1),
                    'admin_email' => ($GLOBALS['TL_ADMIN_EMAIL'] ?? ''),
                ];
                // no debug
                $this->notificationCenter->sendNotification($notificationId, $tokens);
            }
        } catch (\Throwable $e) {
            // ignore
        }
    }
}
