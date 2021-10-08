<?php

namespace ThemeHouse\AttachmentsPlus\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * Class AttachmentIcon
 * @package ThemeHouse\AttachmentsPlus\Entity
 *
 * @property string file_extension
 * @property string type
 * @property string extra_css
 * @property array data
 */
class AttachmentIcon extends Entity
{
    /**
     *
     * @throws \XF\PrintableException
     */
    protected function _postSave()
    {
        /** @var \ThemeHouse\AttachmentsPlus\Repository\AttachmentIcon $repo */
        $repo = $this->repository('ThemeHouse\AttachmentsPlus:AttachmentIcon');
        $repo->rebuildAttachmentIconCache();
    }

    /**
     * @param Structure $structure
     * @return Structure
     */
    public static function getStructure(Structure $structure)
    {
        $structure->primaryKey = 'file_extension';
        $structure->table = 'xf_th_attachmentsplus_attachment_icon';
        $structure->shortName = 'ThemeHouse\AttachmentsPlus:AttachmentIcon';

        $structure->columns = [
            'file_extension' => ['type' => self::STR, 'maxLength' => 10, 'required' => true],
            'type' => ['type' => self::STR, 'allowedValues' => ['image', 'icon', 'text'], 'default' => 'image'],
            'extra_css' => ['type' => self::STR],
            'data' => ['type' => self::JSON]
        ];

        return $structure;
    }
}