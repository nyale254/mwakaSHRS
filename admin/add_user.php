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

    <button type="submit" class="btn">Save User</button>
    <a href="cancel.php" class="cancel">Cancel</a>

</form>

<script>
function checkStrength() {
    const password = document.getElementById("password").value;
    const strength = document.getElementById("strength");

    let score = 0;

    if (password.length >= 8) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[a-z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;

    if (password.length === 0) {
        strength.textContent = "";
        return;
    }

    if (score <= 2) {
        strength.textContent = "Weak password";
        strength.className = "weak";
    } else if (score === 3 || score === 4) {
        strength.textContent = "Medium strength password";
        strength.className = "medium";
    } else {
        strength.textContent = "Strong password";
        strength.className = "strong";
    }
}

function confirmSubmit() {
    return confirm("Are you sure you want to add this user?");
}
</script>
<script>

const toggle = document.getElementById('togglePassword');
const password = document.getElementById('password');

toggle.addEventListener('click', function() {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);

    toggle.textContent = type === 'password' ? '👁️' : '🙈';
});
</script>

</body>
</html>
