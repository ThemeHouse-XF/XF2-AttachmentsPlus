<?php

namespace ThemeHouse\AttachmentsPlus\XF\Pub\Controller;

class Attachment extends XFCP_Attachment
{
    /**
     * @return \XF\Mvc\Reply\Error|\XF\Mvc\Reply\Redirect|\XF\Mvc\Reply\View
     * @throws \Exception
     */
    public function actionUpload()
    {

        $type = $this->filter('type', 'str');
        $handler = $this->getAttachmentRepo()->getAttachmentHandler($type);
        if (!$handler) {
            return $this->noPermission();
        }

        $context = $this->filter('context', 'array-str');
        if (!$handler->canManageAttachments($context, $error)) {
            return $this->noPermission($error);
        }

        $hash = $this->filter('hash', 'str');
        if (!$hash) {
            return $this->noPermission();
        }

        /** @var \XF\Attachment\Manipulator $manipulator */
        $class = \XF::extendClass('XF\Attachment\Manipulator');
        $manipulator = new $class($handler, $this->getAttachmentRepo(), $context, $hash);

        if ($this->isPost()) {
            $json = [];

            $delete = $this->filter('delete', 'uint');
            if ($delete) {
                $manipulator->deleteAttachment($delete);
                $json['delete'] = $delete;
            }

            $uploadError = null;
            if ($manipulator->canUpload($uploadError)) {
                $upload = $this->request->getFile('upload', false, false);
                if ($upload) {
                    /** @var \ThemeHouse\AttachmentsPlus\XF\Repository\Attachment $repo */
                    $repo = $this->getAttachmentRepo();
                    $totals = $repo->getThUserTotals();

                    $visitor = \XF::visitor();
                    $maxAttachments = $visitor->hasPermission('thattachmentsplus', 'maxAttachments');
                    $maxSpace = $visitor->hasPermission('thattachmentsplus', 'maxDiskSpace') * 1048576;

                    if($maxAttachments >= 0 && $totals['attach_count'] >= $maxAttachments) {
                        return $this->error(\XF::phrase('thattachmentsplus_you_have_reached_your_maximum_number_of_attachments'));
                    }

                    if($maxSpace >= 0 && $totals['disk_space'] + $upload->getFileSize() >= $maxSpace) {
                        return $this->error(\XF::phrase('thattachmentsplus_maximum_storage_space_exceeded'));
                    }
                }
            }
        }


        return parent::actionUpload();
    }
}
