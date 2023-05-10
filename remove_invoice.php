<?php
require_once "functions.php";

if (isset($_GET["invoice_item_id"])) {
    $invoice_item_id = $_GET["invoice_item_id"];
} else {
    echo "Invoice item ID is missing.";
    exit();
}

try {
    $db = openDb();
    $statement = $db->prepare("
        DELETE FROM invoice_items
        WHERE InvoiceLineId = ?
    ");
    $statement->execute([$invoice_item_id]);
    echo "Invoice item deleted successfully.";
} catch (PDOException $e) {
    echo "Error deleting invoice item: " . $e->getMessage();
}
