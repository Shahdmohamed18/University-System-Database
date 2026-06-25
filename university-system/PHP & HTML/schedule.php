<?php
include 'config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'create') {
    $schedule_id = $_POST['schedule_id'];
    $room_number = $_POST['room_number'];
    $course_code = $_POST['course_code'];
    $instructor_id = $_POST['instructor_id'];
    $day = $_POST['day'];
    $time = $_POST['time'];
    
    $sql = "INSERT INTO schedule (scheduleID, roomNum, courseCode, instID, day, time) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisiss", $schedule_id, $room_number, $course_code, $instructor_id, $day, $time);
    
    if ($stmt->execute()) {
        echo "Schedule added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
elseif ($action === 'delete') {
    $schedule_id = $_POST['schedule_id'];
    
    $sql = "DELETE FROM schedule WHERE scheduleID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $schedule_id);
    
    if ($stmt->execute()) {
        echo "Schedule deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
elseif ($action === 'search') {
    $search = $_GET['q'];
if (is_numeric($search)) {
    $sql = "SELECT s.scheduleID, c.name as course_name, r.num as room_number, 
                   i.fname, i.lname, s.day, s.time 
            FROM schedule s 
            JOIN course c ON s.courseCode = c.code 
            JOIN room r ON s.roomNum = r.num 
            JOIN instructor i ON s.instID = i.id 
            WHERE r.num = ? OR s.scheduleID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $search, $search);
} else {
    $sql = "SELECT s.scheduleID, c.name as course_name, r.num as room_number, 
                   i.fname, i.lname, s.day, s.time 
            FROM schedule s 
            JOIN course c ON s.courseCode = c.code 
            JOIN room r ON s.roomNum = r.num 
            JOIN instructor i ON s.instID = i.id 
            WHERE c.name LIKE ? OR i.fname LIKE ? OR i.lname LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_term = "%$search%";
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
}

$stmt->execute();
$result = $stmt->get_result();

$schedules = [];
while ($row = $result->fetch_assoc()) {
    $schedules[] = $row;
}

echo json_encode($schedules);
exit;
}
else {
    $sql = "SELECT s.scheduleID, c.name as course_name, r.num as room_number, 
                   i.fname, i.lname, s.day, s.time 
            FROM schedule s 
            JOIN course c ON s.courseCode = c.code 
            JOIN room r ON s.roomNum = r.num 
            JOIN instructor i ON s.instID = i.id 
            ORDER BY s.day, s.time";
    $result = $conn->query($sql);
    
    $schedules = [];
    while ($row = $result->fetch_assoc()) {
        $schedules[] = $row;
    }
    
    if (isset($_GET['ajax'])) {
        echo json_encode($schedules);
        exit;
    }
}
?>