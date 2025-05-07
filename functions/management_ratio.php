<?php
function get_ratio_data($pdo)
{
    $stmt = $pdo->prepare("SELECT r.ratio_id, r.ratio_thick, r.ratio_data, r.ratio_size, r.ratio_gram, r.ratio_inch, u.first_name, r.updated_users_id , r.updated_at 
                            FROM ratio_data r 
                            LEFT JOIN users u ON r.updated_users_id = u.users_id 
                            ORDER BY r.ratio_id ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function get_ratio_master($pdo)
{
    $stmt = $pdo->prepare("SELECT r.ratio_id, r.ratio_thick, r.ratio_data, r.ratio_size, r.ratio_gram, r.ratio_inch, u.first_name, r.updated_users_id, r.updated_at 
                            FROM ratio_data r 
                            LEFT JOIN users u ON r.updated_users_id = u.users_id 
                            WHERE r.ratio_status = 'master'
                            ORDER BY r.ratio_id ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function get_ratio_By_id($pdo, $ratio_id)
{
    $stmt = $pdo->prepare("SELECT ratio_id, ratio_thick, ratio_data, ratio_size, ratio_gram, ratio_inch,updated_users_id, updated_at 
                          FROM ratio_data 
                          WHERE ratio_id = :ratio_id");
    $stmt->bindParam(':ratio_id', $ratio_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
// เพิ่มข้อมูล
function add_ratio($pdo, $ratio_thick, $ratio_data, $ratio_size, $ratio_gram, $ratio_inch, $updated_users_id)
{
    $stmt = $pdo->prepare("INSERT INTO ratio_data (ratio_thick, ratio_data, ratio_size, ratio_gram, ratio_inch,updated_users_id) 
                          VALUES (:ratio_thick, :ratio_data, :ratio_size, :ratio_gram, :ratio_inch , :updated_users_id)");
    $stmt->bindParam(':ratio_thick', $ratio_thick);
    $stmt->bindParam(':ratio_data', $ratio_data);
    $stmt->bindParam(':ratio_size', $ratio_size);
    $stmt->bindParam(':ratio_gram', $ratio_gram);
    $stmt->bindParam(':ratio_inch', $ratio_inch);
    $stmt->bindParam(':updated_users_id', $updated_users_id);

    return $stmt->execute();
}
// แก้ไขข้อมูล
function update_ratio($pdo, $ratio_id, $ratio_thick, $ratio_data, $ratio_size, $ratio_gram, $ratio_inch)
{
    $stmt = $pdo->prepare("UPDATE ratio_data 
                          SET ratio_thick = :ratio_thick, ratio_data = :ratio_data, ratio_size = :ratio_size, 
                              ratio_gram = :ratio_gram, ratio_inch = :ratio_inch
                          WHERE ratio_id = :ratio_id");
    $stmt->bindParam(':ratio_id', $ratio_id);
    $stmt->bindParam(':ratio_thick', $ratio_thick);
    $stmt->bindParam(':ratio_data', $ratio_data);
    $stmt->bindParam(':ratio_size', $ratio_size);
    $stmt->bindParam(':ratio_gram', $ratio_gram);
    $stmt->bindParam(':ratio_inch', $ratio_inch);
    return $stmt->execute();
}
// ลบข้อมูล
function delete_ratio($pdo, $ratio_id)
{
    $stmt = $pdo->prepare("DELETE FROM ratio_data WHERE ratio_id = :ratio_id");
    $stmt->bindParam(':ratio_id', $ratio_id);
    return $stmt->execute();
}
