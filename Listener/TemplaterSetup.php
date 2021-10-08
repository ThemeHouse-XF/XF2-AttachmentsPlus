<?php

namespace ThemeHouse\AttachmentsPlus\Listener;

use XF\Container;
use XF\Template\Templater;

/**
 * Class TemplaterSetup
 * @package ThemeHouse\AttachmentsPlus\Listener
 */
class TemplaterSetup
{
    /**
     * @param Container $container
     * @param Templater $templater
     * @throws \Exception
     */
    public static function templaterSetup(Container $container, Templater &$templater)
    {
        /** @var \ThemeHouse\AttachmentsPlus\Template\TemplaterSetup $templaterSetup */
        $class = \XF::extendClass('ThemeHouse\AttachmentsPlus\Template\TemplaterSetup');
        $templaterSetup = new $class();

        $templater->addFunction('thattachplus_attachment_icon', [$templaterSetup, 'fnThAttachPlusAttachmentIcon']);
    }
}
