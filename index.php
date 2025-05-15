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
            padding: 20px;
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
        <p>这是主界面内容，可以在此展示各种管理功能等。</p>
    </div>
</body>

</html>