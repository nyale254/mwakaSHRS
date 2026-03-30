<body>

<div class="control-panel">
    <h1>📋 Student Health Report Generator</h1>
    <p>Generate and export comprehensive student health reports.</p>

    <div class="button-container">
        <button class="btn btn-primary" onclick="window.print()">
            🖨️ Export / Save as PDF
        </button>

        <button class="btn btn-secondary" onclick="updateDate()">
            📅 Refresh Date
        </button>

        <button class="btn btn-secondary" onclick="fillSampleData()">
            📝 Load Demo Data
        </button>
    </div>
</div>

<!-- ===== REPORT ===== -->
<div class="report-container">

    <!-- HEADER -->
    <div class="report-header">
        <div class="report-logo">⚕️</div>
        <h1 class="report-title">STUDENT HEALTH REPORT</h1>
        <p class="report-subtitle">Medical Records & Health Assessment</p>

        <div class="report-info">
            <div>Report ID: <span id="reportId">SHR-2026-001234</span></div>
            <div>Date: <span id="reportDate"></span></div>
            <div>Academic Year: <span>2025-2026</span></div>
        </div>
    </div>

    <!-- STUDENT INFO -->
    <div class="section">
        <div class="section-header">
            👤 Student Information
        </div>

        <div class="info-grid" id="studentInfo">
            <!-- Dynamic Data -->
        </div>
    </div>

    <!-- SUMMARY -->
    <div class="section">
        <div class="section-header">📊 Health Summary</div>

        <div class="summary-grid" id="summaryData">
            <!-- Dynamic Data -->
        </div>
    </div>

    <!-- CHARTS -->
    <div class="section">
        <div class="section-header">📈 Statistics Overview</div>

        <div class="chart-container">
            <canvas id="visitsChart"></canvas>
            <canvas id="appointmentsChart"></canvas>
        </div>
    </div>

    <!-- MEDICAL TABLE -->
    <div class="section">
        <div class="section-header">🏥 Physical Examination</div>

        <table id="examTable">
            <thead>
                <tr>
                    <th>Parameter</th>
                    <th>Value</th>
                    <th>Range</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- TIMELINE -->
    <div class="section">
        <div class="section-header">📅 Medical History</div>

        <div class="timeline" id="timeline">
            <!-- Dynamic -->
        </div>
    </div>

    <!-- FOOTER -->
    <div class="report-footer">
        <p class="footer-note">
            Confidential medical report. Authorized use only.
        </p>

        <div class="footer-contact">
            <strong>Student Health System</strong><br>
            Email: health@school.com | Phone: +254 XXX XXX XXX
        </div>
    </div>

</div>

<!-- ===== SCRIPTS ===== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// ===== DATE =====
function updateDate() {
    const today = new Date();
    document.getElementById('reportDate').textContent =
        today.toLocaleDateString('en-GB', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
}

// ===== SAMPLE DATA =====
function fillSampleData() {

    // Student Info
    document.getElementById("studentInfo").innerHTML = `
        <div class="info-box"><div class="info-label">Name</div><div class="info-value">John Doe</div></div>
        <div class="info-box"><div class="info-label">Student ID</div><div class="info-value">STU-1001</div></div>
        <div class="info-box"><div class="info-label">Age</div><div class="info-value">15</div></div>
        <div class="info-box"><div class="info-label">Blood Group</div><div class="info-value">O+</div></div>
    `;

    // Summary
    document.getElementById("summaryData").innerHTML = `
        <div class="summary-card"><div>Total Visits</div><h2>12</h2></div>
        <div class="summary-card"><div>Appointments</div><h2>8</h2></div>
        <div class="summary-card"><div>Treatments</div><h2>5</h2></div>
        <div class="summary-card"><div>Medicines</div><h2>20</h2></div>
    `;

    // Charts
    createChart("visitsChart", ["Checkup","Emergency","Vaccination"], [5,3,4], "Visits");
    createChart("appointmentsChart", ["Pending","Confirmed","Rejected"], [2,5,1], "Appointments");

    // Table
    document.querySelector("#examTable tbody").innerHTML = `
        <tr><td>Height</td><td>160 cm</td><td>150-170</td><td>Normal</td></tr>
        <tr><td>Weight</td><td>50 kg</td><td>45-60</td><td>Normal</td></tr>
    `;

    // Timeline
    document.getElementById("timeline").innerHTML = `
        <div class="timeline-item">
            <strong>March 2026</strong>
            <p>General Checkup - Healthy</p>
        </div>
        <div class="timeline-item">
            <strong>Feb 2026</strong>
            <p>Vaccination Completed</p>
        </div>
    `;
}

// ===== CHART FUNCTION =====
function createChart(id, labels, data, label) {
    new Chart(document.getElementById(id), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: data
            }]
        }
    });
}

// ===== INIT =====
window.onload = () => {
    updateDate();
    fillSampleData();
};
</script>

</body>