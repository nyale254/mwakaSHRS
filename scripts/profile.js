document.addEventListener("DOMContentLoaded", () => {
    console.log("Student profile loaded");
});

function initStudentList() {
    document.querySelectorAll(".clickable-row").forEach(row => {
        row.replaceWith(row.cloneNode(true));
    });

    document.querySelectorAll(".clickable-row").forEach(row => {
        row.addEventListener("click", () => {
            const studentId = row.getAttribute("data-id");
            window.location.href = "view_student.php?id=" + studentId;
        });
    });
}
export function initStudentFilterSearch() {
    const form = document.querySelector(".filter-form");

    if (!form) return;
    
    const searchInput = form.querySelector('input[name="search"]');
    const resetBtn = document.getElementById("btn-reset");
    form.addEventListener("submit", e => {
        e.preventDefault(); 
        const query = searchInput.value.trim();
        fetch(`student_list.php?search=${encodeURIComponent(query)}`)
            .then(res => res.text())
            .then(html => {
                document.getElementById("main-content").innerHTML = html;
                initStudentPage(); 
            });
    });

    resetBtn.addEventListener("click", e => {
        e.preventDefault();
        searchInput.value = "";
        fetch("student_list.php")
            .then(res => res.text())
            .then(html => {
                document.querySelector(".list-container").innerHTML = html;
                initStudentPage();
            });
    });
}
export function initStudentPage() {
    initStudentList();
    initStudentFilterSearch();

    const btn = document.getElementById("searchBtn");
    if (!btn) return;

    const newBtn = btn.cloneNode(true);
    btn.replaceWith(newBtn);

    newBtn.addEventListener("click", loadRecord);
}

function loadRecord() {
    const input = document.getElementById("student_id");
    if (!input) {
        console.warn("student_id input not found");
        return;
    }

    const query = input.value.trim();
    if (!query) return;

    const isId = /^\d+$/.test(query); 
    let url = '';
    if (isId) {
        url = `fetch_health_records.php?student_id=${query}`;
    } else {
        url = `fetch_health_records.php?student_name=${encodeURIComponent(query)}`;
    }

    fetch(url)
        .then(res => res.json())
        .then(data => {
            displayLatest(data.latest);
            displayHistory(data.history);

            let studentId = null;

            if (isId) {
                studentId = query; 
            } else if (data.latest && data.latest.student_id) {
                studentId = data.latest.student_id;
            } else if (data.history && data.history.length > 0) {
                studentId = data.history[0].student_id; 
            }

            if (studentId) {
                loadTreatmentHistory(studentId);
            } else {
                const container = document.getElementById("medicalHistory");
                if (container) container.innerHTML = "<p>No treatment history available.</p>";
            }
        })
        .catch(err => console.error("Error fetching records:", err));
}

function displayLatest(record) {
    const container = document.getElementById("latestVisit");

    if (!container) {
        console.warn("latestVisit not found");
        return;
    }

    if (!record) {
        container.innerHTML = "<p>No recent visit found</p>";
        return;
    }

    container.innerHTML = `
        <h3>Last Visit</h3>
        <p><strong>Date:</strong> ${record.visit_date}</p>
        <p><strong>Type:</strong> ${record.visit_type}</p>
        <p><strong>Complaint:</strong> ${record.complain}</p>
        <p><strong>Diagnosis:</strong> ${record.diagnosis}</p>
        <p><strong>Status:</strong> ${record.status}</p>
        <p><strong>Severity:</strong> ${record.severity}</p>
    `;
}

function displayHistory(records) {
    const container = document.getElementById("history");

    if (!container) {
        console.warn("history container not found");
        return;
    }

    if (!records || !records.length) {
        container.innerHTML = "<p>No history available</p>";
        return;
    }

    let html = "<h3>Visit History</h3>";

    records.forEach(r => {
        html += `
            <div class="history-card">
                <p><strong>${r.visit_date}</strong> (${r.visit_type})</p>
                <p>${r.complain}</p>
                <p><em>${r.diagnosis}</em></p>
                <hr>
            </div>
        `;
    });

    container.innerHTML = html;
}

window.loadTreatmentHistory = function(studentId) {
    const container = document.getElementById("medicalHistory");
    if (!container) return;

    fetch(`fetch_treatment.php?student_id=${studentId}`)
        .then(res => res.json())
        .then(data => {
            if (!data) {
                container.innerHTML = "<p>No treatment history found.</p>";
                return;
            }

            container.innerHTML = `
                <h3>Latest Treatment</h3>
                <div class="treatment-card">
                    <p><strong>Date:</strong> ${data.treatment_date}</p>
                    <p><strong>Category:</strong> ${data.category}</p>
                    <p><strong>Medication:</strong> ${data.medication}</p>
                    <p><strong>Symptoms:</strong> ${data.symptoms}</p>
                    <p><strong>Diagnosis:</strong> ${data.diagnosis}</p>
                    <p><strong>Dosage:</strong> ${data.dosage}</p>
                    <p><strong>Notes:</strong> ${data.notes}</p>
                    <p><strong>Referral:</strong> ${data.referral}</p>
                </div>
            `;
        })
        .catch(err => {
            console.error("Error fetching treatment history:", err);
            container.innerHTML = "<p>Error loading treatment history.</p>";
        });
}