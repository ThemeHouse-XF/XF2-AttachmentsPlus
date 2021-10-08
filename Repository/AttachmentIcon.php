<?php

namespace ThemeHouse\AttachmentsPlus\Repository;

use XF\Mvc\Entity\Repository;

/**
 * Class AttachmentIcon
 * @package ThemeHouse\AttachmentsPlus\Repository
 */
class AttachmentIcon extends Repository
{
    /**
     * @throws \XF\PrintableException
     */
    public function rebuildAttachmentIconCache()
    {
        $attachmentIcons = $this->findAttachmentIcons()->fetch();

        $templateContent = '';
        $cache = [];

        foreach ($attachmentIcons as $fileExtension => $attachmentIcon) {
            $templateContent .= ".thAttachmentsPlus-attachmentIcon--{$fileExtension} {{$attachmentIcon->extra_css}}\n\n";

            $cache[$fileExtension] = [
                'extension' => $fileExtension,
                'type' => $attachmentIcon->type,
                'data' => $attachmentIcon->data
            ];
        }

        /** @var \XF\Entity\Template $template */
        $template = $this->finder('XF:Template')
            ->where('style_id', '=', 0)
            ->where('title', '=', 'thattachmentsplus_attachmentIcons.less')
            ->fetchOne();

        if (!$template) {
            $template = $this->em->create('XF:Template');
            $template->style_id = 0;
            $template->type = 'public';
            $template->addon_id = '';
            $template->title = 'thattachmentsplus_attachmentIcons.less';
        }

        $template->template = $templateContent;
        $template->save();

        \XF::app()->simpleCache()->setValue('ThemeHouse/AttachmentsPlus', 'iconCache', $cache);
    }

    /**
     * @return null
     */
    public function getAttachmentIconCache() {
        return \XF::app()->simpleCache()->getValue('ThemeHouse/AttachmentsPlus', 'iconCache');
    }

    /**
     * @return \XF\Mvc\Entity\Finder
     */
    public function findAttachmentIcons()
    {
        $finder = $this->finder('ThemeHouse\AttachmentsPlus:AttachmentIcon');

        $finder->setDefaultOrder('file_extension', 'ASC');

        return $finder;
    }
}