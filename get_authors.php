<?php
require_once("./connect.php");
extract($_POST);

// Total count without filtering
$totalCount = $conn->query("SELECT * FROM `authors`")->num_rows;

// Initialize search condition
$search_where = "";
if (!empty($search)) {
    $search_value = $conn->real_escape_string($search['value']);
    $search_where = "WHERE 
        first_name LIKE '%$search_value%' OR 
        last_name LIKE '%$search_value%' OR 
        email LIKE '%$search_value%' OR 
        DATE_FORMAT(birthdate, '%M %d, %Y') LIKE '%$search_value%'";
}

// Define columns for ordering
$columns_arr = [
    "id",
    "first_name",
    "last_name",
    "email",
    "unix_timestamp(birthdate)"
];

// Build main query with ordering and pagination
$order_column_index = (int)$order[0]['column'];
$order_dir = strtoupper($order[0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
$sort_column = $columns_arr[$order_column_index];

$sql = "SELECT * FROM `authors` 
        $search_where 
        ORDER BY $sort_column $order_dir 
        LIMIT $length OFFSET $start";

$query = $conn->query($sql);

// Get filtered record count
$recordsFilterCount = $conn->query("SELECT * FROM `authors` $search_where")->num_rows;

// Prepare response
$data = [];
$index = 1 + $start;

while ($row = $query->fetch_assoc()) {
    $row['no'] = $index++;
    $row['birthdate'] = date("F d, Y", strtotime($row['birthdate']));
    $data[] = $row;
}

// Send JSON response
echo json_encode([
    'draw' => intval($draw),
    'recordsTotal' => $totalCount,
    'recordsFiltered' => $recordsFilterCount,
    'data' => $data
]);

