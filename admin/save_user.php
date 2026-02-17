<?php
session_start();
include "../connect.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $status = $_POST['status'] ?? 'active'; 

    if (empty($fullname) || empty($username) || empty($email) || empty($password) || empty($role)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long!";
    }

    if (empty($error)) {

        $checkUsername = mysqli_prepare($conn, "SELECT user_id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($checkUsername, "s", $username);
        mysqli_stmt_execute($checkUsername);
        mysqli_stmt_store_result($checkUsername);

        if (mysqli_stmt_num_rows($checkUsername) > 0) {
            $error = "Username already exists!";
        }
        mysqli_stmt_close($checkUsername);

        if (empty($error)) {
            $checkEmail = mysqli_prepare($conn, "SELECT user_id FROM users WHERE email = ?");
            mysqli_stmt_bind_param($checkEmail, "s", $email);
            mysqli_stmt_execute($checkEmail);
            mysqli_stmt_store_result($checkEmail);

            if (mysqli_stmt_num_rows($checkEmail) > 0) {
                $error = "Email already exists!";
            }
            mysqli_stmt_close($checkEmail);
        }
    }

    if (empty($error)) {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        mysqli_begin_transaction($conn);

        try {
            $stmt = mysqli_prepare($conn,
                "INSERT INTO users (fullname, username, email, password, role, status, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, NOW())"
            );
            mysqli_stmt_bind_param($stmt, "ssssss", $fullname, $username, $email, $hashed_password, $role, $status);
            mysqli_stmt_execute($stmt);

            $user_id = mysqli_insert_id($conn); 
            mysqli_stmt_close($stmt);

            if ($role === 'student') {
                $reg_no = "REG" . rand(1000, 9999); 
                $gender = "Not Set";                
                $course = "Undeclared";             
                $phone = "0000000000";              
                $emergency_contact = "0000000000";  
                $year_of_study = 1;                 

                $studentStmt = mysqli_prepare($conn,
                    "INSERT INTO students (full_name, reg_no, gender, course, phone, emergency_contact, email, user_id, year_of_study, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
                );
                mysqli_stmt_bind_param(
                    $studentStmt,
                    "ssssssssi",
                    $fullname,
                    $reg_no,
                    $gender,
                    $course,
                    $phone,
                    $emergency_contact,
                    $email,
                    $user_id,
                    $year_of_study
                );
                mysqli_stmt_execute($studentStmt);
                mysqli_stmt_close($studentStmt);
            }

            mysqli_commit($conn);
            $_SESSION['success'] = "User added successfully!";
            header("Location: users_management.php");
            exit();

        } catch (Exception $e) {
            mysqli_rollback($conn);
            $_SESSION['error'] = "Error adding user: " . $e->getMessage();
            header("Location: add_user.php");
            exit();
        }
    } else {
        $_SESSION['error'] = $error;
        header("Location: add_user.php");
        exit();
    }

} else {
    header("Location: add_user.php");
    exit();
}
