<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SHRS System</title>
    <link rel="stylesheet" href="styles/index.css">
</head>
<body>

<header class="header">
    <h1>SHRS System</h1>
    <p>School Health Record Management System</p>
</header>
<nav class="nav">
    <a  href="index.php" class="active">Home</a>
    <a href="#" class="nav-link" data-page="about_us.php">About</a>
    <a href="#" class="nav-link" data-page="contact.php">Contact</a>
</nav>


<main class="content" id="main-content">
    <h2>Welcome to SHRS</h2>
    <p>
        SHRS is a digital school healthcare system designed to manage
        student medical records, appointments, and treatments efficiently.
    </p>

    <div class="buttons">
        <a href="login.php" class="btn">Login to System</a>
    </div>
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
