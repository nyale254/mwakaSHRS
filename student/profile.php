<?php
session_start();
include "../connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "
    SELECT
        s.student_id,s.full_name, s.reg_no,s.gender, s.DoB AS date_of_birth ,s.phone,s.course,s.year_of_study,
        s.address,s.email,s.emergency_contact, s.status,
        s.blood_type,
        s.profile_photo
    FROM students s
    WHERE s.student_id = ?
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$student) {
    die("Student profile not found.");
}
$default_photo = "/Mwaka.SHRS.2/assets/profile.png";

if (!empty($student['profile_photo']) && 
    file_exists("../uploads/students/" . $student['profile_photo'])) {

    $photo = "/Mwaka.SHRS.2/uploads/students/" . $student['profile_photo'];
} else {
    $photo = $default_photo;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile | SHRS</title>
    <link rel="stylesheet" href="/Mwaka.SHRS.2/styles/profile.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=person" />
</head>
<body>

<div class="topbar">
    <h2>SHRS</h2>
    <div class="topbar-right">
        <span><?= htmlspecialchars($student['full_name']) ?></span>
        <a href="/Mwaka.SHRS.2/logout.php" class="logout">Logout</a>
    </div>
</div>

<div class="container">

  <?php
    $has_photo = !empty($student['profile_photo']) && 
                file_exists("../uploads/students/" . $student['profile_photo']);

    if ($has_photo) {
        $photo_path = "/Mwaka.SHRS.2/uploads/students/" . $student['profile_photo'];
    }
    ?>

    <div class="profile-header">

        <?php if ($has_photo): ?>
            
            <img src="<?= htmlspecialchars($photo_path) ?>" 
                alt="Profile Photo" 
                class="profile-image">

        <?php else: ?>

            <div class="profile-avatar">
                <svg xmlns="http://www.w3.org/2000/svg" 
                    viewBox="0 -960 960 960">
                    <path d="M367-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Z"/>
                </svg>
            </div>

        <?php endif; ?>

        <div class="profile-info">
            <h3><?= htmlspecialchars($student['full_name']) ?></h3>
            <p><?= htmlspecialchars($student['reg_no']) ?></p>
            <span class="status <?= strtolower($student['status']) ?>">
                <?= htmlspecialchars($student['status']) ?>
            </span>
        </div>

    </div>

    <div class="card">
        <h4>Personal Information</h4>
        <div class="grid">
             <p>
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f">
                    <path d="M367-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm296.5-343.5Q560-607 560-640t-23.5-56.5Q513-720 480-720t-56.5 23.5Q400-673 400-640t23.5 56.5Q447-560 480-560t56.5-23.5ZM480-640Zm0 400Z"/>
                </svg>
                <strong>Fullname:</strong> <?= $student['full_name'] ?>
            </p>

            <p><strong>Gender:</strong> <?= $student['gender'] ?></p>

            <p>
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f">
                    <path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Zm280 240q-17 0-28.5-11.5T440-440q0-17 11.5-28.5T480-480q17 0 28.5 11.5T520-440q0 17-11.5 28.5T480-400Zm-188.5-11.5Q280-423 280-440t11.5-28.5Q303-480 320-480t28.5 11.5Q360-457 360-440t-11.5 28.5Q337-400 320-400t-28.5-11.5ZM640-400q-17 0-28.5-11.5T600-440q0-17 11.5-28.5T640-480q17 0 28.5 11.5T680-440q0 17-11.5 28.5T640-400ZM480-240q-17 0-28.5-11.5T440-280q0-17 11.5-28.5T480-320q17 0 28.5 11.5T520-280q0 17-11.5 28.5T480-240Zm-188.5-11.5Q280-263 280-280t11.5-28.5Q303-320 320-320t28.5 11.5Q360-297 360-280t-11.5 28.5Q337-240 320-240t-28.5-11.5ZM640-240q-17 0-28.5-11.5T600-280q0-17 11.5-28.5T640-320q17 0 28.5 11.5T680-280q0 17-11.5 28.5T640-240Z"/>
                </svg>
                <strong>Date of Birth:</strong> <?= $student['date_of_birth'] ?>
            </p>

            <p>
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f">
                    <path d="M798-120q-125 0-247-54.5T329-329Q229-429 174.5-551T120-798q0-18 12-30t30-12h162q14 0 25 9.5t13 22.5l26 140q2 16-1 27t-11 19l-97 98q20 37 47.5 71.5T387-386q31 31 65 57.5t72 48.5l94-94q9-9 23.5-13.5T670-390l138 28q14 4 23 14.5t9 23.5v162q0 18-12 30t-30 12ZM241-600l66-66-17-94h-89q5 41 14 81t26 79Zm358 358q39 17 79.5 27t81.5 13v-88l-94-19-67 67ZM241-600Zm358 358Z"/>
                </svg>
                <strong>Phone:</strong> <?= $student['phone'] ?>
            </p>

            <p>
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f">
                    <path d="M480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480v58q0 59-40.5 100.5T740-280q-35 0-66-15t-52-43q-29 29-65.5 43.5T480-280q-83 0-141.5-58.5T280-480q0-83 58.5-141.5T480-680q83 0 141.5 58.5T680-480v58q0 26 17 44t43 18q26 0 43-18t17-44v-58q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93h200v80H480Zm85-315q35-35 35-85t-35-85q-35-35-85-35t-85 35q-35 35-35 85t35 85q35 35 85 35t85-35Z"/>
                </svg>
                <strong>Email:</strong> <?= $student['email'] ?>
            </p>

            <p>
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f">
                    <path d="M120-120v-560h160v-160h400v320h160v400H520v-160h-80v160H120Zm80-80h80v-80h-80v80Zm0-160h80v-80h-80v80Zm0-160h80v-80h-80v80Zm160 160h80v-80h-80v80Zm0-160h80v-80h-80v80Zm0-160h80v-80h-80v80Zm160 320h80v-80h-80v80Zm0-160h80v-80h-80v80Zm0-160h80v-80h-80v80Zm160 480h80v-80h-80v80Zm0-160h80v-80h-80v80Z"/>
                </svg>
                <strong>Address:</strong> <?= $student['address'] ?>
            </p>

            <p>
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f">
                    <path d="M760-60q-15 0-28-7t-21-19q-121-22-213.5-108T368-406q-96 45-152 132.5T160-80H80q0-134 73.5-244T350-486q-21-115-3.5-207.5T420-843q2-24 19-40.5t41-16.5q25 0 42.5 17.5T540-840q0 25-17.5 42.5T480-780h-4q-2 0-5-1-22 25-35 61.5T419-638q20-20 46.5-34.5T524-695q30-8 64.5-10.5t72.5.5q8-8 18-11.5t21-3.5q25 0 42.5 17.5T760-660q0 25-17.5 42.5T700-600q-14 0-27.5-6.5T651-625q-33-2-63.5.5T533-614q-39 13-61.5 38T443-512q28-5 47.5-6.5T576-520q8-10 19.5-15t24.5-5q25 0 42.5 17.5T680-480q0 25-17.5 42.5T620-420q-13 0-24.5-5T576-440q-63 0-83 1.5t-45 6.5q13 34 51 52t99 20q29 2 62.5-1t67.5-9q8-14 22-22t30-8q25 0 42.5 17.5T840-340q0 25-17.5 42.5T780-280q-10 0-18.5-3t-16.5-9q-34 6-66.5 9.5T617-279q-29 0-55-3t-49-9q38 49 92.5 82.5T720-164q8-8 18.5-12t21.5-4q25 0 42.5 17.5T820-120q0 25-17.5 42.5T760-60Z"/>
                </svg>
                <strong>Allergy:</strong> <?//= $student['allergy_type'] ?>
            </p>
        </div>
    </div>

    <div class="card">
        <h4>Academic Information</h4>
        <div class="grid">
            <p><strong>Course:</strong> <?= $student['course'] ?></p>
            <p><strong>Year of Study:</strong> <?= $student['year_of_study'] ?></p>
        </div>
    </div>

    <div class="card highlight">
        <h4>Health Information</h4>
        <div class="grid">
            <p>
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f">
                    <path d="M251.5-174Q160-268 160-408q0-100 79.5-217.5T480-880q161 137 240.5 254.5T800-408q0 140-91.5 234T480-80q-137 0-228.5-94ZM652-230.5Q720-301 720-408q0-73-60.5-165T480-774Q361-665 300.5-573T240-408q0 107 68 177.5T480-160q104 0 172-70.5ZM360-240h240v-80H360v80Zm80-120h80v-80h80v-80h-80v-80h-80v80h-80v80h80v80Zm40-120Z"/>
                </svg>
                <strong>Blood Group:</strong> <?= $student['blood_type'] ?>
            </p>
            <p><strong>Allergies:</strong> <?//= $student['allergies'] ?: 'None' ?></p>
            <p>
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f">
                    <path d="M760-480q0-117-81.5-198.5T480-760v-80q75 0 140.5 28.5t114 77q48.5 48.5 77 114T840-480h-80Zm-160 0q0-50-35-85t-85-35v-80q83 0 141.5 58.5T680-480h-80Zm198 360q-125 0-247-54.5T329-329Q229-429 174.5-551T120-798q0-18 12-30t30-12h162q14 0 25 9.5t13 22.5l26 140q2 16-1 27t-11 19l-97 98q20 37 47.5 71.5T387-386q31 31 65 57.5t72 48.5l94-94q9-9 23.5-13.5T670-390l138 28q14 4 23 14.5t9 23.5v162q0 18-12 30t-30 12ZM241-600l66-66-17-94h-89q5 41 14 81t26 79Zm358 358q39 17 79.5 27t81.5 13v-88l-94-19-67 67ZM241-600Zm358 358Z"/>
                </svg>
                <strong>Emergency contact:</strong> <?= $student['emergency_contact'] ?>
            </p>
        </div>
    </div>

    <div class="actions">
        <a href="appointment.php" class="btn secondary">Request Appointment</a>
    </div>

</div>

<script src="/Mwaka.SHRS.2/scripts/profile.js"></script>
</body>
</html>
