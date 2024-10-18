<!DOCTYPE html>
<?php
    // PHP Code as provided
    include "db_conn.php";

    if (isset($_POST['insert_record'])) {
        // PHP code to handle form submission
        $productID = $_POST['productID'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $type = $_POST['type'];
        $dimensions = $_POST['dimensions'];
        $weight = $_POST['weight'];

        $insert = oci_parse($conn, 'INSERT INTO Product (ProductID, Description, Price, Type, Dimensions, Weight) VALUES (:productID, :description, :price, :type, :dimensions, :weight)');
        oci_bind_by_name($insert, ':productID', $productID);
        oci_bind_by_name($insert, ':description', $description);
        oci_bind_by_name($insert, ':price', $price);
        oci_bind_by_name($insert, ':type', $type);
        oci_bind_by_name($insert, ':dimensions', $dimensions);
        oci_bind_by_name($insert, ':weight', $weight);
        
        $execute = oci_execute($insert);

        if ($execute) {
            oci_execute(oci_parse($conn, 'COMMIT'));
        }

        oci_free_statement($insert);
    }

    // PHP code to handle search and fetching data
    $selected_field = isset($_GET['field']) ? $_GET['field'] : null;
    $search_value = isset($_GET['search']) ? $_GET['search'] : null;

    $query = 'SELECT * FROM PRODUCT';
    if ($selected_field && $search_value) {
        $query .= ' WHERE ' . $selected_field . ' LIKE :search_value';
    }
    $query .= ' ORDER BY ProductID ASC';

    $stid = oci_parse($conn, $query);

    if ($selected_field && $search_value) {
        $search_like = '%' . $search_value . '%';
        oci_bind_by_name($stid, ':search_value', $search_like);
    }

    oci_execute($stid);
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IDEA Furnishings</title>
    <link rel="stylesheet" type="text/css" href="./styles/products.css"> <!-- Link to CSS file -->
</head>
<body>
    <div class="heading">
        <h1>Our Products</h1>
    </div>

    <div class="search-div">
        <form action="products.php" method="get">
            <label for="field">Search by Field:</label>
            <select name="field" id="field">
                <option value="">--Select Field--</option>
                <option value="ProductID">ProductID</option>
                <option value="Description">Description</option>
                <option value="Price">Price</option>
                <option value="Type">Type</option>
                <option value="Dimensions">Dimensions</option>
                <option value="Weight">Weight</option>
            </select>

            <label for="search">Search:</label>
            <input type="text" name="search" id="search" />

            <button type="submit">Search</button>
        </form>
    </div>

    <div class="result_table">
        <?php
            echo "<table>"; 
            echo "<tr><th>ProductID</th><th>Description</th><th>Price</th><th>Type</th><th>Dimensions</th><th>Weight</th></tr>"; 

            while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
                echo "<tr>\n";
                foreach ($row as $item) {
                    echo "<td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
                }
                echo "</tr>\n";
            }
            echo "</table>\n";
        ?>
    </div>

    <div class="form-div">
        <h2>Add a new Product</h2>
        <form action="products.php" method="post" id="form">
            <label for="productID">ProductID</label><br>
            <input type="number" name="productID" /><br><br>

            <label for="description">Description</label><br>
            <input type="text" name="description" /><br><br>

            <label for "price">Price</label><br>
            <input type="number" name="price" /><br><br>

            <label for="type">Type</label><br>
            <input type="text" name="type" /><br><br>

            <label for="dimensions">Dimensions</label><br>
            <input type="text" name="dimensions" /><br><br>

            <label for="weight">Weight</label><br>
            <input type="number" name="weight" /><br><br><br>

            <input type="submit" name="insert_record" value="Add Product"/>
        </form>
    </div>
</body>
</html>
