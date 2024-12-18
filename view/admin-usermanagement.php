<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../db/db.php'; // Include your database connection

// Handle Edit and Delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'edit_user') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $type = $_POST['type'];

        $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, user_type = ? WHERE user_id = ?");
        $stmt->bind_param("sssi", $name, $email, $type, $id);

        if ($stmt->execute()) {
            $message = "User updated successfully!";
        } else {
            $message = "Error updating user: " . $stmt->error;
        }
    }

    if ($_POST['action'] === 'delete_user') {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $message = "User deleted successfully!";
        } else {
            $message = "Error deleting user: " . $stmt->error;
        }
    }
}

// Fetch all users for the table
$result = $conn->query("SELECT user_id, full_name, email, user_type FROM users");
$users = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - User Management - Campus Beats</title>
    <link rel="stylesheet" href="../assets/css/admin-usermanagement.css">
</head>

<body>
    <?php if (isset($message)) : ?>
        <p style="color: green;"><?= $message ?></p>
    <?php endif; ?>

    <!-- Edit User Modal -->
    <div id="edit-user-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <h3>Edit User</h3>
            <form id="edit-user-form" method="POST">
                <input type="hidden" name="id" id="edit-user-id">
                <input type="text" name="name" id="edit-user-name" placeholder="Full Name" required>
                <input type="email" name="email" id="edit-user-email" placeholder="Email" required>
                <select name="type" id="edit-user-type" required>
                    <option value="Admin">Admin</option>
                    <option value="User">User</option>
                    <option value="Moderator">Moderator</option>
                </select>
                <input type="hidden" name="action" value="edit_user">
                <button type="submit">Save Changes</button>
            </form>
            <button id="close-modal">Cancel</button>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-container">
            <img src="../assets/imgs/logos_and_backgrounds/CampusBeatsLogo.webp" alt="Campus Beats Logo" class="logo">
            <h2>Campus Beats</h2>
        </div>
        <ul>
            <li><a href="./admin-dashboard.php">Dashboard</a></li>
            <li><a href="./admin-usermanagement.php" class="active">User Management</a></li>
            <li><a href="./admin-trackmanagement.php">Track Management</a></li>
            <li><a href="report-management.php">Reports</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="index.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <nav class="navbar">
            <div class="search-container">
                <input type="text" class="search-bar" placeholder="Search for users...">
                <button class="search-btn">Search</button>
            </div>
            <div class="user-info">
                <span>Welcome, Admin</span>
                <img src="profile-picture.jpg" alt="Admin" class="admin-avatar">
            </div>
        </nav>

        <!-- User Management Section -->
        <section class="user-management">
            <h2>User Management</h2>
            <table class="user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>User Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td><?= $user['user_id'] ?></td>
                            <td><?= $user['full_name'] ?></td>
                            <td><?= $user['email'] ?></td>
                            <td><?= $user['user_type'] ?></td>
                            <td>
                                <button class="edit-btn" 
                                    data-id="<?= $user['user_id'] ?>" 
                                    data-name="<?= $user['full_name'] ?>" 
                                    data-email="<?= $user['email'] ?>" 
                                    data-type="<?= $user['user_type'] ?>">
                                    Edit
                                </button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?= $user['user_id'] ?>">
                                    <input type="hidden" name="action" value="delete_user">
                                    <button type="submit" class="delete-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <p>&copy; 2024 Campus Beats. All Rights Reserved.</p>
        </footer>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const editModal = document.getElementById("edit-user-modal");
            const closeModalButton = document.getElementById("close-modal");

            // Bind Edit Buttons
            document.querySelectorAll(".edit-btn").forEach(button => {
                button.addEventListener("click", () => {
                    const id = button.dataset.id;
                    const name = button.dataset.name;
                    const email = button.dataset.email;
                    const type = button.dataset.type;

                    document.getElementById("edit-user-id").value = id;
                    document.getElementById("edit-user-name").value = name;
                    document.getElementById("edit-user-email").value = email;
                    document.getElementById("edit-user-type").value = type;

                    editModal.style.display = "block";
                });
            });

            // Close Modal
            closeModalButton.addEventListener("click", () => {
                editModal.style.display = "none";
            });
        });
    </script>
</body>

</html>
