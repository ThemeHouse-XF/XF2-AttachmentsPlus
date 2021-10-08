<?php

namespace ThemeHouse\AttachmentsPlus\XF\Entity;

/**
 * Class Attachment
 * @package ThemeHouse\AttachmentsPlus\XF\Entity
 */
class Attachment extends XFCP_Attachment
{
    /**
     * @param null $error
     * @return bool
     */
    public function canTHAttachPlusDelete(&$error = null)
    {
        $canDelete = false;

        if ($this->Container && method_exists($this->Container, 'canDelete')) {
            $canDelete = $canDelete || $this->Container->canDelete($error);
        }

        if ($this->Container && method_exists($this->Container, 'canEdit')) {
            $canDelete = $canDelete || $this->Container->canEdit($error);
        }

        return $canDelete;
    }
}
