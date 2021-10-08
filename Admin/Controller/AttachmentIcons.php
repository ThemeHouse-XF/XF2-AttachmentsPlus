<?php

namespace ThemeHouse\AttachmentsPlus\Admin\Controller;

use ThemeHouse\AttachmentsPlus\Entity\AttachmentIcon;
use XF\Admin\Controller\AbstractController;
use XF\Mvc\ParameterBag;

/**
 * Class AttachmentIcons
 * @package ThemeHouse\AttachmentsPlus\Admin\Controller
 */
class AttachmentIcons extends AbstractController
{
    /**
     * @return \XF\Mvc\Reply\View
     */
    public function actionIndex()
    {
        /** @var \ThemeHouse\AttachmentsPlus\Repository\AttachmentIcon $attachmentIconRepo */
        $attachmentIconRepo = $this->repository('ThemeHouse\AttachmentsPlus:AttachmentIcon');
        $attachmentIconFinder = $attachmentIconRepo->findAttachmentIcons();

        $viewParams = [
            'attachmentIcons' => $attachmentIconFinder->fetch()
        ];


        return $this->view('ThemeHouse\AttachmentsPlus:AttachmentIcon\List', 'thattachmentsplus_attachment_icon_list',
            $viewParams);
    }

    /**
     * @param AttachmentIcon $attachmentIcon
     * @return \XF\Mvc\Reply\View
     */
    protected function attachmentIconAddEdit(AttachmentIcon $attachmentIcon)
    {
        $viewParams = [
            'attachmentIcon' => $attachmentIcon
        ];

        return $this->view('ThemeHouse\AttachmentsPlus:AttachmentIcon\Edit', 'thattachmentsplus_attachment_icon_edit',
            $viewParams);
    }

    /**
     * @return \XF\Mvc\Reply\Error|\XF\Mvc\Reply\View
     */
    public function actionAdd()
    {
        if ($this->isPost()) {
            $fileExtension = $this->filter('file_extension', 'str');

            $existingIcon = $this->em()->find('ThemeHouse\AttachmentsPlus:AttachmentIcon', $fileExtension);
            if ($existingIcon) {
                return $this->error(\XF::phrase('thattachmentsplus_icon_for_extension_already_exists'));
            }

            /** @var AttachmentIcon $attachmentIcon */
            $attachmentIcon = $this->em()->create('ThemeHouse\AttachmentsPlus:AttachmentIcon');
            $attachmentIcon->file_extension = $fileExtension;

            return $this->attachmentIconAddEdit($attachmentIcon);
        }

        return $this->view('ThemeHouse\AttacmentsPlus:AttachmentIcon\Add', 'thattachmentsplus_attachment_icon_add');
    }

    /**
     * @param ParameterBag $params
     * @return \XF\Mvc\Reply\View
     * @throws \XF\Mvc\Reply\Exception
     */
    public function actionEdit(ParameterBag $params)
    {
        $attachmentIcon = $this->assertAttachmentIconExists($params['file_extension']);
        return $this->attachmentIconAddEdit($attachmentIcon);
    }

    /**
     * @param AttachmentIcon $attachmentIcon
     * @return \XF\Mvc\FormAction
     */
    protected function attachmentIconSaveProcess(AttachmentIcon $attachmentIcon) {
        $form = $this->formAction();

        $input = $this->filter([
            'file_extension' => 'str',
            'type' => 'str',
            'extra_css' => 'str'
        ]);

        $data = $this->filter('data', 'array-str');
        $input['data'] = $data[$input['type']];

        $form->basicEntitySave($attachmentIcon, $input);

        return $form;
    }

    /**
     * @param ParameterBag $params
     * @return \XF\Mvc\Reply\Redirect
     * @throws \XF\PrintableException
     */
    public function actionSave(ParameterBag $params) {
        $fileExtension = $this->filter('file_extension', 'str');
        $attachmentIcon = $this->em()->find('ThemeHouse\AttachmentsPlus:AttachmentIcon', $fileExtension);

        if(!$attachmentIcon) {
            /** @var AttachmentIcon $attachmentIcon */
            $attachmentIcon = $this->em()->create('ThemeHouse\AttachmentsPlus:AttachmentIcon');
            $attachmentIcon->file_extension = $fileExtension;
        }

        $this->attachmentIconSaveProcess($attachmentIcon)->run();

        return $this->redirect($this->buildLink('th-attachment-icons'));
    }

    /**
     * @param ParameterBag $params
     * @return \XF\Mvc\Reply\Error|\XF\Mvc\Reply\Redirect|\XF\Mvc\Reply\View
     * @throws \XF\Mvc\Reply\Exception
     */
    public function actionDelete(ParameterBag $params)
    {
        $icon = $this->assertAttachmentIconExists($params['file_extension']);

        /** @var \XF\ControllerPlugin\Delete $plugin */
        $plugin = $this->plugin('XF:Delete');
        return $plugin->actionDelete(
            $icon,
            $this->buildLink('th-attachment-icons/delete', $icon),
            $this->buildLink('th-attachment-icons/edit', $icon),
            $this->buildLink('th-attachment-icons'),
            $icon->file_extension
        );

    }

    /**
     * @param $id
     * @param null $with
     * @param null $phraseKey
     * @return \XF\Mvc\Entity\Entity|AttachmentIcon
     * @throws \XF\Mvc\Reply\Exception
     */
    protected function assertAttachmentIconExists($id, $with = null, $phraseKey = null)
    {
        return $this->assertRecordExists('ThemeHouse\AttachmentsPlus:AttachmentIcon', $id, $with, $phraseKey);
    }
}
