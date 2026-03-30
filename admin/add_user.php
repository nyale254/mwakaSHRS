<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add User | SHRS</title>
    <link rel="stylesheet" href="/Mwaka.SHRS.2/styles/user_management.css">
    <style>
        #strength {
            font-size: 14px;
            margin-top: 5px;
        }
        .weak { color: red; }
        .medium { color: orange; }
        .strong { color: green; }
        .buttons {
            margin-top: 25px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-start;
            width: 200px;
        }
        .btn {
            background-color: #3498db;
            color: white;
            border: none;
        }

        .btn:hover {background-color: #2980b9;}
        .btn-back {
            background-color: #bdc3c7;
            color: #2c3e50;
            text-decoration: none;
            display: inline-block;
        }

        .btn-back:hover {background-color: #95a5a6;}
    </style>

</head>
<body>

<h2>Add New User</h2>

<form method="POST" action="save_user.php" class="form-box" onsubmit="return confirmSubmit()">

    <label>Fullname</label>
    <input type="text" name="fullname" required>

    <label>Username</label>
    <input type="text" name="username" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <div style="position: relative;">
        <input type="password" name="password" id="password" required onkeyup="checkStrength()">
        <span id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
            cursor: pointer; color: #555;">👁️</span>
    </div>
    <div id="strength"></div>


    <label>Role</label>
    <select name="role" required>
        <option value="">-- Select Role --</option>
        <option value="admin">Admin</option>
        <option value="nurse">Nurse</option>
        <option value="student">Student</option>
        <option value="doctor">Doctor</option>
    </select>

    <label>Status</label>
    <select name="status">
        <option value="active">Active</option>
        <option value="disabled">Disabled</option>
    </select>
    
    <div class="buttons">
        <button type="submit" class="btn">Save User</button>
        <button type="button" id="backBtn" class="btn-back">⬅ Back</button>
    </div>
</form>
</body>
</html>
