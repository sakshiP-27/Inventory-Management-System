<!DOCTYPE html>
<?php
    // PHP Code as provided
    include "db_conn.php";

    if (isset($_POST['insert_record'])) {
        // PHP code to handle form submission
        $supplierID = $_POST['supplierID'];
        $name = $_POST['name'];
        $contactInfo = $_POST['contactInfo'];
        
        $insert = oci_parse($conn, 'INSERT INTO Supplier (SupplierID, Name, ContactInfo) VALUES (:supplierID, :name, :contactInfo)');
        oci_bind_by_name($insert, ':supplierID', $supplierID);
        oci_bind_by_name($insert, ':name', $name);
        oci_bind_by_name($insert, ':contactInfo', $contactInfo);
        
        $execute = oci_execute($insert);

        if ($execute) {
            oci_execute(oci_parse($conn, 'COMMIT'));
        }

        oci_free_statement($insert);
    }

    // PHP code to handle search and fetching data
    $selected_field = isset($_GET['field']) ? $_GET['field'] : null;
    $search_value = isset($_GET['search']) ? $_GET['search'] : null;

    $query = 'SELECT * FROM SUPPLIER';
    if ($selected_field && $search_value) {
        $query .= ' WHERE ' . $selected_field . ' LIKE :search_value';
    }
    $query .= ' ORDER BY SupplierID ASC';

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
    <link rel="stylesheet" type="text/css" href="./styles/suppliers.css"> <!-- Link to CSS file -->
</head>
<body>
    <div class="heading">
        <h1>Our Suppliers</h1>
    </div>

    <div class="search-div">
        <form action="suppliers.php" method="get">
            <label for="field">Search by Field:</label>
            <select name="field" id="field">
                <option value="">--Select Field--</option>
                <option value="SupplierID">SupplierID</option>
                <option value="Name">Name</option>
                <option value="ContactInfo">ContactInfo</option>
            </select>

            <label for="search">Search:</label>
            <input type="text" name="search" id="search" />

            <button type="submit">Search</button>
        </form>
    </div>

    <div class="result_table">
        <?php
            echo "<table>"; 
            echo "<tr><th>SupplierID</th><th>Name</th><th>ContactInfo</th></tr>"; 

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
        <h2>Add New Suppliers</h2>
        <form action="suppliers.php" method="post" id="form">
            <label for="supplierID">SupplierID</label><br>
            <input type="number" name="supplierID" /><br><br>

            <label for="name">Name</label><br>
            <input type="text" name="name" /><br><br>

            <label for "contactInfo">ContactInfo</label><br>
            <input type="text" name="contactInfo" /><br><br>

            <input type="submit" name="insert_record" value="Add Supplier"/>
        </form>
    </div>
</body>
</html>
