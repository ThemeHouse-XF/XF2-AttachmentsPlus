<?php

namespace ThemeHouse\AttachmentsPlus\Template;

use ThemeHouse\AttachmentsPlus\Repository\AttachmentIcon;
use XF\Entity\Attachment;
use XF\Mvc\Entity\Entity;
use XF\Template\Templater;

/**
 * Class TemplaterSetup
 * @package ThemeHouse\AttachmentsPlus\Template
 */
class TemplaterSetup
{
    /**
     * @var
     */
    protected $attachmentIconCache;

    /**
     * @return bool
     */
    protected function getAttachmentIconCache()
    {
        if (is_null($this->attachmentIconCache)) {
            /** @var AttachmentIcon $repo */
            $repo = \XF::repository('ThemeHouse\AttachmentsPlus:AttachmentIcon');
            $this->attachmentIconCache = $repo->getAttachmentIconCache() ?: false;
        }

        return $this->attachmentIconCache;
    }

    /**
     * @param Templater $templater
     * @param $escape
     * @param Entity $attachment
     * @param string $size
     * @return string
     */
    public function fnThAttachPlusAttachmentIcon(Templater $templater, &$escape, Entity $attachment, $size = 'm')
    {
        $escape = false;

        $cache = $this->getAttachmentIconCache();

        if (!$cache) {
            return '<i aria-hidden="true"></i>';
        }

        $templater->includeCss('public:thattachmentsplus_attachmentIcons_core.less');
        $templater->includeCss('public:thattachmentsplus_attachmentIcons.less');

        if ($attachment instanceof Attachment) {
            /** @var Attachment $attachment */
            $fileExtension = $attachment->extension;
        } else {
            if ($attachment instanceof \ThemeHouse\AttachmentsPlus\Entity\AttachmentIcon) {
                /** @var \ThemeHouse\AttachmentsPlus\Entity\AttachmentIcon $attachment */
                $fileExtension = $attachment->file_extension;
            } else {
                return '<i aria-hidden="true"></i>';
            }
        }

        if (!isset($cache[$fileExtension])) {
            return '<i aria-hidden="true"></i>';
        }

        switch ($size) {
            case 'xxs':
            case 'xs':
            case 's':
            case 'm':
            case 'l':
                break;

            default:
                $size = 'm';
        }

        switch ($cache[$fileExtension]['type']) {
            case 'icon':
                $content = '<em class="' . $cache[$fileExtension]['data'] . '"></em>';
                break;

            case 'image':
                $content = '<img src="' . $cache[$fileExtension]['data'] . '" />';
                break;

            case 'text':
                $content = $cache[$fileExtension]['data'];
                break;

            default:
                return '<i aria-hidden="true"></i>';
        }

        return '<span class="thAttachmentsPlus-attachmentIcon thAttachmentsPlus-attachmentIcon--' . $cache[$fileExtension]['type'] . ' thAttachmentsPlus-attachmentIcon--' . $size . ' thAttachmentsPlus-attachmentIcon--' . $fileExtension . '">' . $content . '</span>';
    }
}