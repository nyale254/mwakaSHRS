<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Nurse Report Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    body {
        font-family: Arial;
        background: #f4f6f9;
        padding: 20px;
    }
    .chart-container {
        width: 45%;
        display: inline-block;
        margin: 20px;
        background: #fff;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
</style>
</head>
<body>

<h2>Nurse Report Dashboard</h2>

<div class="chart-container">
    <canvas id="appointmentsChart"></canvas>
</div>

<div class="chart-container">
    <canvas id="visitsChart"></canvas>
</div>

<div class="chart-container">
    <canvas id="treatmentsChart"></canvas>
</div>

<div class="chart-container">
    <canvas id="medicineChart"></canvas>
</div>

<script>
fetch("nurse_report.php")
.then(res => res.json())
.then(data => {

    function createChart(canvasId, label, chartData) {
        new Chart(document.getElementById(canvasId), {
            type: 'bar',
            data: {
                labels: Object.keys(chartData),
                datasets: [{
                    label: label,
                    data: Object.values(chartData)
                }]
            }
        });
    }

    createChart("appointmentsChart", "Appointments Status", data.appointments);
    createChart("visitsChart", "Visits Type", data.visits);
    createChart("treatmentsChart", "Top Treatments", data.treatments);
    createChart("medicineChart", "Medicine Status", data.medicine);

});
</script>

</body>
</html>