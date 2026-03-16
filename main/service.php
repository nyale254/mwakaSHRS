<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>SHRS Services</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body{
font-family: Arial, sans-serif;
margin:0;
background:#f4f6f8;
}
.services-page{
margin-top:-70px;
padding:50px;
text-align:center;
}
.services-page h2{
font-size:32px;
margin-bottom:10px;
color:blue;
}
.service-head{
    background:white;
    padding:5px;
    border-radius:20px;
    width:80%;
    margin-left:120px;
}
.service-intro{
color:#555;
margin-bottom:40px;
}

.service-container{
display:flex;
flex-wrap:wrap;
gap:30px;
justify-content:center;
}

.card{
background:white;
padding:30px;
border-radius:12px;
width:260px;
box-shadow:0 5px 20px rgba(0,0,0,0.1);
transition:all 0.3s ease;
animation:fadeUp 0.8s ease;
}

.card:hover{
transform:translateY(-8px);
box-shadow:0 10px 25px rgba(0,0,0,0.2);
}

.service-icon{
font-size:40px;
color:#1e81c4;
margin-bottom:15px;
}

.card h3{
margin-bottom:10px;
}

.card p{
color:#555;
font-size:14px;
}

@keyframes fadeUp{
from{
opacity:0;
transform:translateY(30px);
}
to{
opacity:1;
transform:translateY(0);
}
}
</style>
</head>
<body>
<main class="services-page">
    <div class="service-head">
        <h2>Our Health Services</h2>
        <p class="service-intro">
            The Student Health Record System provides healthcare services
            to support students and ensure efficient medical management.
        </p>
    </div>
    
    <div class="service-container">
        <div class="card">
            <i class="fas fa-calendar-check service-icon"></i>
            <h3>Online Appointment</h3>
            <p>Students can easily book appointments with the campus clinic.</p>
        </div>

        <div class="card">
            <i class="fas fa-file-medical service-icon"></i>
            <h3>Medical Records</h3>
            <p>Secure storage and quick access to student medical history.</p>
        </div>

        <div class="card">
            <i class="fas fa-pills service-icon"></i>
            <h3>Prescription Management</h3>
            <p>Doctors record treatments and medications given to students.</p>
        </div>

        <div class="card">
            <i class="fas fa-flask service-icon"></i>
            <h3>Laboratory Tests</h3>
            <p>Lab requests and results are recorded and tracked digitally.</p>
        </div>

        <div class="card">
            <i class="fas fa-syringe service-icon"></i>
            <h3>Vaccination Records</h3>
            <p>The system tracks vaccinations administered to students.</p>
            </div>

        <div class="card">
            <i class="fas fa-user-md service-icon"></i>
            <h3>Health Counseling</h3>
            <p>Students receive health guidance and counseling support.</p>
        </div>
    </div>

</main>
</body>
</html>