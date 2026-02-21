<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Healthcare Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="\Mwaka.SHRS.2\styles\dashboard.css" rel="stylesheet">
    
</head>
<body>

<div class="sidebar">
    <div class="logo">HealthCare</div>
    <ul>
        <li><a href="#" class="active">Dashboard</a></li>
        <li><a href="#">Students</a></li>
        <li><a href="#">Health Records</a></li>
        <li><a href="#">Appointments</a></li>
        <li><a href="#">Medications</a></li>
        <li><a href="#">Vital Signs</a></li>
        <li><a href="#">Settings</a></li>
    </ul>
</div>

<!-- MAIN -->
<div class="main">

    <!-- TOPBAR -->
    <div class="topbar">
        <div class="search">
            <input type="text" placeholder="Search students, records, appointments...">
        </div>
        <div class="profile">
            <span>Dr. Sarah Johnson</span>
            <img src="https://i.pravatar.cc/100" alt="profile">
        </div>
    </div>

    <!-- CONTENT -->
    <div class="content">
        <h1>Dashboard Overview</h1>
        <p class="subtitle">Welcome back! Here's what's happening with student health records today.</p>

        <!-- CARDS -->
        <div class="cards">
            <div class="card">
                <h3>Total Students</h3>
                <div class="number">1,284</div>
                <div class="increase">+12.5% vs last month</div>
            </div>

            <div class="card">
                <h3>Health Records</h3>
                <div class="number">3,456</div>
                <div class="increase">+8.2% vs last month</div>
            </div>

            <div class="card">
                <h3>Today's Appointments</h3>
                <div class="number">24</div>
                <div class="decrease">-3.1% vs last month</div>
            </div>

            <div class="card">
                <h3>Critical Cases</h3>
                <div class="number">7</div>
                <div class="increase">+2.4% vs last month</div>
            </div>
        </div>

        <!-- GRID SECTION -->
        <div class="grid-2">
            <div class="box">
                <h2>Health Records Trend</h2>
                <p>Monthly health services overview</p>
                <div class="chart"></div>
            </div>

            <div class="box">
                <h2>Today's Appointments</h2>

                <div class="appointment">
                    Emma Wilson
                    <span class="status confirmed">Confirmed</span>
                    <div class="time">09:00 AM</div>
                </div>

                <div class="appointment">
                    James Smith
                    <span class="status pending">Pending</span>
                    <div class="time">10:30 AM</div>
                </div>

                <div class="appointment">
                    Olivia Brown
                    <span class="status confirmed">Confirmed</span>
                    <div class="time">11:00 AM</div>
                </div>

                <div class="appointment">
                    Noah Davis
                    <span class="status confirmed">Confirmed</span>
                    <div class="time">02:00 PM</div>
                </div>
            </div>
        </div>

        <!-- RECENT ACTIVITIES -->
        <div class="box">
            <h2>Recent Activities</h2>

            <div class="activity">
                New health record added
                <div class="time">2 minutes ago</div>
            </div>

            <div class="activity">
                New student registered
                <div class="time">15 minutes ago</div>
            </div>

            <div class="activity">
                Appointment scheduled
                <div class="time">1 hour ago</div>
            </div>

            <div class="activity">
                Medication prescribed
                <div class="time">2 hours ago</div>
            </div>

            <div class="activity">
                Lab results uploaded
                <div class="time">3 hours ago</div>
            </div>

        </div>

    </div>
</div>

</body>
</html>