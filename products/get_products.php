<?php
include("../config/db.php");

$result = $conn->query("SELECT * FROM products");

$products = [];

while ($row = $result->fetch_assoc()) {

    if (!empty($row['image_data'])) {

        // ✅ REMOVE DUPLICATE PREFIX IF EXISTS
        if (strpos($row['image_data'], 'data:image') === false) {
            $row['image_data'] = "data:image/jpeg;base64," . $row['image_data'];
        }

        // ✅ FIX DOUBLE PREFIX (IMPORTANT)
        $row['image_data'] = str_replace(
            "data:image/jpeg;base64,data:image/jpeg;base64,",
            "data:image/jpeg;base64,",
            $row['image_data']
        );
    }

    $products[] = $row;
}

echo json_encode($products);
?>