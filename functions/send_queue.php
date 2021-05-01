<?php

require_once __DIR__.'/config.php';

$sms_handle = new SMS_data_handle($PDO);
$sms_handle->send();

/*

* * * * * ( php /home/admin/web/smsc.duncat.net/public_shtml/functions/send_queue.php )
* * * * * ( sleep 10 ; php /home/admin/web/smsc.duncat.net/public_shtml/functions/send_queue.php )
* * * * * ( sleep 20 ; php /home/admin/web/smsc.duncat.net/public_shtml/functions/send_queue.php )
* * * * * ( sleep 30 ; php /home/admin/web/smsc.duncat.net/public_shtml/functions/send_queue.php )
* * * * * ( sleep 40 ; php /home/admin/web/smsc.duncat.net/public_shtml/functions/send_queue.php )
* * * * * ( sleep 50 ; php /home/admin/web/smsc.duncat.net/public_shtml/functions/send_queue.php )

*/
?>
