<?php 
    if (isset($_POST['imgid'])) {
        include '../config.php';
        $stmt = $dbcon->prepare("UPDATE `images` SET broken = 1 WHERE id = :id");
        $stmt->bindParam(':id', $_POST['imgid']);
        $stmt->execute();
    } else {
        echo "No SRC Passed";
    }
?>