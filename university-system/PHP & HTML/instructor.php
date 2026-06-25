<?php
include 'config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'create') {
    $id = $_POST['id'];
    $ssn = $_POST['ssn'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $address = $_POST['address'];
    $sex = $_POST['sex'];
    $date_of_birth = $_POST['date_of_birth'];
    $title = $_POST['title'];
    $salary = $_POST['salary'];
    
    $sql = "INSERT INTO instructor (id, ssn, fname, lname, address, sex, date_of_birth, title, salary) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissssssi", $id, $ssn, $fname, $lname, $address, $sex, $date_of_birth, $title, $salary);
    
    if ($stmt->execute()) {
        echo "Instructor added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
elseif ($action === 'update') {
    $id = $_POST['id'];
    $ssn = $_POST['ssn'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $address = $_POST['address'];
    $sex = $_POST['sex'];
    $date_of_birth = $_POST['date_of_birth'];
    $title = $_POST['title'];
    $salary = $_POST['salary'];
    
    $sql = "UPDATE instructor SET ssn=?, fname=?, lname=?, address=?, sex=?, date_of_birth=?, title=?, salary=? WHERE id=?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssii", $ssn, $fname, $lname, $address, $sex, $date_of_birth, $title, $salary, $id);
    
    if ($stmt->execute()) {
        echo "Instructor updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
elseif ($action === 'delete') {
    $instructor_id = $_POST['instructor_id'];
    
    $sql = "DELETE FROM instructor WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $instructor_id);
    
    if ($stmt->execute()) {
        echo "Instructor deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
elseif ($action === 'search') {
    $search = $_GET['q'];
if (is_numeric($search)) {
    $sql = "SELECT * FROM instructor WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $search);
} else {
    $sql = "SELECT * FROM instructor WHERE fname LIKE ? OR lname LIKE ? OR title LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_term = "%$search%";
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
}

$stmt->execute();
$result = $stmt->get_result();

$instructors = [];
while ($row = $result->fetch_assoc()) {
    $instructors[] = $row;
}

echo json_encode($instructors);
exit;
}
elseif ($action === 'get') {
    $id = $_GET['id'];
    $sql = "SELECT * FROM instructor WHERE id = ?";
    
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
    $sql = "SELECT * FROM instructor ORDER BY id";
    $result = $conn->query($sql);
    
    $instructors = [];
    while ($row = $result->fetch_assoc()) {
        $instructors[] = $row;
    }
    
    if (isset($_GET['ajax'])) {
        echo json_encode($instructors);
        exit;
    }
}
?>