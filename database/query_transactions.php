<?php
$db = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
$stmt = $db->query('SELECT count(*) as c FROM transactions');
$r = $stmt->fetch(PDO::FETCH_ASSOC);
echo $r['c'] ?? '0';
