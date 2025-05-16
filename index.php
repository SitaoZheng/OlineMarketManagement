<?php
session_start();
require_once('db.php');

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$adminId = $_SESSION['admin_id'];
$sql = "SELECT username FROM admin WHERE id =?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $adminId);
$stmt->execute();
$stmt->bind_result($adminUsername);
$stmt->fetch();
$stmt->close();

$goodsSql = "SELECT COUNT(*) as total_goods FROM commodity";
$goodsStmt = $conn->prepare($goodsSql);
$goodsStmt->execute();
$goodsResult = $goodsStmt->get_result();
$goodsRow = $goodsResult->fetch_assoc();
$totalGoods = $goodsRow['total_goods'];

$roleSql = "SELECT COUNT(*) as total_roles FROM role";
$roleStmt = $conn->prepare($roleSql);
$roleStmt->execute();
$roleResult = $roleStmt->get_result();
$roleRow = $roleResult->fetch_assoc();
$totalRoles = $roleRow['total_roles'];

$categorySql = "SELECT COUNT(*) as total_categories FROM category";
$categoryStmt = $conn->prepare($categorySql);
$categoryStmt->execute();
$categoryResult = $categoryStmt->get_result();
$categoryRow = $categoryResult->fetch_assoc();
$totalCategories = $categoryRow['total_categories'];

$conn->close();

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Market Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .main-header {
            background-color: #007BFF;
            color: #fff;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 40px;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            background: none;
            border: none;
            color: #fff;
            cursor: pointer;
            margin-left: 10px;
            transition: color 0.3s ease;
        }

        .logout-btn:hover {
            color: #e07a00;
        }

        .logout-btn svg {
            margin-left: 5px;
            width: 20px;
            height: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .tabs {
            background-color: #fff;
            border-bottom: 1px solid #ddd;
            display: flex;
            padding: 0 20px;
        }

        .tab {
            padding: 12px 20px;
            text-decoration: none;
            color: #333;
            border-bottom: 2px solid transparent;
            transition: color 0.3s ease;
        }

        .tab.active {
            font-weight: bold;
            color: #007BFF;
            border-bottom: 2px solid #007BFF;
        }

        .tab:hover {
            background-color: #f8f9fa;
            color: #007BFF;
        }

        .main-content {
            padding: 20px 150px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-gap: 20px;
        }

        .card {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            padding: 20px;
        }

        .card svg {
            width: 40px;
            height: 40px;
            margin: 0 20px;
        }

        .card-info {
            display: flex;
            flex-direction: column;
            margin-left: 50px;
        }

        .card-name {
            font-size: 32px;
        }

        .card-number {
            font-size: 64px;
            font-weight: bold;
            color: #007BFF;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(function (tab) {
                tab.addEventListener('click', function (e) {
                    e.preventDefault();
                    const tabText = this.textContent.trim();
                    let targetUrl;
                    if (tabText === 'Home') {
                        targetUrl = window.location.href;
                    } else if (tabText === 'Administrator') {
                        targetUrl = 'store/manage/user/index.php';
                    } else if (tabText === 'Commodity') {
                        targetUrl = 'store/goods/index.php';
                    }
                    window.location.href = targetUrl;
                });
            });
        });
    </script>
</head>

