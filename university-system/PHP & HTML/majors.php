<?php
include 'config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'create') {
    $major_id = $_POST['major_id'];
    $major_name = $_POST['major_name'];
    $SupervisorID = $_POST['SupervisorID'];
    
    $sql = "INSERT INTO major (id, name, SupervisorID) VALUES (?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $major_id, $major_name, $SupervisorID);
    
    if ($stmt->execute()) {
        echo "Major added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
elseif ($action === 'update') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $SupervisorID = $_POST['SupervisorID'];
    
    $sql = "UPDATE major SET name=?, SupervisorID=? WHERE id=?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $name, $SupervisorID, $id);
    
    if ($stmt->execute()) {
        echo "Major updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
elseif ($action === 'delete') {
    $major_id = $_POST['major_id'];
    
    $sql = "DELETE FROM major WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $major_id);
    
    if ($stmt->execute()) {
        echo "Major deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
elseif ($action === 'search') {
    $search = $_GET['q'];
if (is_numeric($search)) {
    $sql = "SELECT m.*, i.fname, i.lname as supervisor_name 
            FROM major m 
            JOIN instructor i ON m.SupervisorID = i.id 
            WHERE m.id = ? OR m.SupervisorID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $search, $search);
} else {
    $sql = "SELECT m.*, i.fname, i.lname as supervisor_name 
            FROM major m 
            JOIN instructor i ON m.SupervisorID = i.id 
            WHERE m.name LIKE ? OR i.fname LIKE ? OR i.lname LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_term = "%$search%";
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
}

$stmt->execute();
$result = $stmt->get_result();

$majors = [];
while ($row = $result->fetch_assoc()) {
    $majors[] = $row;
}

echo json_encode($majors);
exit;
}
elseif ($action === 'get') {
    $id = $_GET['id'];
    $sql = "SELECT * FROM major WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode([]);
    }
    exit;
}
else {
    $sql = "SELECT m.*, i.fname, i.lname as supervisor_name 
            FROM major m 
            JOIN instructor i ON m.SupervisorID = i.id 
            ORDER BY m.id";
    $result = $conn->query($sql);
    
    $majors = [];
    while ($row = $result->fetch_assoc()) {
        $majors[] = $row;
    }
    
    if (isset($_GET['ajax'])) {
        echo json_encode($majors);
        exit;
    }
}
?>