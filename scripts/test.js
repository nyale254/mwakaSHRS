document.addEventListener("DOMContentLoaded", loadDashboard);

function loadDashboard() {
    document.getElementById("loadingOverlay").style.display = "flex";

    fetch("admin_dashboard_data.php")
        .then(res => res.json())
        .then(data => {
            document.getElementById("loadingOverlay").style.display = "none";

            if (!data.success) return alert(data.message);

            loadStats(data.stats);
            loadTable(data.records);
            loadAlerts(data.alerts);
            loadCharts(data);
            document.getElementById("lastUpdated").innerText = data.last_updated;
        });
}

function loadStats(stats) {
    const grid = document.getElementById("statsGrid");

    grid.innerHTML = `
        <div class="stat-card">Students: ${stats.total_students}</div>
        <div class="stat-card">Visits: ${stats.total_visits}</div>
        <div class="stat-card">Pending: ${stats.pending_cases}</div>
        <div class="stat-card">Recovered: ${stats.recovered}</div>
    `;
}

function loadTable(records) {
    const tbody = document.getElementById("tableBody");

    tbody.innerHTML = "";

    records.forEach(r => {
        tbody.innerHTML += `
            <tr>
                <td>${r.student_id}</td>
                <td>${r.fullname}</td>
                <td>${r.age}</td>
                <td>${r.condition_name}</td>
                <td>${r.visit_date}</td>
                <td>${r.status}</td>
            </tr>
        `;
    });
}


// ================= ALERTS =================
function loadAlerts(alerts) {
    const list = document.getElementById("alertsList");

    list.innerHTML = "";

    alerts.forEach(a => {
        list.innerHTML += `<li>${a}</li>`;
    });
}


// ================= CHARTS =================
function loadCharts(data) {

    // Ailments chart
    new Chart(document.getElementById("ailmentsChart"), {
        type: 'bar',
        data: {
            labels: data.ailments.map(a => a.condition_name),
            datasets: [{
                label: 'Cases',
                data: data.ailments.map(a => a.total)
            }]
        }
    });

    // Trend chart
    new Chart(document.getElementById("trendChart"), {
        type: 'line',
        data: {
            labels: data.trend.map(t => t.month),
            datasets: [{
                label: 'Visits',
                data: data.trend.map(t => t.total)
            }]
        }
    });
}