<body>
    <div class="main-header">
        <h1>Management Central</h1>
        <div class="user-info">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7ZM14 7C14 8.10457 13.1046 9 12 9C10.8954 9 10 8.10457 10 7C10 5.89543 10.8954 5 12 5C13.1046 5 14 5.89543 14 7Z"
                    fill="currentColor" />
                <path
                    d="M16 15C16 14.4477 15.5523 14 15 14H9C8.44772 14 8 14.4477 8 15V21H6V15C6 13.3431 7.34315 12 9 12H15C16.6569 12 18 13.3431 18 15V21H16V15Z"
                    fill="currentColor" />
            </svg>
            <span><?php echo $adminUsername; ?></span>
            <button class="logout-btn" onclick="window.location.href='index.php?logout=1'">
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
        <a href="#" class="tab active">Home</a>
        <a href="#" class="tab">Administrator</a>
        <a href="#" class="tab">Commodity</a>
    </div>
    <div class="main-content">
        <div class="card">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M7 7V11H11V7H7Z" fill="currentColor" />
                <path d="M13 7H17V11H13V7Z" fill="currentColor" />
                <path d="M13 13V17H17V13H13Z" fill="currentColor" />
                <path d="M7 13H11V17H7V13Z" fill="currentColor" />
                <path fill-rule="evenodd" clip-rule="evenodd" d="M3 3H21V21H3V3ZM5 5V19H19V5H5Z" fill="currentColor" />
            </svg>
            <div class="card-info">
                <span class="card-name">Commodities</span>
                <span class="card-number"><?php echo $totalGoods; ?></span>
            </div>
        </div>
        <div class="card">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M16.9307 4.01587H14.7655C14.3582 2.84239 13.2428 2 11.9307 2C10.6185 2 9.50313 2.84239 9.09581 4.01587H6.93066C5.27381 4.01587 3.93066 5.35901 3.93066 7.01587V9.21205C2.80183 9.64283 2 10.7357 2 12.0159C2 13.296 2.80183 14.3889 3.93066 14.8197V17.0159C3.93066 18.6727 5.27381 20.0159 6.93066 20.0159H9.08467C9.48247 21.2064 10.6064 22.0645 11.9307 22.0645C13.255 22.0645 14.3789 21.2064 14.7767 20.0159H16.9307C18.5875 20.0159 19.9307 18.6727 19.9307 17.0159V14.8446C21.095 14.4322 21.929 13.3214 21.929 12.0159C21.929 10.7103 21.095 9.5995 19.9307 9.18718V7.01587C19.9307 5.35901 18.5875 4.01587 16.9307 4.01587ZM5.93066 14.8687V17.0159C5.93066 17.5682 6.37838 18.0159 6.93066 18.0159H9.11902C9.54426 16.8761 10.6427 16.0645 11.9307 16.0645C13.2187 16.0645 14.3171 16.8761 14.7423 18.0159H16.9307C17.4829 18.0159 17.9307 17.5682 17.9307 17.0159V14.8458C16.7646 14.4344 15.929 13.3227 15.929 12.0159C15.929 10.709 16.7646 9.59732 17.9307 9.18597V7.01587C17.9307 6.46358 17.4829 6.01587 16.9307 6.01587H14.7543C14.338 7.17276 13.2309 8 11.9307 8C10.6304 8 9.52331 7.17276 9.10703 6.01587H6.93066C6.37838 6.01587 5.93066 6.46358 5.93066 7.01587V9.16302C7.13193 9.55465 8 10.6839 8 12.0159C8 13.3479 7.13193 14.4771 5.93066 14.8687Z"
                    fill="currentColor" />
            </svg>
            <div class="card-info">
                <span class="card-name">Roles</span>
                <span class="card-number"><?php echo $totalRoles; ?></span>
            </div>
        </div>
        <div class="card">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M12.552 8C11.9997 8 11.552 8.44772 11.552 9C11.552 9.55228 11.9997 10 12.552 10H16.552C17.1043 10 17.552 9.55228 17.552 9C17.552 8.44772 17.1043 8 16.552 8H12.552Z"
                    fill="currentColor" fill-opacity="0.5" />
                <path
                    d="M12.552 17C11.9997 17 11.552 17.4477 11.552 18C11.552 18.5523 11.9997 19 12.552 19H16.552C17.1043 19 17.552 18.5523 17.552 18C17.552 17.4477 17.1043 17 16.552 17H12.552Z"
                    fill="currentColor" fill-opacity="0.5" />
                <path
                    d="M12.552 5C11.9997 5 11.552 5.44772 11.552 6C11.552 6.55228 11.9997 7 12.552 7H20.552C21.1043 7 21.552 6.55228 21.552 6C21.552 5.44772 21.1043 5 20.552 5H12.552Z"
                    fill="currentColor" fill-opacity="0.8" />
                <path
                    d="M12.552 14C11.9997 14 11.552 14.4477 11.552 15C11.552 15.5523 11.9997 16 12.552 16H20.552C21.1043 16 21.552 15.5523 21.552 15C21.552 14.4477 21.1043 14 20.552 14H12.552Z"
                    fill="currentColor" fill-opacity="0.8" />
                <path
                    d="M3.448 4.00208C2.89571 4.00208 2.448 4.44979 2.448 5.00208V10.0021C2.448 10.5544 2.89571 11.0021 3.448 11.0021H8.448C9.00028 11.0021 9.448 10.5544 9.448 10.0021V5.00208C9.448 4.44979 9.00028 4.00208 8.448 4.00208H3.448Z"
                    fill="currentColor" />
                <path
                    d="M3.448 12.9979C2.89571 12.9979 2.448 13.4456 2.448 13.9979V18.9979C2.448 19.5502 2.89571 19.9979 3.448 19.9979H8.448C9.00028 19.9979 9.448 19.5502 9.448 18.9979V13.9979C9.448 13.4456 9.00028 12.9979 8.448 12.9979H3.448Z"
                    fill="currentColor" />
            </svg>
            <div class="card-info">
                <span class="card-name">Categories</span>
                <span class="card-number"><?php echo $totalCategories; ?></span>
            </div>
        </div>
        <div class="card">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M12 6C12.5523 6 13 6.44772 13 7V11H17C17.5523 11 18 11.4477 18 12C18 12.5523 17.5523 13 17 13H13V17C13 17.5523 12.5523 18 12 18C11.4477 18 11 17.5523 11 17V13H7C6.44772 13 6 12.5523 6 12C6 11.4477 6.44772 11 7 11H11V7C11 6.44772 11.4477 6 12 6Z"
                    fill="currentColor" />
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M5 22C3.34315 22 2 20.6569 2 19V5C2 3.34315 3.34315 2 5 2H19C20.6569 2 22 3.34315 22 5V19C22 20.6569 20.6569 22 19 22H5ZM4 19C4 19.5523 4.44772 20 5 20H19C19.5523 20 20 19.5523 20 19V5C20 4.44772 19.5523 4 19 4H5C4.44772 4 4 4.44772 4 5V19Z"
                    fill="currentColor" />
            </svg>
            <div class="card-info">
                <span class="card-name">xxxx</span>
                <span class="card-number">--</span>
            </div>
        </div>
        <div class="card">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M12 6C12.5523 6 13 6.44772 13 7V11H17C17.5523 11 18 11.4477 18 12C18 12.5523 17.5523 13 17 13H13V17C13 17.5523 12.5523 18 12 18C11.4477 18 11 17.5523 11 17V13H7C6.44772 13 6 12.5523 6 12C6 11.4477 6.44772 11 7 11H11V7C11 6.44772 11.4477 6 12 6Z"
                    fill="currentColor" />
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M5 22C3.34315 22 2 20.6569 2 19V5C2 3.34315 3.34315 2 5 2H19C20.6569 2 22 3.34315 22 5V19C22 20.6569 20.6569 22 19 22H5ZM4 19C4 19.5523 4.44772 20 5 20H19C19.5523 20 20 19.5523 20 19V5C20 4.44772 19.5523 4 19 4H5C4.44772 4 4 4.44772 4 5V19Z"
                    fill="currentColor" />
            </svg>
            <div class="card-info">
                <span class="card-name">xxxx</span>
                <span class="card-number">--</span>
            </div>
        </div>
        <div class="card">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M12 6C12.5523 6 13 6.44772 13 7V11H17C17.5523 11 18 11.4477 18 12C18 12.5523 17.5523 13 17 13H13V17C13 17.5523 12.5523 18 12 18C11.4477 18 11 17.5523 11 17V13H7C6.44772 13 6 12.5523 6 12C6 11.4477 6.44772 11 7 11H11V7C11 6.44772 11.4477 6 12 6Z"
                    fill="currentColor" />
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M5 22C3.34315 22 2 20.6569 2 19V5C2 3.34315 3.34315 2 5 2H19C20.6569 2 22 3.34315 22 5V19C22 20.6569 20.6569 22 19 22H5ZM4 19C4 19.5523 4.44772 20 5 20H19C19.5523 20 20 19.5523 20 19V5C20 4.44772 19.5523 4 19 4H5C4.44772 4 4 4.44772 4 5V19Z"
                    fill="currentColor" />
            </svg>
            <div class="card-info">
                <span class="card-name">xxxx</span>
                <span class="card-number">--</span>
            </div>
        </div>
        <div class="card">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M12 6C12.5523 6 13 6.44772 13 7V11H17C17.5523 11 18 11.4477 18 12C18 12.5523 17.5523 13 17 13H13V17C13 17.5523 12.5523 18 12 18C11.4477 18 11 17.5523 11 17V13H7C6.44772 13 6 12.5523 6 12C6 11.4477 6.44772 11 7 11H11V7C11 6.44772 11.4477 6 12 6Z"
                    fill="currentColor" />
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M5 22C3.34315 22 2 20.6569 2 19V5C2 3.34315 3.34315 2 5 2H19C20.6569 2 22 3.34315 22 5V19C22 20.6569 20.6569 22 19 22H5ZM4 19C4 19.5523 4.44772 20 5 20H19C19.5523 20 20 19.5523 20 19V5C20 4.44772 19.5523 4 19 4H5C4.44772 4 4 4.44772 4 5V19Z"
                    fill="currentColor" />
            </svg>
            <div class="card-info">
                <span class="card-name">xxxx</span>
                <span class="card-number">--</span>
            </div>
        </div>
        <div class="card">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M12 6C12.5523 6 13 6.44772 13 7V11H17C17.5523 11 18 11.4477 18 12C18 12.5523 17.5523 13 17 13H13V17C13 17.5523 12.5523 18 12 18C11.4477 18 11 17.5523 11 17V13H7C6.44772 13 6 12.5523 6 12C6 11.4477 6.44772 11 7 11H11V7C11 6.44772 11.4477 6 12 6Z"
                    fill="currentColor" />
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M5 22C3.34315 22 2 20.6569 2 19V5C2 3.34315 3.34315 2 5 2H19C20.6569 2 22 3.34315 22 5V19C22 20.6569 20.6569 22 19 22H5ZM4 19C4 19.5523 4.44772 20 5 20H19C19.5523 20 20 19.5523 20 19V5C20 4.44772 19.5523 4 19 4H5C4.44772 4 4 4.44772 4 5V19Z"
                    fill="currentColor" />
            </svg>
            <div class="card-info">
                <span class="card-name">xxxx</span>
                <span class="card-number">--</span>
            </div>
        </div>
        <div class="card">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M12 6C12.5523 6 13 6.44772 13 7V11H17C17.5523 11 18 11.4477 18 12C18 12.5523 17.5523 13 17 13H13V17C13 17.5523 12.5523 18 12 18C11.4477 18 11 17.5523 11 17V13H7C6.44772 13 6 12.5523 6 12C6 11.4477 6.44772 11 7 11H11V7C11 6.44772 11.4477 6 12 6Z"
                    fill="currentColor" />
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M5 22C3.34315 22 2 20.6569 2 19V5C2 3.34315 3.34315 2 5 2H19C20.6569 2 22 3.34315 22 5V19C22 20.6569 20.6569 22 19 22H5ZM4 19C4 19.5523 4.44772 20 5 20H19C19.5523 20 20 19.5523 20 19V5C20 4.44772 19.5523 4 19 4H5C4.44772 4 4 4.44772 4 5V19Z"
                    fill="currentColor" />
            </svg>

            <div class="card-info">
                <span class="card-name">xxxx</span>
                <span class="card-number">--</span>
            </div>
        </div>
    </div>
</body>

</html>
