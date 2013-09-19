<?php

require_once 'apnspush.php';

$mongo  = new Mongo();
$db     = $mongo->development;

$collection     = $db->cdup_message;

$cursor     = $collection->find();

$push       = new ApnsPHP_Push ();

foreach ($cursor as $data) {
        echo $data['message']."\n";
        $push->send ($data['message'], $data['device_token']);
        $collection->remove (array ('_id' => $data['_id']));
}

echo "Push successed";

