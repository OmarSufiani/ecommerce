<?php

include 'db.php';




function getJerseys($conn) {
    $sql = "SELECT * FROM jerseys";
    return $conn->query($sql);
}

function getJersey($conn, $id) {
    $sql = "SELECT * FROM jerseys WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
?>