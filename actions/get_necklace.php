<?php
require '../config/db_connect.php';
header('Content-Type: application/json');

try {
    $id = $_GET['id'];

    // Get necklace details
    $sql = "SELECT nd.*,np.shapeshape_necklace, np.proportions_size, np.proportions_width, np.proportions_thick 
    FROM necklace_detail nd 
    LEFT JOIN necklace_proportions np ON nd.necklace_detail_id = np.necklace_detail_id 
    WHERE nd.necklace_detail_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $necklace = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get TBS data
    $sql = "SELECT * FROM necklace_tbs WHERE necklace_detail_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $tbs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // เพิ่มข้อมูล TBS เข้าไปใน necklace
    $necklace['tbs'] = $tbs;

    echo json_encode([
        'success' => true,
        'necklace' => $necklace
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}