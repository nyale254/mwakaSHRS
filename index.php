<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Healyh Record System</title>
    <link rel="stylesheet" href="styles/index.css">
</head>
<body>

<header class="header">
    <h1>SHRS System</h1>
    <p>School Health Record Management System</p>

    <nav class="nav">
        <a  href="index.php" class="active">Home</a>
        <a href="#" class="nav-link" data-page="/Mwaka.SHRS.2/main/service.php">Services</a>
        <a href="#" class="nav-link" data-page="/Mwaka.SHRS.2/main/about_us.php">About</a>
        <a href="#" class="nav-link" data-page="/Mwaka.SHRS.2/main/contact.php">Contact</a>

        <div class="auth-buttons">
            <a href="/Mwaka.SHRS.2/login.php" class="btn2">Login</a>
        </div>
    </nav>

</header>

<main class="content" id="main-content">
    <section class="top-header">
        <h2>Welcome to SHRS</h2>
        <p>  
            A digital platform that allows students to book appointments,
            access medical records, and communicate with campus health services.
        </p>

        <div class="btn">
            <a href="/Mwaka.SHRS.2/login.php">Login to System</a>
        </div>
    </section>
    
    <section id="services" class="services">
        <h2>Our Services</h2>
        <div class="service-container">
            <div class="card">
                <h3>Online Appointment</h3>
                <p>Students can easily schedule medical appointments online.</p>
            </div>

            <div class="card">
                <h3>Medical Records</h3>
                <p>Secure digital storage of student health records.</p>
            </div>

            <div class="card">
                <h3>Prescription Tracking</h3>
                <p>Doctors can record treatments and prescriptions.</p>
            </div>

            <div class="card">
                <h3>SMS Notifications</h3>
                <p>Automatic reminders for appointments and updates.</p>
            </div>
        </div>
    </section>

    <section id="about" class="about">
        <div>
            <h2>About The System</h2>
            <p>
            The Student Health Record System improves healthcare service delivery
            within the campus by digitizing medical records, reducing paperwork,
            and enabling quick access to student health information.
            </p>
        </div>
    </section>
</main>
<footer class="footer">
    <p>&copy; 2026 titusNyale System. All Rights Reserved.</p>
</footer>

<script>
const links = document.querySelectorAll('.nav-link');
const content = document.getElementById('main-content');

links.forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();

        links.forEach(l => l.classList.remove('active'));
        this.classList.add('active');

        const page = this.getAttribute('data-page');

        fetch(page)
            .then(response => response.text())
            .then(html => {
                content.innerHTML = html;

                if(page.includes('dashboard')) {
                    initDashboardChart(); 
                }
            })
            .catch(err => {
                content.innerHTML = "<p>Error loading page.</p>";
                console.error(err);
            });
    });
});
</script>
</body>
</html>
