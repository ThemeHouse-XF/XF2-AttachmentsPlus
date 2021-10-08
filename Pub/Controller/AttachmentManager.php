<?php

namespace ThemeHouse\AttachmentsPlus\Pub\Controller;

use XF\Mvc\Entity\Repository;
use XF\Mvc\ParameterBag;
use XF\Pub\Controller\AbstractController;

/**
 * Class AttachmentManager
 * @package ThemeHouse\AttachmentsPlus\Pub\Controller
 */
class AttachmentManager extends AbstractController
{
    /**
     * @param ParameterBag $params
     * @return \XF\Mvc\Reply\View
     * @throws \Exception
     */
    public function actionIndex(ParameterBag $params)
    {
        /** @var \ThemeHouse\AttachmentsPlus\XF\Entity\User $visitor */
        $visitor = \XF::visitor();

        if (!$visitor->canViewThAttachmentManager($error)) {
            return $this->noPermission($error);
        }

        $attachmentRepo = $this->getAttachmentRepo();
        $page = $this->filterPage();
        $perPage = 25;

        $finder = $attachmentRepo->findAttachmentsForThAttachmentManager();
        $finder->limitByPage($page, $perPage);

        $viewParams = [
            'page' => $page,
            'perPage' => $perPage,

            'attachments' => $finder->fetch(),
            'handlers' => $attachmentRepo->getAttachmentHandlers(),
            'userMax' => [
                'attach_count' => $visitor->hasPermission('thattachmentsplus', 'maxAttachments'),
                'disk_space' => $visitor->hasPermission('thattachmentsplus', 'maxDiskSpace')
            ],
            'userTotals' => $attachmentRepo->getThUserTotals()
        ];

        return $this->view('TH\AttachmentsPlus:AttachmentManager\List', 'thattachmentsplus_attachment_manager_list',
            $viewParams);
    }

    /**
     * @param ParameterBag $params
     * @return \XF\Mvc\Reply\Error|\XF\Mvc\Reply\View
     * @throws \XF\Mvc\Reply\Exception
     */
    public function actionView(ParameterBag $params)
    {
        //$attachment = $this->assertViewableAttachment($params['attachment_id']);
        $attachment = $this->assertRecordExists('XF:Attachment', $params['attachment_id'], ['Data']);

        if (!$attachment->canView($error)) {
            return $this->noPermission($error);
        }

        if (!$attachment->Data || !$attachment->Data->isDataAvailable()) {
            return $this->error(\XF::phrase('attachment_cannot_be_shown_at_this_time'));
        }

        $this->setResponseType('raw');

        $eTag = $this->request->getServer('HTTP_IF_NONE_MATCH');
        $return304 = ($eTag && $eTag == '"' . $attachment['attach_date'] . '"');

        $viewParams = [
            'attachment' => $attachment,
            'return304' => $return304
        ];
        return $this->view('XF:Attachment\View', '', $viewParams);
    }

    /**
     * @param ParameterBag $params
     * @return \XF\Mvc\Reply\Error|\XF\Mvc\Reply\Redirect|\XF\Mvc\Reply\View
     * @throws \XF\Mvc\Reply\Exception
     * @throws \XF\PrintableException
     */
    public function actionDelete(ParameterBag $params)
    {
        $attachment = $this->assertUserAttachment($params['attachment_id']);

        if (!$attachment->canTHAttachPlusDelete($error)) {
            return $this->noPermission($error);
        }

        if ($this->isPost()) {
            $attachment->delete(false);

            return $this->redirect($this->buildLink('account/th-attachment-manager', null,
                ['type' => $this->filter('type', 'str', 'post')]));
        } else {

            $viewParams = [
                'attachment' => $attachment
            ];

            return $this->view('TH\AttachmentsPlus:AttachmentManager\Delete',
                'thattachmentsplus_attachment_manager_delete', $viewParams);
        }
    }

    /**
     * @param $id
     * @param null $with
     * @param null $phraseKey
     * @return \ThemeHouse\AttachmentsPlus\XF\Entity\Attachment|\XF\Mvc\Entity\Entity
     * @throws \XF\Mvc\Reply\Exception
     */
    protected function assertViewableAttachment($id, $with = null, $phraseKey = null)
    {
        $with[] = 'Data';
        return $this->assertViewableRecord('XF:Attachment', $id, array_unique($with), $phraseKey);
    }

    /**
     * @param $id
     * @param null $with
     * @param null $phraseKey
     * @return \ThemeHouse\AttachmentsPlus\XF\Entity\Attachment|\XF\Mvc\Entity\Entity
     * @throws \XF\Mvc\Reply\Exception
     * @throws \Exception
     */
    protected function assertUserAttachment($id, $with = null, $phraseKey = null)
    {
        $with[] = 'Data';
        $attachment = $this->assertRecordExists('XF:Attachment', $id, array_unique($with), $phraseKey);

        if ($attachment->Data->user_id != \XF::visitor()->user_id) {
            throw $this->exception($this->noPermission(\XF::phrase($phraseKey)));
        }

        return $attachment;
    }

    /**
     * @return  Repository|\ThemeHouse\AttachmentsPlus\XF\Repository\Attachment
     */
    protected function getAttachmentRepo()
    {
        return $this->repository('XF:Attachment');
    }
}
