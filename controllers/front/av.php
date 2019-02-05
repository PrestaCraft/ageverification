<?php
/**
 * This software is provided "as is" without warranty of any kind.
 *
 * Visit my website (http://prestacraft.com) for future updates, new articles and other awesome modules.
 *
 * @author     PrestaCraft
 * @copyright  PrestaCraft
 */

$require = array(
    'classes/AgeVerificationDb.php',
);

foreach ($require as $item) {
    require_once(_PS_MODULE_DIR_.'ageverification/'.$item);
}

class AgeverificationAvModuleFrontController extends ModuleFrontController
{
    public $php_self = 'av';

    public function initContent()
    {
        $this->context->cookie->__set('ageverification_validated', true);
        $this->context->cookie->write();
    }

}