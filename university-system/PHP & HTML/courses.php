<?php
include 'config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'create') {
    $course_code = $_POST['course_code'];
    $course_name = $_POST['course_name'];
    $credits = $_POST['credits'];
    
    $sql = "INSERT INTO course (code, name, credits) VALUES (?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $course_code, $course_name, $credits);
    
    if ($stmt->execute()) {
        echo "Course added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
elseif ($action === 'update') {
    $code = $_POST['code'];
    $name = $_POST['name'];
    $credits = $_POST['credits'];
    
    $sql = "UPDATE course SET name=?, credits=? WHERE code=?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sis", $name, $credits, $code);
    
    if ($stmt->execute()) {
        echo "Course updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
elseif ($action === 'delete') {
    $course_code = $_POST['course_code'];
    
    $sql = "DELETE FROM course WHERE code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $course_code);
    
    if ($stmt->execute()) {
        echo "Course deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
elseif ($action === 'search') {
    $search = $_GET['q'];
if (is_numeric($search)) {
    $sql = "SELECT c.*, i.fname, i.lname 
            FROM course c 
            LEFT JOIN schedule s ON c.code = s.courseCode 
            LEFT JOIN instructor i ON s.instID = i.id 
            WHERE c.code = ? 
            GROUP BY c.code";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $search);
} else {
    $sql = "SELECT c.*, i.fname, i.lname 
            FROM course c 
            LEFT JOIN schedule s ON c.code = s.courseCode 
            LEFT JOIN instructor i ON s.instID = i.id 
            WHERE c.name LIKE ? 
            GROUP BY c.code";
    $stmt = $conn->prepare($sql);
    $search_term = "%$search%";
    $stmt->bind_param("s", $search_term);
}

$stmt->execute();
$result = $stmt->get_result();

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

echo json_encode($courses);
exit;
}
elseif ($action === 'get') {
    $code = $_GET['code'];
    $sql = "SELECT * FROM course WHERE code = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $code);
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
    $sql = "SELECT c.*, i.fname, i.lname 
            FROM course c 
            LEFT JOIN schedule s ON c.code = s.courseCode 
            LEFT JOIN instructor i ON s.instID = i.id 
            GROUP BY c.code 
            ORDER BY c.code";
    $result = $conn->query($sql);
    
    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
    
    if (isset($_GET['ajax'])) {
        echo json_encode($courses);
        exit;
    }
}
?>