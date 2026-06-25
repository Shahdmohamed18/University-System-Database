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
    $level = $_POST['level'];
    $gpa = $_POST['gpa'];
    $majorID = $_POST['majorID'];
    
    $sql = "INSERT INTO student (id, ssn, fname, lname, address, sex, `date_of_birth`, level, gpa, status, majorID) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active', ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissssssdi", $id, $ssn, $fname, $lname, $address, $sex, $date_of_birth, $level, $gpa, $majorID);
    
    if ($stmt->execute()) {
        echo "Student added successfully!";
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
    $level = $_POST['level'];
    $gpa = $_POST['gpa'];
    $majorID = $_POST['majorID'];
    
    $sql = "UPDATE student SET ssn=?, fname=?, lname=?, address=?, sex=?, `date_of_birth`=?, level=?, gpa=?, majorID=? WHERE id=?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssdsi", $ssn, $fname, $lname, $address, $sex, $date_of_birth, $level, $gpa, $majorID, $id);
    
    if ($stmt->execute()) {
        echo "Student updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
elseif ($action === 'delete') {
    $student_id = $_POST['student_id'];
    
    $sql1 = "DELETE FROM std_contact WHERE id = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("i", $student_id);
    $stmt1->execute();
    
    $sql2 = "DELETE FROM enrollment WHERE stdID = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $student_id);
    $stmt2->execute();
    
    $sql3 = "DELETE FROM student WHERE id = ?";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("i", $student_id);
    
    if ($stmt3->execute()) {
        echo "Student deleted successfully!";
    } else {
        echo "Error: " . $stmt3->error;
    }
}
elseif ($action === 'search') {
    $search = $_GET['q'];
if (is_numeric($search)) {
    $sql = "SELECT s.id, s.ssn, s.fname, s.lname, s.address, s.sex, s.`date_of_birth` as `dob`, s.level, s.gpa, s.status, m.name as major_name 
            FROM student s 
            LEFT JOIN major m ON s.majorID = m.id 
            WHERE s.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $search);
} else {
    $sql = "SELECT s.id, s.ssn, s.fname, s.lname, s.address, s.sex, s.`date_of_birth` as `dob`, s.level, s.gpa, s.status, m.name as major_name 
            FROM student s 
            LEFT JOIN major m ON s.majorID = m.id 
            WHERE s.fname LIKE ? OR s.lname LIKE ? OR s.ssn LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_term = "%$search%";
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
}

$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode($students);
exit;
}
elseif ($action === 'get') {
    $id = $_GET['id'];
    $sql = "SELECT s.*, s.`date_of_birth` as dob FROM student s WHERE s.id = ?";
    
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
    $sql = "SELECT s.id, s.ssn, s.fname, s.lname, s.address, s.sex, s.`date_of_birth` as `dob`, s.level, s.gpa, s.status, m.name as major_name 
            FROM student s 
            LEFT JOIN major m ON s.majorID = m.id 
            ORDER BY s.id";
    
    $result = $conn->query($sql);
    
    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    
    if (isset($_GET['ajax'])) {
        echo json_encode($students);
        exit;
    }
}
?>