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
    <title>Create Commodity</title>
    <link rel="stylesheet" href="../../styles/store/goods/create.css">
    <script src="../../js/store/goods/create.js" defer></script>
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
        <h1>Create Commodity</h1>
        <div class="user-info">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7ZM14 7C14 8.10457 13.1046 9 12 9C10.8954 9 10 8.10457 10 7C10 5.89543 10.8954 5 12 5C13.1046 5 14 5.89543 14 7Z" fill="currentColor" />
                <path d="M16 15C16 14.4477 15.5523 14 15 14H9C8.44772 14 8 14.4477 8 15V21H6V15C6 13.3431 7.34315 12 9 12H15C16.6569 12 18 13.3431 18 15V21H16V15Z" fill="currentColor" />
            </svg>
            <span><?php echo $adminUsername; ?></span>
            <button class="logout-btn" onclick="window.location.href='../../../index.php?logout=1'">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.51428 20H4.51428C3.40971 20 2.51428 19.1046 2.51428 18V6C2.51428 4.89543 3.40971 4 4.51428 4H8.51428V6H4.51428V18H8.51428V20Z" fill="currentColor" />
                    <path d="M13.8418 17.385L15.262 15.9768L11.3428 12.0242L20.4857 12.0242C21.038 12.0242 21.4857 11.5765 21.4857 11.0242C21.4857 10.4719 21.038 10.0242 20.4857 10.0242L11.3236 10.0242L15.304 6.0774L13.8958 4.6572L7.5049 10.9941L13.8418 17.385Z" fill="currentColor" />
                </svg>
                Sign out
            </button>
        </div>
    </div>

    <div class="tabs">
        <a href="#" class="tab">Home</a>
        <a href="#" class="tab">Administrator</a>
        <a href="#" class="tab active">Commodity</a>
    </div>

    <div class="sub-tabs">
        <a href="#" class="sub-tab">List</a>
        <a href="#" class="sub-tab active">Create</a>
        <a href="#" class="sub-tab">Category</a>
    </div>

    <div class="form-section">
        <h2 style="margin-bottom: 30px;">Create Commodity</h2>
        <form method="post" id="createCommodityForm" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="createName">Item Name</label>
                        <textarea id="createName" name="createName" placeholder="Please enter the item name" required maxlength="200" style="height: 100px;"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="createSales">Sales</label>
                        <input type="number" id="createSales" name="createSales" placeholder="Please enter the sales" required>
                    </div>
                    <div class="form-group">
                        <label for="createInventory">Inventory</label>
                        <input type="number" id="createInventory" name="createInventory" placeholder="Please enter the inventory" required>
                    </div>
                </div>

                <div class="form-col">
                    <div class="form-group">
                        <label for="createPrice">Price</label>
                        <input type="number" step="0.01" id="createPrice" name="createPrice" placeholder="Please enter the price" required>
                    </div>
                    <div class="form-group">
                        <label for="createCategory">Category</label>
                        <select id="createCategory" name="createCategory" required>
                            <?php while ($categoryStmt->fetch()): ?>
                                <option value="<?php echo $categoryOption; ?>"><?php echo $categoryOption; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="createStatus">Status</label>
                        <select id="createStatus" name="createStatus" required>
                            <option value="Put On">Put On</option>
                            <option value="Put Off">Put Off</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="upload-image">Image</label>
                        <div class="image-upload-container">
                            <div class="image-upload" id="image-upload-area">
                                <input type="file" id="upload-image" name="image" accept=".png,.jpg,.jpeg" required style="display: none;">
                                <div class="upload-icon">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19 11H7M19 11C20.1046 11 21 11.8954 21 13V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V13C3 11.8954 3.89543 11 5 11M19 11V9C19 7.89543 18.1046 7 17 7M7 11V9C7 7.89543 6.10465 7 5 7M12 14V18M12 14L14 16M12 14L10 16" stroke="#888" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <p>Click to upload</p>
                                <p style="font-size: 10px; color: #888;">JPG, JPEG, PNG</p>
                            </div>
                            <div class="image-preview" id="image-preview">
                                <img id="preview-image" src="" alt="Preview">
                                <div class="delete-icon" onclick="clearImagePreview()">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="btn-group">
                <button type="submit" class="save-btn">Save</button>
                <button type="button" onclick="window.location.href='index.php'" class="cancel-btn">Cancel</button>
            </div>
        </form>
    </div>

    <div class="message-container" id="message-container"></div>
</body>
</html>