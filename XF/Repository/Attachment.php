<?php

namespace ThemeHouse\AttachmentsPlus\XF\Repository;

use XF\Entity\User;
use XF\Mvc\Entity\Finder;

/**
 * Class Attachment
 * @package ThemeHouse\AttachmentsPlus\XF\Repository
 */
class Attachment extends XFCP_Attachment
{
    /**
     * @return array
     */
    protected function getThInvalidAttachmentManagerTypes()
    {
        return array_map('trim', preg_split('/[\s,]/', \XF::options()->thattachmentsplus_invalidContentTypes));
    }

    /**
     * @param User|null $user
     * @param bool $withImages
     * @return Finder
     */
    public function findAttachmentsForThAttachmentManager(User $user = null, $withImages = true)
    {
        if (!$user) {
            $user = \XF::visitor();
        }

        $finder = $this->finder('XF:Attachment')
            ->with('Data', true)
            ->setDefaultOrder('attach_date', 'DESC')
            ->where('content_type', '!=', $this->getThInvalidAttachmentManagerTypes())
            ->where('Data.user_id', '=', $user->user_id);

        if (!$withImages) {
            $finder->where('Data.thumbnail_width', '=', 0);
        }

        return $finder;
    }

    /**
     * @param User|null $user
     * @return Finder
     */
    public function findImagesForThAttachmentManager(User $user = null)
    {
        if (!$user) {
            $user = \XF::visitor();
        }

        $finder = $this->finder('XF:Attachment')
            ->with('Data', true)
            ->setDefaultOrder('attach_date', 'DESC')
            ->where('content_type', '!=', $this->getThInvalidAttachmentManagerTypes())
            ->where('Data.thumbnail_width', '>', 0)
            ->where('Data.user_id', '=', $user->user_id);

        return $finder;
    }

    /**
     * @param User|null $user
     * @param bool $withImages
     * @return array|bool
     */
    public function getThUserTotals(User $user = null, $withImages = true)
    {
        if (!$user) {
            $user = \XF::visitor();
        }

        return \XF::db()->fetchRow('
            SELECT
                COUNT(*) AS attach_count,
                SUM(data.file_size) AS disk_space
            FROM
              xf_attachment_data data
            JOIN
              xf_attachment attachment USING(data_id)
            WHERE
              data.user_id = ?
              ' . ($withImages ? '' : ' AND data.thumbnail_width = 0 ') . '
              AND attachment.content_type NOT IN (\'' . join("','", $this->getThInvalidAttachmentManagerTypes()) . '\')
        ', [$user->user_id]);
    }

    /**
     * @param User|null $user
     * @return array|bool
     */
    public function getThUserImageTotals(User $user = null)
    {
        if (!$user) {
            $user = \XF::visitor();
        }

        return \XF::db()->fetchRow('
            SELECT
                COUNT(*) AS attach_count,
                SUM(data.file_size) AS disk_space
            FROM
              xf_attachment_data data
            JOIN
              xf_attachment attachment USING(data_id)
            WHERE
              data.user_id = ?
              AND data.thumbnail_width
              AND attachment.content_type NOT IN (\'' . join("','", $this->getThInvalidAttachmentManagerTypes()) . '\')
        ', [$user->user_id]);
    }
}
