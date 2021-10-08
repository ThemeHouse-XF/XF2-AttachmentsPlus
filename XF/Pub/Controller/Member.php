<?php

namespace ThemeHouse\AttachmentsPlus\XF\Pub\Controller;

use ThemeHouse\AttachmentsPlus\XF\Entity\User;
use XF\Mvc\Entity\Repository;
use XF\Mvc\ParameterBag;

/**
 * Class Member
 * @package ThemeHouse\AttachmentsPlus\XF\Pub\Controller
 */
class Member extends XFCP_Member
{
    /**
     * @param ParameterBag $params
     * @return mixed
     * @throws \XF\Mvc\Reply\Exception
     */
    public function actionThattachplusAttachments(ParameterBag $params)
    {
        /** @var User $user */
        $user = $this->assertViewableUser($params['user_id']);
        /** @var User $visitor */
        $visitor = \XF::visitor();

        if (!$visitor->canViewThAttachmentsTab($error)) {
            return $this->noPermission($error);
        }

        $withImages = !\XF::options()->thattachmentsplus_splitImageAttachments;

        $attachmentRepo = $this->getAttachmentRepo();
        $attachmentFinder = $attachmentRepo->findAttachmentsForThAttachmentManager($user, $withImages);

        $total = $attachmentFinder->total();
        $page = $this->filterPage();
        $perPage = 25;

        $attachments = $attachmentFinder->limitByPage($page, $perPage)->fetch();

        $viewParams = [
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,

            'user' => $user,
            'attachments' => $attachments,
            'handlers' => $attachmentRepo->getAttachmentHandlers()
        ];

        return $this->view('ThemeHouse\AttachmentsPlus:User\Attachments', 'thattachplus_member_attachments',
            $viewParams);
    }
    /**
     * @param ParameterBag $params
     * @return mixed
     * @throws \XF\Mvc\Reply\Exception
     */
    public function actionThattachplusImages(ParameterBag $params)
    {
        /** @var User $user */
        $user = $this->assertViewableUser($params['user_id']);
        /** @var User $visitor */
        $visitor = \XF::visitor();

        if (!$visitor->canViewThAttachmentsTab($error)) {
            return $this->noPermission($error);
        }

        $attachmentRepo = $this->getAttachmentRepo();
        $attachmentFinder = $attachmentRepo->findImagesForThAttachmentManager($user);

        $total = $attachmentFinder->total();
        $page = $this->filterPage();
        $perPage = 25;

        $attachments = $attachmentFinder->limitByPage($page, $perPage)->fetch();

        $viewParams = [
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,

            'user' => $user,
            'attachments' => $attachments,
            'handlers' => $attachmentRepo->getAttachmentHandlers()
        ];

        return $this->view('ThemeHouse\AttachmentsPlus:User\Attachments', 'thattachplus_member_images',
            $viewParams);
    }

    /**
     * @return  Repository|\ThemeHouse\AttachmentsPlus\XF\Repository\Attachment
     */
    protected function getAttachmentRepo()
    {
        return $this->repository('XF:Attachment');
    }
}
