<?php
session_start();
require_once('../../../db.php');

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../../../login.php');
    exit;
}

$adminUsername = '';
$adminStmt = $conn->prepare("SELECT username FROM admin WHERE id =?");
$adminStmt->bind_param('i', $_SESSION['admin_id']);
$adminStmt->execute();
$adminStmt->bind_result($adminUsername);
$adminStmt->fetch();
$adminStmt->close();

$successMsg = '';
$errorMsg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_role'])) {
    $addRole = $_POST['add_role'];

    $checkStmt = $conn->prepare("SELECT id FROM role WHERE role =?");
    $checkStmt->bind_param('s', $addRole);
    $checkStmt->execute();
    $checkStmt->store_result();
    
    if ($checkStmt->num_rows > 0) {
        $errorMsg = "Role already exists.";
    } else {
        $insertStmt = $conn->prepare("INSERT INTO role (role) VALUES (?)");
        $insertStmt->bind_param('s', $addRole);
        
        if ($insertStmt->execute()) {
            $successMsg = "Role added successfully.";
            $_POST = array();
        } else {
            $errorMsg = "Error adding role: " . $insertStmt->error;
        }
        
        $insertStmt->close();
    }
    
    $checkStmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editRole'])) {
    $editRole = $_POST['editRole'];
    $editId = $_POST['editId'];

    $updateStmt = $conn->prepare("UPDATE role SET role =? WHERE id =?");
    $updateStmt->bind_param('si', $editRole, $editId);

    if ($updateStmt->execute()) {
        $successMsg = "Role updated successfully.";
    } else {
        $errorMsg = "Error updating role: " . $updateStmt->error;
    }
    
    $updateStmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteId'])) {
    $deleteId = $_POST['deleteId'];

    $checkStmt = $conn->prepare("SELECT id FROM admin WHERE role = (SELECT role FROM role WHERE id =?)");
    $checkStmt->bind_param('i', $deleteId);
    $checkStmt->execute();
    $checkStmt->store_result();
    
    if ($checkStmt->num_rows > 0) {
        $errorMsg = "Cannot delete role because it is in use by administrators.";
    } else {
        $deleteStmt = $conn->prepare("DELETE FROM role WHERE id =?");
        $deleteStmt->bind_param('i', $deleteId);
        
        if ($deleteStmt->execute()) {
            $successMsg = "Role deleted successfully.";
        } else {
            $errorMsg = "Error deleting role: " . $deleteStmt->error;
        }
        
        $deleteStmt->close();
    }
    
    $checkStmt->close();
}

$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
if (!empty($searchQuery)) {
    $searchValue = '%' . $searchQuery . '%';
    $stmt = $conn->prepare("SELECT id, role FROM role WHERE role LIKE ?");
    $stmt->bind_param('s', $searchValue);
} else {
    $stmt = $conn->prepare("SELECT id, role FROM role");
}

$stmt->execute();
$stmt->store_result();
$stmt->bind_result($roleId, $roleName);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Roles</title>
    <link rel="stylesheet" href="../../../styles/store/manage/role/index.css">
    <script src="../../../js/store/manage/role/index.js"></script>
    <style>
        #message-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            z-index: 1000;
            padding-top: 20px;
        }

        .success-message, .error-message {
            padding: 10px 20px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.3s, transform 0.3s;
            margin: 0 auto;
            max-width: 80%;
            text-align: center;
            white-space: pre-line;
        }

        .success-message {
            background-color: #4CAF50;
        }

        .error-message {
            background-color: #f44336;
        }
    </style>
</head>

<body>
    <div class="main-header">
        <h1>Manage Role</h1>
        <div class="user-info">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7ZM14 7C14 8.10457 13.1046 9 12 9C10.8954 9 10 8.10457 10 7C10 5.89543 10.8954 5 12 5C13.1046 5 14 5.89543 14 7Z"
                    fill="currentColor" />
                <path
                    d="M16 15C16 14.4477 15.5523 14 15 14H9C8.44772 14 8 14.4477 8 15V21H6V15C6 13.3431 7.34315 12 9 12H15C16.6569 12 18 13.3431 18 15V21H16V15Z"
                    fill="currentColor" />
            </svg>
            <span><?php echo $adminUsername;?></span>
            <button class="logout-btn" onclick="window.location.href='../../../index.php?logout=1'">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M8.51428 20H4.51428C3.40971 20 2.51428 19.1046 2.51428 18V6C2.51428 4.89543 3.40971 4 4.51428 4H8.51428V6H4.51428V18H8.51428V20Z"
                        fill="currentColor" />
                    <path
                        d="M13.8418 17.385L15.262 15.9768L11.3428 12.0242L20.4857 12.0242C21.038 12.0242 21.4857 11.5765 21.4857 11.0242C21.4857 10.4719 21.038 10.0242 20.4857 10.0242L11.3236 10.0242L15.304 6.0774L13.8958 4.6572L7.5049 10.9941L13.8418 17.385Z"
                        fill="currentColor" />
                </svg>
                Sign out
            </button>
        </div>
    </div>
    <div class="tabs">
        <a href="#" class="tab">Home</a>
        <a href="#" class="tab active">Administrator</a>
        <a href="#" class="tab">Commodity</a>
    </div>
    
    <div class="sub-tabs">
        <a href="#" class="sub-tab">List</a>
        <a href="#" class="sub-tab active">Role</a>
    </div>

    <?php if (!empty($successMsg)): ?>
        <script>showMessage('success', '<?php echo addslashes($successMsg); ?>');</script>
    <?php endif; ?>
    <?php if (!empty($errorMsg)): ?>
        <script>showMessage('error', '<?php echo addslashes($errorMsg); ?>');</script>
    <?php endif; ?>

    <div class="role-list">
        <h2>Role List</h2>
        <div class="search-bar">
            <button class="add-btn">Add Role</button>
            <button class="refresh-btn">Refresh</button>
            <input type="text" placeholder="Search by role name" name="search"
                value="<?php echo htmlspecialchars($searchQuery);?>">
            <button type="button" class="search-btn">Search</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Num</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $counter = 1; while ($stmt->fetch()):?>
                    <tr data-id="<?php echo $roleId;?>">
                        <td><?php echo $counter++;?></td>
                        <td><?php echo $roleName;?></td>
                        <td>
                            <?php if ($roleName!== 'superadmin'):?>
                            <button class="edit-btn">Edit</button>
                            <button class="delete-btn" onclick="deleteRole(<?php echo $roleId;?>, '<?php echo addslashes($roleName);?>')">Delete</button>
                            <?php endif;?>
                        </td>
                    </tr>
                <?php endwhile;?>
            </tbody>
        </table>
    </div>

    <div class="overlay" id="overlay"></div>
    <div class="edit-form" id="editForm">
        <h3>Edit Role</h3>
        <form method="post" id="editRoleForm">
            <label for="editRole">Role Name:</label>
            <input type="text" id="editRole" name="editRole" placeholder="Role Name" required>
            <input type="hidden" id="editId" name="editId" value="">
            <button type="submit">Save Changes</button>
            <button type="button">Cancel</button>
        </form>
    </div>

    <div class="add-form" id="addForm">
        <h3>Add Role</h3>
        <form method="post" id="addRoleForm">
            <label for="add_role">Role Name:</label>
            <input type="text" id="add_role" name="add_role" placeholder="Role Name" required>
            <button type="submit">Add Role</button>
            <button type="button">Cancel</button>
        </form>
    </div>
</body>

</html>