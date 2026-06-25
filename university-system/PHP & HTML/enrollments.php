<?php
include 'config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'create') {
    $enrollment_id = $_POST['enrollment_id'];
    $student_id = $_POST['student_id'];
    $course_code = $_POST['course_code'];
    $semester = $_POST['semester'];
    $year = $_POST['year'];
    $grade = $_POST['grade'];
    $status = $_POST['status'];
    
    $sql = "INSERT INTO enrollment (id, stdID, courseCode, semester, year, grade, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisssss", $enrollment_id, $student_id, $course_code, $semester, $year, $grade, $status);
    
    if ($stmt->execute()) {
        echo "Enrollment added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
elseif ($action === 'update') {
    $id = $_POST['id'];
    $stdID = $_POST['stdID'];
    $courseCode = $_POST['courseCode'];
    $semester = $_POST['semester'];
    $year = $_POST['year'];
    $grade = $_POST['grade'];
    $status = $_POST['status'];
    
    $sql = "UPDATE enrollment SET stdID=?, courseCode=?, semester=?, year=?, grade=?, status=? WHERE id=?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssi", $stdID, $courseCode, $semester, $year, $grade, $status, $id);
    
    if ($stmt->execute()) {
        echo "Enrollment updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
elseif ($action === 'delete') {
    $enrollment_id = $_POST['enrollment_id'];
    
    $sql = "DELETE FROM enrollment WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $enrollment_id);
    
    if ($stmt->execute()) {
        echo "Enrollment deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
elseif ($action === 'search') {
    $search = $_GET['q'];
if (is_numeric($search)) {
    $sql = "SELECT e.id, s.fname, s.lname, c.name as course_name, 
                   e.semester, e.year, e.grade, e.status 
            FROM enrollment e 
            JOIN student s ON e.stdID = s.id 
            JOIN course c ON e.courseCode = c.code 
            WHERE e.id = ? OR s.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $search, $search);
} else {
    $sql = "SELECT e.id, s.fname, s.lname, c.name as course_name, 
                   e.semester, e.year, e.grade, e.status 
            FROM enrollment e 
            JOIN student s ON e.stdID = s.id 
            JOIN course c ON e.courseCode = c.code 
            WHERE s.fname LIKE ? OR s.lname LIKE ? OR c.name LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_term = "%$search%";
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
}

$stmt->execute();
$result = $stmt->get_result();

$enrollments = [];
while ($row = $result->fetch_assoc()) {
    $enrollments[] = $row;
}

echo json_encode($enrollments);
exit;
}
elseif ($action === 'get') {
    $id = $_GET['id'];
    $sql = "SELECT * FROM enrollment WHERE id = ?";
    
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
    $sql = "SELECT e.id, s.fname, s.lname, c.name as course_name, 
                   e.semester, e.year, e.grade, e.status 
            FROM enrollment e 
            JOIN student s ON e.stdID = s.id 
            JOIN course c ON e.courseCode = c.code 
            ORDER BY e.id";
    $result = $conn->query($sql);
    
    $enrollments = [];
    while ($row = $result->fetch_assoc()) {
        $enrollments[] = $row;
    }
    
    if (isset($_GET['ajax'])) {
        echo json_encode($enrollments);
        exit;
    }
}
?>