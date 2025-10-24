<?php

namespace Websailing\RegistrationNotifyAdminBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\FilesModel;
use Contao\FrontendUser;
use Contao\MemberModel;
use Contao\StringUtil;

/**
 * Injects the current avatar preview above the avatar upload widget
 * in the personal data frontend module (module id 41).
 */
class ParseTemplateAvatarPreviewListener
{
    /**
     * @Hook("parseFrontendTemplate")
     */
    public function __invoke(string $buffer, string $templateName): string
    {
        // Only target the personal data module templates
        if (0 !== strpos($templateName, 'member_')) {
            return $buffer;
        }

        $moduleId = 41; // requested module instance
        $needle = 'id="ctrl_avatar_'.$moduleId.'"';

        if (false === ($pos = strpos($buffer, $needle))) {
            return $buffer;
        }

        // Resolve current avatar path
        $src = '';
        try {
            $feUser = FrontendUser::getInstance();
            $raw = $feUser->avatar ?? null;
            if (!$raw && isset($feUser->id)) {
                if (($m = MemberModel::findByPk((int) $feUser->id)) !== null) {
                    $raw = $m->avatar;
                }
            }
            if (is_string($raw) && $raw !== '') {
                if (strlen($raw) === 16) {
                    $uuid = StringUtil::binToUuid($raw);
                    if ($uuid) {
                        if ($fm = FilesModel::findByUuid($uuid)) { $src = (string) $fm->path; }
                    }
                } elseif (preg_match('~^[0-9a-fA-F\-]{36}$~', $raw)) {
                    if ($fm = FilesModel::findByUuid($raw)) { $src = (string) $fm->path; }
                } elseif (0 === strpos($raw, 'files/')) {
                    $src = $raw;
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        if ($src === '') {
            return $buffer;
        }

        // Normalize to absolute path
        if (!preg_match('~^https?://~i', $src) && 0 !== strpos($src, '/')) {
            $src = '/'.$src;
        }

        $previewHtml = '<div class="widget widget-avatar-preview"><img src="'.htmlspecialchars($src, ENT_QUOTES, 'UTF-8').'" alt="Avatar" class="avatar-preview" style="max-width:300px;height:auto"></div>';

        // Inject preview before the surrounding upload widget
        $before = substr($buffer, 0, $pos);
        $divStart = strrpos($before, '<div');
        if ($divStart === false) {
            return $previewHtml.$buffer;
        }

        return substr($buffer, 0, $divStart).$previewHtml.substr($buffer, $divStart);
    }
}
