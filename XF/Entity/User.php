<?php

namespace ThemeHouse\AttachmentsPlus\XF\Entity;

/**
 * Class User
 * @package ThemeHouse\AttachmentsPlus\XF\Entity
 */
class User extends XFCP_User
{
    /**
     * @param null $error
     * @return bool
     */
    public function canViewThAttachmentManager(&$error = null)
    {
        return $this->hasPermission('thattachmentsplus', 'viewAttachmentManager');
    }

    public function canViewThAttachmentsTab(&$error = null)
    {
        return $this->hasPermission('thattachmentsplus', 'viewAttachmentTab');
    }

    public function showThAttachmentsPlusTab($type)
    {
        if(\XF::options()->thattachmentsplus_hideEmptyTabs) {
            /** @var \ThemeHouse\AttachmentsPlus\XF\Repository\Attachment $attachmentRepo */
            $attachmentRepo = \XF::repository('XF:Attachment');

            switch ($type) {
                case 'main':
                    return $attachmentRepo->getThUserTotals($this, !\XF::options()->thattachmentsplus_splitImageAttachments)['attach_count'] > 0;
                    break;

                case 'images':
                    return $attachmentRepo->getThUserImageTotals($this)['attach_count'] > 0;
                    break;
            }
        }

        return true;
    }
}
