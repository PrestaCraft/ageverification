<?php

require_once('../../config/config.inc.php');

$secret = Tools::getValue("secret");
$nr = (int)Tools::getValue("nr");

if ($secret != Configuration::get("AGEVERIFICATION_SECRET")) {
    echo "Wrong security token.";
} else {
    $cnt = Db::getInstance()->getValue('SELECT count(*) FROM '._DB_PREFIX_.'ageverification');

    if ($cnt < $nr) {
        echo "Number of rows in the database is less than {$nr}. Removing was not needed this time.";
    } else {
        $cnt = Db::getInstance()->executeS('SELECT id_ageverification FROM '._DB_PREFIX_.'ageverification 
        ORDER BY `date` ASC LIMIT '.(int)$nr.'');

        if (count($cnt) > 0) {
            $list = '';

            foreach ($cnt as $row) {
                $list .= $row['id_ageverification'].',';
            }

            $list = rtrim($list, ',');

            Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'ageverification 
            WHERE id_ageverification IN('.$list.')');

            echo "{$nr} rows were deleted.";
        } else {
            echo "Empty database - no rows to delete.";
        }
    }
}

echo "<br /><br />-----<br /><br />";
echo "Thanks for using PrestaCraft module. If you like this module, please donate.";