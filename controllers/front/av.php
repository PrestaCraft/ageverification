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
    'classes/AgeverificationDb.php',
);

foreach ($require as $item) {
    require_once(_PS_MODULE_DIR_.'/ageverification/'.$item);
}

class AgeverificationAvModuleFrontController extends ModuleFrontController
{
    public $php_self = 'av';

    public function initContent()
    {
        if (Tools::getToken() == Tools::getValue("token")) {
            $av = new AgeVerificationDb();
            $av->token = Tools::getToken();
            $av->accepted = 1;
            $av->date = date("Y-m-d H:i:s");
            $av->save();
        }

    }

}