<?php
require_once 'config.php';
session_start();
if(!isset($_SESSION['seller_logged_in'])) { echo json_encode(['error'=>'unauth']); exit; }
$code = trim($_GET['code'] ?? '');
$seller_id = (int)$_SESSION['seller_id'];
if(!$code){ echo json_encode(['error'=>'no_code']); exit; }
$db = db_connect();
$stmt = $db->prepare("SELECT * FROM seller_items WHERE seller_id=? AND item_code=? LIMIT 1");
$stmt->bind_param('is', $seller_id, $code);
$stmt->execute();
$res = $stmt->get_result();
if($res->num_rows===0){ echo json_encode(['error'=>'not_found']); exit; }
$item = $res->fetch_assoc();
echo json_encode(['item'=>$item]);
