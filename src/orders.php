<!DOCTYPE html>
<?php
    // PHP code to connect to Oracle database
    include "db_conn.php";

    function order_exists($conn, $orderID) {
        // Query to check if the order with the given OrderID exists
        $query = oci_parse($conn, 'SELECT COUNT(*) AS COUNT FROM Orders WHERE OrderID = :orderID');
        oci_bind_by_name($query, ':orderID', $orderID);
        oci_execute($query);
        $result = oci_fetch_assoc($query);
        oci_free_statement($query);
        return $result['COUNT'] > 0;  // Returns true if count is greater than 0
    }

    if (isset($_POST['insert_record'])) {
        // Collect form inputs
        $orderID = $_POST['orderID'];
        $productID = $_POST['productID'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $customerName = $_POST['customerName'];
        $storeID = $_POST['storeID'];
        $supplierID = $_POST['supplierID'];
        $supplyDate = $_POST['supplyDate'];

        // Check if the order with this OrderID already exists
        if (order_exists($conn, $orderID)) {
            echo "Error inserting record: OrderID already exists.";
        } else {
            // Convert supplyDate to the correct Oracle date format
            $supplyDateFormatted = date('d-M-Y', strtotime($supplyDate));

            // Prepare and bind the SQL query
            $insert = oci_parse($conn, 'INSERT INTO Orders (OrderID, ProductID, Description, Price, CustomerName, StoreID, SupplierID, SupplyDate) VALUES (:orderID, :productID, :description, :price, :customerName, :storeID, :supplierID, TO_DATE(:supplyDate, \'DD-MON-YYYY\'))');
            oci_bind_by_name($insert, ':orderID', $orderID);
            oci_bind_by_name($insert, ':productID', $productID);
            oci_bind_by_name($insert, ':description', $description);
            oci_bind_by_name($insert, ':price', $price);
            oci_bind_by_name($insert, ':customerName', $customerName);
            oci_bind_by_name($insert, ':storeID', $storeID);
            oci_bind_by_name($insert, ':supplierID', $supplierID);
            oci_bind_by_name($insert, ':supplyDate', $supplyDateFormatted);

            // Execute and handle errors
            $execute = oci_execute($insert);
            if ($execute) {
                oci_commit($conn);  // Commit the transaction on success
                echo "Record inserted successfully!";
            } else {
                $error = oci_error($insert);
                echo "Error inserting record: " . htmlentities($error['message']);  // Display error message
            }

            oci_free_statement($insert);  // Clean up
        }
    }

    // Code to handle search and fetching data
    $selected_field = isset($_GET['field']) ? $_GET['field'] : null;
    $search_value = isset($_GET['search']) ? $_GET['search'] : null;

    $query = 'SELECT * FROM Orders';
    if ($selected_field && $search_value) {
        $query .= ' WHERE ' . $selected_field . ' LIKE :search_value';
    }
    $query .= ' ORDER BY OrderID ASC';

    $stid = oci_parse($conn, $query);

    if ($selected_field && $search_value) {
        $search_like = '%' . $search_value . '%';
        oci_bind_by_name($stid, ':search_value', $search_like);
    }

    $execute = oci_execute($stid);
    if (!$execute) {
        $error = oci_error($stid);
        echo "Error in query execution: " . htmlentities($error['message']);
    }
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IDEA Furnishings</title>
    <link rel="stylesheet" type="text/css" href="./styles/orders.css"> 
</head>
<body>
    <div class="heading">
        <h1>Current Orders</h1>
    </div>

    <div class="search-div">
        <form action="orders.php" method="get">
            <label for="field">Search by Field:</label>
            <select name="field" id="field">
                <option value="OrderID">OrderID</option>
                <option value="ProductID">ProductID</option>
                <option value="Description">Description</option>
                <option value="Price">Price</option>
                <option value="CustomerName">CustomerName</option>
                <option value="StoreID">StoreID</option>
                <option value="SupplierID">SupplierID</option>
                <option value="SupplyDate">SupplyDate</option>
            </select>

            <label for="search">Search:</label>
            <input type="text" name="search" id="search" />

            <button type="submit">Search</button>
        </form>
    </div>

    <div class="result_table">
        <?php
            if ($execute) { 
                echo "<table>";
                echo "<tr><th>OrderID</th><th>ProductID</th><th>Description</th><th>Price</th><th>CustomerName</th><th>StoreID</th><th>SupplierID</th><th>SupplyDate</th></tr>"; 

                while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
                    echo "<tr>\n";
                    foreach ($row as $item) {
                        echo "<td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
                    }
                    echo "</tr>\n";
                }
                echo "</table>\n";
            }
        ?>
    </div>

    <div class="form-div">
        <h2>Add a New Order</h2> 
        <form action="orders.php" method="post" id="form">
            <label for="orderID">OrderID</label><br>
            <input type="number" name="orderID" required /><br><br>

            <label for="productID">ProductID</label><br>
            <input type="number" name="productID" required /><br><br>

            <label for="description">Description</label><br>
            <input type="text" name="description" required /><br><br>

            <label for "price">Price</label><br>
            <input type="number" step="0.01" name="price" required /><br><br>

            <label for="customerName">CustomerName</label><br>
            <input type="text" name="customerName" required /><br><br>

            <label for="storeID">StoreID</label><br>
            <input type="number" name="storeID" required /><br><br>

            <label for="supplierID">SupplierID</label><br>
            <input type="number" name="supplierID" required /><br><br>

            <label for "supplyDate">SupplyDate</label><br>
            <input type="date" name="supplyDate" required /><br><br>

            <input type="submit" name="insert_record" value="Add New Order"/> 
        </form>
    </div>
</body>
</html>
