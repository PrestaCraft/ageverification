<?php
/**
 * This software is provided "as is" without warranty of any kind.
 *
 * Visit my website (http://prestacraft.com) for future updates, new articles and other awesome modules.
 *
 * @author     PrestaCraft
 * @copyright  PrestaCraft
 */

class AgeVerificationDb extends ObjectModel
{
    public $token;
    public $accepted;
    public $date;

    public static $definition = array(
        'table' => 'ageverification',
        'primary' => 'id_ageverification',
        'fields' => array(
            'token' => array('type' => self::TYPE_STRING, 'required' => true),
            'accepted' => array('type' => self::TYPE_INT, 'required' => true),
            'date' => array('type' => self::TYPE_STRING),
        ),
    );

    public static function checkByToken($token)
    {
        return (bool)Db::getInstance()->getValue(
          'SELECT accepted 
          FROM '._DB_PREFIX_.'ageverification 
          WHERE token="'.$token.'"'
        );
    }

    public static function count()
    {
        return Db::getInstance()->getValue('SELECT count(*) FROM '._DB_PREFIX_.'ageverification');
    }
}