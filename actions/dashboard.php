<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../db/db.php'; // Include your database connection file

// Fetch summary data for the admin dashboard
if ($_GET['action'] === 'fetch_dashboard_summary') {
    $query = "SELECT COUNT(*) as total_artists FROM artists";
    $total_artists = $conn->query($query)->fetch_assoc()['total_artists'];

    $query = "SELECT COUNT(*) as total_listeners FROM listeners";
    $total_listeners = $conn->query($query)->fetch_assoc()['total_listeners'];

    $query = "SELECT COUNT(*) as total_tracks FROM tracks";
    $total_tracks = $conn->query($query)->fetch_assoc()['total_tracks'];

    $query = "SELECT COUNT(*) as total_record_labels FROM record_labels";
    $total_record_labels = $conn->query($query)->fetch_assoc()['total_record_labels'];

    echo "Artists: $total_artists, Listeners: $total_listeners, Tracks: $total_tracks, Record Labels: $total_record_labels";
    exit;
}

// Fetch all users for user management
if ($_GET['action'] === 'fetch_users') {
    $query = "SELECT user_id, full_name, email, user_type FROM users";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        echo $row['user_id'] . ',' . $row['full_name'] . ',' . $row['email'] . ',' . $row['user_type'] . "\n";
    }
    exit;
}

// Edit user
if ($_POST['action'] === 'edit_user') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $type = $_POST['type'];

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, user_type = ? WHERE user_id = ?");
    $stmt->bind_param("sssi", $name, $email, $type, $id);

    if ($stmt->execute()) {
        echo "User updated successfully.";
    } else {
        echo "Error updating user: " . $stmt->error;
    }
    exit;
}

// Delete user
if ($_POST['action'] === 'delete_user') {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "User deleted successfully.";
    } else {
        echo "Error deleting user: " . $stmt->error;
    }
    exit;
}
?>
