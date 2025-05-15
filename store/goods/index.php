<?php
session_start();
require_once('../../db.php');

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../../login.php');
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteId'])) {
    $deleteId = $_POST['deleteId'];
    $deleteStmt = $conn->prepare("DELETE FROM commodity WHERE id =?");
    $deleteStmt->bind_param('i', $deleteId);
    if ($deleteStmt->execute()) {
        $successMsg = "Commodity deleted successfully.";
    } else {
        $errorMsg = "Error deleting commodity: ". $deleteStmt->error;
    }
    $deleteStmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteSelected'])) {
    $selectedIds = $_POST['selected_ids'];
    $ids = implode(',', $selectedIds);
    $deleteStmt = $conn->prepare("DELETE FROM commodity WHERE id IN ($ids)");
    if ($deleteStmt->execute()) {
        $successMsg = "Selected commodities deleted successfully.";
    } else {
        $errorMsg = "Error deleting selected commodities: ". $deleteStmt->error;
    }
    $deleteStmt->close();
}

$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
if (!empty($searchQuery)) {
    $searchValue = '%'. $searchQuery. '%';
    $stmt = $conn->prepare("SELECT id, name, price, sales, category, inventory, status, image 
                           FROM commodity 
                           WHERE name LIKE? OR status LIKE?");
    $stmt->bind_param('ss', $searchValue, $searchValue);
} else {
    $stmt = $conn->prepare("SELECT id, name, price, sales, category, inventory, status, image FROM commodity");
}
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $name, $price, $sales, $category, $inventory, $status, $image);

$categoryStmt = $conn->prepare("SELECT category FROM category");
$categoryStmt->execute();
$categoryStmt->store_result();
$categoryStmt->bind_result($categoryOption);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Commodity</title>
    <link rel="stylesheet" href="../../styles/store/goods/index.css">
    <script src="../../js/store/goods/index.js"></script>
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

       .success-message,
       .error-message {
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
        <h1>Manage Commodity</h1>
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
            <button class="logout-btn" onclick="window.location.href='../../index.php?logout=1'">
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
        <a href="../../../index.php" class="tab">Home</a>
        <a href="../index.php" class="tab">Administrator</a>
        <a href="#" class="tab active">Commodity</a>
    </div>
    <div class="sub-tabs">
        <a href="#" class="sub-tab active">List</a>
        <a href="#" class="sub-tab">Create</a>
        <a href="#" class="sub-tab">Category</a>
    </div>

    <?php if (!empty($successMsg)):?>
        <script>showMessage('success', '<?php echo addslashes($successMsg);?>');</script>
    <?php endif;?>
    <?php if (!empty($errorMsg)):?>
        <script>showMessage('error', '<?php echo addslashes($errorMsg);?>');</script>
    <?php endif;?>

    <div class="commodity-list">
        <h2>Commodity List</h2>
        <div class="search-bar">
            <button class="add-btn" onclick="window.location.href='create.php'">Add Commodity</button>
            <button class="refresh-btn">Refresh</button>
            <input type="text" placeholder="Search by name or status" name="search"
                value="<?php echo htmlspecialchars($searchQuery);?>">
            <button type="button" class="search-btn">Search</button>
        </div>
        <div class="batch-actions">
            <button type="button" onclick="selectAllCheckboxes()">Select All</button>
            <button type="button" onclick="unselectAllCheckboxes()">Unselect All</button>
            <button type="button" class="delete-selected-btn" onclick="deleteSelectedCommodities()">Delete
                Selected</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>Num</th>
                    <th>Image</th>
                    <th>Item Name</th>
                    <th>Price</th>
                    <th>Sales</th>
                    <th>Category</th>
                    <th>Inventory</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = 1;
                while ($stmt->fetch()):?>
                    <tr data-id="<?php echo $id;?>">
                        <td><input type="checkbox" class="select-item" name="selected_ids[]" value="<?php echo $id;?>"></td>
                        <td><?php echo $count++;?></td>
                        <td><img src="<?php echo $image;?>" alt="<?php echo $name;?>" width="50"></td>
                        <td style="max-width: 150px; word-wrap: break-word;"><?php echo $name;?></td>
                        <td><?php echo $price;?></td>
                        <td><?php echo $sales;?></td>
                        <td><?php echo $category;?></td>
                        <td><?php echo $inventory;?></td>
                        <td class="<?php echo $status === 'Put On'? 'active' : 'inactive';?>"><?php echo $status;?></td>
                        <td>
                            <button class="btn edit-btn">Edit</button>
                            <button class="btn delete-btn" onclick="deleteCommodity(<?php echo $id;?>)">Delete</button>
                        </td>
                    </tr>
                <?php endwhile;?>
            </tbody>
        </table>
    </div>

    <div class="overlay" id="overlay"></div>
    <div class="edit-form" id="editForm">
        <h3>Edit Commodity</h3>
        <form method="post" id="editCommodityForm" enctype="multipart/form-data">
            <input type="hidden" id="editId" name="editId" value="">
            <label for="editName">Name:</label>
            <textarea id="editName" name="editName" placeholder="Name" required maxlength="200"></textarea>
            <label for="editPrice">Price:</label>
            <input type="text" id="editPrice" name="editPrice" placeholder="Price" required>
            <label for="editSales">Sales:</label>
            <input type="number" id="editSales" name="editSales" placeholder="Sales" required>
            <label for="editCategory">Category:</label>
            <select id="editCategory" name="editCategory" required>
                <?php while ($categoryStmt->fetch()):?>
                    <option value="<?php echo $categoryOption;?>"><?php echo $categoryOption;?></option>
                <?php endwhile;?>
            </select>
            <label for="editInventory">Inventory:</label>
            <input type="number" id="editInventory" name="editInventory" placeholder="Inventory" required>
            <label for="editStatus">Status:</label>
            <select id="editStatus" name="editStatus" required>
                <option value="Put On">Put On</option>
                <option value="Put Off">Put Off</option>
            </select>
            <label for="editImage">Image:</label>
            <div id="image-container"></div>
            <input type="hidden" id="editImage" name="editImage" value="">
            <button type="submit">Save Changes</button>
            <button type="button">Cancel</button>
        </form>
    </div>
</body>

</html>