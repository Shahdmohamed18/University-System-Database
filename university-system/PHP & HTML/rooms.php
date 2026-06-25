<?php
include 'config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'create') {
    $room_number = $_POST['room_number'];
    $floor = $_POST['floor'];
    $building = $_POST['building'];
    $capacity = $_POST['capacity'];
    $room_type = $_POST['room_type'];
    
    $sql = "INSERT INTO room (num, floor, bulding, capacity, Type) VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiss", $room_number, $floor, $building, $capacity, $room_type);
    
    if ($stmt->execute()) {
        echo "Room added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
elseif ($action === 'update') {
    $num = $_POST['num'];
    $floor = $_POST['floor'];
    $bulding = $_POST['bulding'];
    $capacity = $_POST['capacity'];
    $Type = $_POST['Type'];
    
    $sql = "UPDATE room SET floor=?, bulding=?, capacity=?, Type=? WHERE num=?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissi", $floor, $bulding, $capacity, $Type, $num);
    
    if ($stmt->execute()) {
        echo "Room updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
elseif ($action === 'delete') {
    $room_number = $_POST['room_number'];
    
    $sql = "DELETE FROM room WHERE num = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_number);
    
    if ($stmt->execute()) {
        echo "Room deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
elseif ($action === 'search') {
    $search = $_GET['q'];
if (is_numeric($search)) {
    $sql = "SELECT * FROM room WHERE num = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $search);
} else {
    $sql = "SELECT * FROM room WHERE Type LIKE ? OR bulding LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_term = "%$search%";
    $stmt->bind_param("ss", $search_term, $search_term);
}

$stmt->execute();
$result = $stmt->get_result();

$rooms = [];
while ($row = $result->fetch_assoc()) {
    $rooms[] = $row;
}

echo json_encode($rooms);
exit;
}
elseif ($action === 'get') {
    $num = $_GET['num'];
    $sql = "SELECT * FROM room WHERE num = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $num);
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
    $sql = "SELECT * FROM room ORDER BY num";
    $result = $conn->query($sql);
    
    $rooms = [];
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
    
    if (isset($_GET['ajax'])) {
        echo json_encode($rooms);
        exit;
    }
}
?>