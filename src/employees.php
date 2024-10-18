<!DOCTYPE html>
<?php
    // PHP Code as provided
    include "db_conn.php";

    if (isset($_POST['insert_record'])) {
        // PHP code to handle form submission
        $employeeID = $_POST['employeeID'];
        $name = $_POST['name'];
        $role = $_POST['role'];
        $storeID = $_POST['storeID'];

        $insert = oci_parse($conn, 'INSERT INTO Employee (EmployeeID, Name, Role, StoreID) VALUES (:employeeID, :name, :role, :storeID)');
        oci_bind_by_name($insert, ':employeeID', $employeeID);
        oci_bind_by_name($insert, ':name', $name);
        oci_bind_by_name($insert, ':role', $role);
        oci_bind_by_name($insert, ':storeID', $storeID);
        
        $execute = oci_execute($insert);

        if ($execute) {
            oci_execute(oci_parse($conn, 'COMMIT'));
        }

        oci_free_statement($insert);
    }

    // PHP code to handle search and fetching data
    $selected_field = isset($_GET['field']) ? $_GET['field'] : null;
    $search_value = isset($_GET['search']) ? $_GET['search'] : null;

    $query = 'SELECT * FROM EMPLOYEE';
    if ($selected_field && $search_value) {
        $query .= ' WHERE ' . $selected_field . ' LIKE :search_value';
    }
    $query .= ' ORDER BY EmployeeID ASC';

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
    <link rel="stylesheet" type="text/css" href="./styles/employees.css"> <!-- Link to CSS file -->
</head>
<body>
    <div class="heading">
        <h1>Employees</h1>
    </div>

    <div class="search-div">
        <form action="employees.php" method="get">
            <label for="field">Search by Field:</label>
            <select name="field" id="field">
                <option value="">--Select Field--</option>
                <option value="EmployeeID">EmployeeID</option>
                <option value="Name">Name</option>
                <option value="Role">Role</option>
                <option value="StoreID">StoreID</option>
            </select>

            <label for="search">Search:</label>
            <input type="text" name="search" id="search" />

            <button type="submit">Search</button>
        </form>
    </div>

    <div class="result_table">
        <?php
            echo "<table>"; 
            echo "<tr><th>EmployeeID</th><th>Name</th><th>Role</th><th>StoreID</th></tr>"; 

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
        <h2>Adding New Employee</h2>
        <form action="employees.php" method="post" id="form">
            <label for="employeeID">EmployeeID</label><br>
            <input type="number" name="employeeID" /><br><br>

            <label for="name">Name</label><br>
            <input type="text" name="name" /><br><br>

            <label for "role">Role</label><br>
            <input type="text" name="role" /><br><br>

            <label for="storeID">StoreID</label><br>
            <input type="number" name="storeID" /><br><br>

            <input type="submit" name="insert_record" value="Add Employee"/>
        </form>
    </div>
</body>
</html>
