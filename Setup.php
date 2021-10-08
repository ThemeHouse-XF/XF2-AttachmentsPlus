<?php

namespace ThemeHouse\AttachmentsPlus;

use ThemeHouse\AttachmentsPlus\Repository\AttachmentIcon;
use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;

/**
 * Class Setup
 * @package ThemeHouse\AttachmentsPlus
 */
class Setup extends AbstractSetup
{
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;

    /**
     *
     */
    public function installStep1()
    {
        $this->schemaManager()->createTable('xf_th_attachmentsplus_attachment_icon', function (Create $table) {
            $table->addColumn('file_extension', 'varchar', 10);
            $table->addColumn('type', 'enum')->values(['image', 'icon', 'text']);
            $table->addColumn('extra_css', 'text');
            $table->addColumn('data', 'blob');
            $table->addPrimaryKey('file_extension');
        });
    }

    /**
     * @throws \XF\PrintableException
     */
    public function installStep2()
    {
        $boardUrl = \XF::options()->boardUrl;
        $this->db()->insertBulk('xf_th_attachmentsplus_attachment_icon', [
            [
                'file_extension' => 'apk',
                'type' => 'image',
                'extra_css' => '',
                'data' => '"' . $boardUrl . "/styles/attachment-icons/apk.svg\""
            ],
            [
                'file_extension' => 'xml',
                'type' => 'image',
                'extra_css' => '',
                'data' => '"' . $boardUrl . "/styles/attachment-icons/xml.svg\""
            ],
            [
                'file_extension' => 'xlsx',
                'type' => 'image',
                'extra_css' => '',
                'data' => '"' . $boardUrl . "/styles/attachment-icons/xlsx.svg\""
            ],
            [
                'file_extension' => 'xls',
                'type' => 'image',
                'extra_css' => '',
                'data' => '"' . $boardUrl . "/styles/attachment-icons/xls.svg\""
            ],
            [
                'file_extension' => 'wav',
                'type' => 'image',
                'extra_css' => '',
                'data' => '"' . $boardUrl . "/styles/attachment-icons/wav.svg\""
            ],
            [
                'file_extension' => 'txt',
                'type' => 'image',
                'extra_css' => '',
                'data' => '"' . $boardUrl . "/styles/attachment-icons/txt.svg\""
            ],
            [
                'file_extension' => 'torrent',
                'type' => 'image',
                'extra_css' => '',
                'data' => '"' . $boardUrl . "/styles/attachment-icons/torrent.svg\""
            ],
            [
                'file_extension' => 'rtf',
                'type' => 'image',
                'extra_css' => '',
                'data' => '"' . $boardUrl . "/styles/attachment-icons/rtf.svg\""
            ],
            [
                'file_extension' => 'rar',
                'type' => 'image',
                'extra_css' => '',
                'data' => '"' . $boardUrl . "/styles/attachment-icons/rar.svg\""
            ],
            [
                'file_extension' => 'php',
                'type' => 'image',
                'extra_css' => '',
                'data' => '"' . $boardUrl . "/styles/attachment-icons/php.svg\""
            ],
            [
                'file_extension' => 'pdf',
                'type' => 'image',
                'extra_css' => '',
                'data' => '"' . $boardUrl . "/styles/attachment-icons/pdf.svg\""
            ],
            [
                'file_extension' => 'mp3',
                'type' => 'image',
                'extra_css' => '',
                'data' => '"' . $boardUrl . "/styles/attachment-icons/mp3.svg\""
            ],
            [
                'file_extension' => 'js',
                'type' => 'image',
                'extra_css' => '',
                'data' => '"' . $boardUrl . "/styles/attachment-icons/js.svg\""
            ],
            [
                'file_extension' => 'docx',
                'type' => 'image',
                'extra_css' => '',
                'data' => '"' . $boardUrl . "/styles/attachment-icons/docx.svg\""
            ],
            [
                'file_extension' => 'doc',
                'type' => 'image',
                'extra_css' => '',
                'data' => '"' . $boardUrl . "/styles/attachment-icons/doc.svg\""
            ],
            [
                'file_extension' => 'css',
                'type' => 'image',
                'extra_css' => '',
                'data' => '"' . $boardUrl . "/styles/attachment-icons/css.svg\""
            ],
            [
                'file_extension' => 'zip',
                'type' => 'image',
                'extra_css' => '',
                'data' => '"' . $boardUrl . "/styles/attachment-icons/zip.svg\""
            ],
        ]);

        /** @var AttachmentIcon $repo */
        $repo = \XF::repository('ThemeHouse\AttachmentsPlus:AttachmentIcon');
        $repo->rebuildAttachmentIconCache();
    }

    public function installStep3()
    {
        $this->schemaManager()->alterTable('xf_attachment', function (Alter $table) {
            $table->addKey('content_type');
        });
    }

    public function installStep4()
    {
        $this->schemaManager()->alterTable('xf_attachment_data', function (Alter $table) {
            $table->addKey('user_id');
            $table->addKey('thumbnail_width');
        });
    }

    /**
     * @param array $stateChanges
     */
    public function postInstall(array &$stateChanges)
    {
        $this->applyDefaultPermissions();
    }

    public function upgrade1000195Step1()
    {
        $this->schemaManager()->alterTable('xf_attachment', function (Alter $table) {
            $table->addKey('content_type');
        });
    }

    public function upgrade1000195Step2()
    {
        $this->schemaManager()->alterTable('xf_attachment_data', function (Alter $table) {
            $table->addKey('user_id');
            $table->addKey('thumbnail_width');
        });
    }

    public function postUpgrade($previousVersion, array &$stateChanges)
    {
        $this->applyDefaultPermissions($previousVersion);
    }

    protected function applyDefaultPermissions($previousVersion = 0)
    {
        if (!$previousVersion) {
            $this->applyGlobalPermission('thattachmentsplus', 'viewAttachmentManager');
            $this->applyGlobalPermissionInt('thattachmentsplus', 'maxAttachments', -1);
            $this->applyGlobalPermissionInt('thattachmentsplus', 'maxDiskSpace', -1);
        }

        if ($previousVersion < 1000131) {
            $this->applyGlobalPermission('thattachmentsplus', 'viewAttachmentTab');
        }


        $this->app->jobManager()->enqueueUnique(
            'permissionRebuild',
            'XF:PermissionRebuild',
            [],
            false
        );
    }

    /**
     *
     */
    public function uninstallStep1()
    {
        $this->schemaManager()->dropTable('xf_th_attachmentsplus_attachment_icon');
    }
}