<?php
$db = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
$stmt = $db->query('SELECT * FROM transactions ORDER BY id DESC LIMIT 10');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows, JSON_PRETTY_PRINT);
