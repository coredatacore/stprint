<?php
require_once 'config.php';
session_start();
header('Content-Type: application/json; charset=utf-8');
if(!isset($_SESSION['seller_logged_in'])){ echo json_encode(['error'=>'unauth']); exit;}
$action = $_POST['action'] ?? '';
$item_id = (int)($_POST['item_id'] ?? 0);
$seller_id = (int)$_SESSION['seller_id'];
$db = db_connect();

if($action === 'sold' && $item_id){
    // decrease qty, increase sold_count, add sale record
    $price = floatval($_POST['price'] ?? 0);
    $db->begin_transaction();
    try {
        $stmt = $db->prepare("SELECT quantity FROM seller_items WHERE id=? AND seller_id=? FOR UPDATE");
        $stmt->bind_param('ii',$item_id,$seller_id); $stmt->execute();
        $stmt->bind_result($qty); if(!$stmt->fetch()){ throw new Exception('Item not found'); } $stmt->close();
        if($qty <= 0) throw new Exception('Out of stock');
        $stmt2 = $db->prepare("UPDATE seller_items SET quantity = quantity - 1, sold_count = sold_count + 1 WHERE id=? AND seller_id=?");
        $stmt2->bind_param('ii',$item_id,$seller_id); $stmt2->execute(); $stmt2->close();

        $total = $price;
        $ins = $db->prepare("INSERT INTO seller_sales (seller_id,item_id,quantity,total_price) VALUES (?,?,?,?)");
        $one = 1; $ins->bind_param('iiid', $seller_id, $item_id, $one, $total); $ins->execute(); $ins->close();
        $db->commit();
        echo json_encode(['ok'=>true]);
    } catch(Exception $e) { $db->rollback(); echo json_encode(['error'=>$e->getMessage()]); }
    exit;
}

if($action === 'returned' && $item_id){
    $db->begin_transaction();
    try{
        $stmt = $db->prepare("UPDATE seller_items SET quantity = quantity + 1, sold_count = GREATEST(sold_count-1,0) WHERE id=? AND seller_id=?");
        $stmt->bind_param('ii',$item_id,$seller_id); $stmt->execute(); $stmt->close();
        // optionally: insert reverse sale record (not implemented)
        $db->commit();
        echo json_encode(['ok'=>true]);
    } catch(Exception $e){ $db->rollback(); echo json_encode(['error'=>$e->getMessage()]); }
    exit;
}

echo json_encode(['error'=>'invalid']);
