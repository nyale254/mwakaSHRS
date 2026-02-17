<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHRS - Nurse Vaccination Management</title>
    <link rel="stylesheet" href="/Mwaka.SHRS.2/styles/vaccination.css">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo-text">
                <h1>SHRS| Nurse Vaccination Management panel</h1>
            </div>
            <div class="header-actions">
                <button class="notification-btn">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span class="notification-badge"></span>
                </button>
                <div class="user-profile">
                    <div class="user-avatar">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="user-info">
                        <p>Nurse Sarah</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <div>
                    <h2>Welcome back, Nurse Sarah</h2>
                    <div class="hero-buttons">
                        <button class="btn btn-primary">View Schedule</button>
                    </div>
                </div>
            </div>
        </section>

        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-content">
                    <div class="stat-info">
                        <p>Total Patients</p>
                        <p></p>
                    </div>
                    <div class="stat-icon icon-blue">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-content">
                    <div class="stat-info">
                        <p>Vaccinations Today</p>
                        <p></p>
                    </div>
                    <div class="stat-icon icon-green">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                </div>
            </div>


            <div class="stat-card">
                <div class="stat-card-content">
                    <div class="stat-info">
                        <p>This Month</p>
                        <p></p>
                    </div>
                    <div class="stat-icon icon-orange">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </section>
        <div class="main-grid">
            <section class="card">
                <div class="card-header">
                    <div class="card-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Today's Schedule
                    </div>
                    <span class="badge badge-secondary">Feb 15, 2026</span>
                </div>
                <div class="card-content">
                    <div class="schedule-list">
                        <div class="schedule-item">
                            <div class="schedule-info">
                                <h4>
                                    Emily Johnson
                                    <span class="badge badge-blue">scheduled</span>
                                </h4>
                                <div class="schedule-details">
                                    <span>
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        09:00 AM
                                    </span>
                                    <span>
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Room 101
                                    </span>
                                    <span class="vaccine-name">COVID-19 Booster</span>
                                </div>
                            </div>
                            <button class="btn-check-in">Check In</button>
                        </div>
                    </div>
                </div>
            </section>
            <div class="sidebar">
                <section class="card">
                    <div class="card-header">
                        <div class="card-title">Quick Actions</div>
                    </div>
                    <div class="card-content">
                        <div class="quick-actions-grid">
                            <button class="action-btn action-blue">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                                <span>Register Patient</span>
                            </button>
                            <button class="action-btn action-green">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                <span>Record Vaccination</span>
                            </button>
                            <button class="action-btn action-purple">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>View Reports</span>
                            </button>
                        </div>
                    </div>
                </section>

                <section class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            Vaccine Inventory
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="inventory-list">
                            <div class="inventory-item">
                                <h4>
                                    <span class="inventory-name">COVID-19 (Pfizer)</span>
                                    <span class="inventory-badge badge-outline-green">145 / 200</span>
                                </h4>
                                <div class="progress-bar">
                                    <div class="progress-fill progress-green" style="width: 72.5%"></div>
                                </div>
                                <p class="expiry-date">Expires: Aug 2026</p>
                            </div>

                            <div class="inventory-item">
                                <h4>
                                    <span class="inventory-name">Influenza</span>
                                    <span class="inventory-badge badge-outline-yellow">28 / 150</span>
                                </h4>
                                <div class="progress-bar">
                                    <div class="progress-fill progress-yellow" style="width: 18.7%"></div>
                                </div>
                                <p class="expiry-date">Expires: Jun 2026</p>
                            </div>

                            <div class="inventory-item">
                                <h4>
                                    <span class="inventory-name">Tdap</span>
                                    <span class="inventory-badge badge-outline-green">89 / 100</span>
                                </h4>
                                <div class="progress-bar">
                                    <div class="progress-fill progress-green" style="width: 89%"></div>
                                </div>
                                <p class="expiry-date">Expires: Dec 2026</p>
                            </div>

                            <div class="inventory-item">
                                <h4>
                                    <span class="inventory-name">
                                        Hepatitis B
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                    </span>
                                    <span class="inventory-badge badge-outline-red">12 / 100</span>
                                </h4>
                                <div class="progress-bar">
                                    <div class="progress-fill progress-red" style="width: 12%"></div>
                                </div>
                                <p class="expiry-date">Expires: Mar 2026</p>
                            </div>

                            <div class="inventory-item">
                                <h4>
                                    <span class="inventory-name">MMR</span>
                                    <span class="inventory-badge badge-outline-green">67 / 100</span>
                                </h4>
                                <div class="progress-bar">
                                    <div class="progress-fill progress-green" style="width: 67%"></div>
                                </div>
                                <p class="expiry-date">Expires: Oct 2026</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <section class="info-banner">
            <div class="info-banner-content">
                <div class="info-icon">
                    <img src="https://images.unsplash.com/photo-1614107574317-f4a5ac661331?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHx2YWNjaW5hdGlvbiUyMHZhY2NpbmUlMjBzeXJpbmdlfGVufDF8fHx8MTc3MTE2MDM2NXww&ixlib=rb-4.1.0&q=80&w=1080" alt="Vaccination">
                </div>
                <div class="info-text">
                    <h3>Vaccination Best Practices</h3>
                    <p>Remember to verify patient information, check for allergies, and document the vaccine lot number and administration site.
                      Always follow the CDC guidelines for proper vaccine storage and handling.
                    </p>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
