<?php
session_start();
include "../connect.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Nurse Treatment | SHRS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{background:#f5f7fb;}
.card{border:none;border-radius:10px;}
</style>
</head>
<body>
    <div class="container mt-4">
        <h3 class="mb-4">Student Treatment</h3>
        <form id="treatmentForm" action="save_treat.php" method="POST">
            <div class="card p-3 mb-4">
                <h5>Student Information</h5>
                <div class="row">
                    <div class="col-md-4">
                        <label>Student Name</label>
                        <input type="text" id="student_name" class="form-control" placeholder="Type student name">
                    </div>


                    <div class="col-md-4">
                        <label>Student ID</label>
                        <input type="text" id="student_id" name="student_id" class="form-control" readonly>
                    </div>

                    <div class="col-md-4">
                        <label>Course</label>
                        <input type="text" id="course" name="course" class="form-control" readonly>
                    </div>
                    <ul class="list-group" id="studentList" style="position:absolute; z-index:1000; max-height:150px; overflow:auto;"></ul>

                </div>
            </div>

            <div class="card p-3 mb-4">
                <h5>Symptoms</h5>
                <textarea name="symptoms" class="form-control"></textarea>
            </div>

            <div class="card p-3 mb-4">

                <h5>Prescription</h5>

                <table class="table table-bordered" id="prescriptionTable">
                    <thead>
                    <tr>
                    <th>Medication</th>
                    <th>Dosage</th>
                    <th>Frequency</th>
                    <th>Quantity</th>
                    <th>Action</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <button type="button" class="btn btn-success" onclick="addRow()">Add Medication</button>
            </div>

            <div class="card p-3 mb-4">
                <h5>Doctor Referral</h5>
                <select name="referral" class="form-select">
                    <option value="">No Referral</option>
                    <option value="doctor">Refer to Doctor</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save Treatment</button>
        </form>

        <div class="card p-3 mt-4">

            <h5>Student Treatment History</h5>

            <table class="table table-striped" id="historyTable">
                <thead>
                <tr>
                <th>Date</th>
                <th>Diagnosis</th>
                <th>Treatment</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>

<script src="/Mwaka.SHRS.2/scripts/nurse_dashboard.js"></script>

</body>
</html>