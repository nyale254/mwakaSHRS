setTimeout(() => {
    location.reload();
}, 90000000);

import { initStudentFilterSearch, initStudentPage } from './profile.js';
document.addEventListener("DOMContentLoaded", function() {
    initStudentSearch();
    loadNotifications(); 
    setupSearchObserver();

    const btn = document.getElementById("notification-btn");
    if (btn) {
        btn.addEventListener("click", toggleNotifications);
    }
});

function setupSearchObserver() {
    const observer = new MutationObserver(function(mutations) {
        const searchInput = document.getElementById("searchInput");
        const resultsBox = document.getElementById("searchResults");
        const clearBtn = document.getElementById("clearSearch");
        
        if (searchInput && resultsBox && clearBtn && !searchInput.dataset.initialized) {
            initStudentSearch();
        }
    });
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}

/*document.querySelectorAll(".clickable-row").forEach(row => {
    row.addEventListener("click", () => {
        const studentId = row.getAttribute("data-id");
        window.location.href = "view_student.php?id=" + studentId;
    });
});*/

function toggleNotifications() {
    const dropdown = document.getElementById("notificationDropdown");
    const badge = document.getElementById("notificationBadge");

    if (!dropdown) return;

    dropdown.classList.toggle("show");

    if (dropdown.classList.contains("show")) {
        fetch("/Mwaka.SHRS.2/student/mark_notifications_read.php")
            .then(res => res.json())
            .then(() => {
                if (badge) {
                    badge.innerText = "0";
                    badge.style.display = "none";
                }
                loadNotifications();
            })
            .catch(err => console.error("Error marking notifications:", err));
    }
}


document.addEventListener("click", function (e) {
    const btn = document.querySelector(".notification-btn");
    const dropdown = document.getElementById("notificationDropdown");

   if (!btn || !dropdown) return;

    if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.remove("show"); 
    }
});

const content = document.getElementById('main-content');

document.addEventListener('click', function(e) {
    const link = e.target.closest('.nav-link, [data-page], .load-page');
    if (!link) return;

    const page = link.dataset.page || link.getAttribute('href');
    if (!page) return;

    e.preventDefault(); 

    if (link.classList.contains('nav-link')) {
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        link.classList.add('active');
    }
    fetch(page)
        .then(response => response.text())
        .then(html => {
            content.innerHTML = html;

            if (page.includes('health_record')) {
                initStudentPage();
            }
            if (page.includes('dashboard')) initDashboardChart();
            if (page.includes('treatment')) initTreatmentPage();
            if (page.includes('student_list')) {
                initStudentSearch();
                initStudentPage();
            }
            if (page.includes('medication')) initMedicationPage();
            if (page.includes('appointment')) {
                fetchAppointments();
                setInterval(fetchAppointments, 5000);
            }
        })
        .catch(err => {
            console.error(err);
            content.innerHTML = "<p>Error loading page.</p>";
        });
});

/*appointment.php js*/
function fetchAppointments() {
    fetch('fetch_appointment.php')
    .then(res => res.json())
    .then(data => {
        const table = document.getElementById('appointmentsTable');
        table.innerHTML = '';

        data.forEach(a => {
            const statusText = a.status.charAt(0).toUpperCase() + a.status.slice(1);

            let buttons = '';
            if(a.status === 'pending') {
                buttons = `
                    <button class="btn confirm" onclick="updateStatus(${a.appointment_id}, 'confirmed', ${a.user_id})">Confirm</button>
                    <button class="btn reject" onclick="updateStatus(${a.appointment_id}, 'rejected', ${a.user_id})">Reject</button>
                `;
            } else if(a.status === 'confirmed' && a.can_reschedule) {
                buttons = `
                    <button class="btn reschedule" onclick="rescheduleAppointment(${a.appointment_id}, ${a.user_id}, '${a.appointment_date}')">Reschedule</button>
                `;
            } else {
                buttons = '-';
            }

            table.innerHTML += `
                <tr id="appointment-${a.appointment_id}">
                    <td>${a.student_name}</td>
                    <td>${a.appointment_date}</td>
                    <td>${a.reason}</td>
                    <td><span class="status ${a.status}">${statusText}</span></td>
                    <td>${buttons}</td>
                </tr>
            `;
        });
    });
}

window.updateStatus = function(appointmentId, action, studentId) {
    if(!confirm(`Are you sure you want to ${action} this appointment?`)) return;

    fetch('/Mwaka.SHRS.2/nurse/update_appointment.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({appointment_id: appointmentId, status: action, user_id: studentId})
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) fetchAppointments();
        else alert('Error: ' + data.message);
    });
}

// Handle rescheduling
window.rescheduleAppointment = function(appointmentId, studentId, currentDate) {
    const modal = document.getElementById('rescheduleModal');
    const closeBtn = modal.querySelector('.close');

    document.getElementById('appointmentId').value = appointmentId;
    document.getElementById('studentId').value = studentId;
    document.getElementById('newDate').value = currentDate.replace(' ', 'T');

    modal.style.display = 'block';

    closeBtn.onclick = () => modal.style.display = 'none';
    window.onclick = (e) => { if(e.target == modal) modal.style.display = 'none'; };

    const form = document.getElementById('rescheduleForm');
    form.onsubmit = (e) => {
        e.preventDefault();
        const newDate = document.getElementById('newDate').value;

        fetch('/Mwaka.SHRS.2/nurse/update_appointment.php', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({ appointment_id: appointmentId, status: 'reschedule', new_date: newDate, user_id: studentId })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                alert('Appointment rescheduled!');
                modal.style.display = 'none';
                fetchAppointments();
            } else {
                alert('Error: ' + data.message);
            }
        });
    };
}
function loadNotifications() {
    const dropdownList = document.querySelector("#notificationDropdown ul");
    const pageBox = document.getElementById("notifications");
    const badge = document.getElementById("notificationBadge");

    if (!dropdownList || !pageBox || !badge) return;

    fetch("/Mwaka.SHRS.2/nurse/fetch_notification.php")
        .then(res => res.json())
        .then(data => {
            dropdownList.innerHTML = "";
            pageBox.innerHTML = ""; 
            let unreadCount = 0;

            if (!Array.isArray(data) || data.length === 0) {
                dropdownList.innerHTML = "<li>No notifications</li>";
                pageBox.innerHTML = "<p>No notifications available.</p>";
                badge.innerText = "0";
                badge.style.display = "none";
                return;
            }

            data.forEach(note => {
                const status = parseInt(note.status);
                if (status === 0) unreadCount++;
                const time = note.created_at ? new Date(note.created_at).toLocaleString() : "";

                dropdownList.innerHTML += `
                    <li class="${status === 0 ? 'unread' : 'read'}" data-id="${note.notification_id}">
                        ${note.message}
                        <span class="time">${time}</span>
                    </li>
                `;

                pageBox.innerHTML += `
                    <div class="notification-item ${status === 0 ? 'unread' : 'read'}" data-id="${note.notification_id}">
                        <p>${note.message}</p>
                        <small>${time}</small>
                    </div>
                `;
            });

            badge.innerText = unreadCount;
            badge.style.display = unreadCount > 0 ? "inline-block" : "none";
        })
        .catch(err => console.error("Error fetching notifications:", err));
}


// treatment.php js
function initTreatmentPage(){
const studentName = document.getElementById("student_name");
const studentId = document.getElementById("student_id");
const course = document.getElementById("course");
const studentList = document.getElementById("studentList");

if(!studentName) return; 

studentName.addEventListener("keyup", function(){

    let query = this.value;

    if(query.length < 2){
        studentList.innerHTML = "";
        return;
    }

    fetch("search_student.php?name="+query)
    .then(res => res.json())
    .then(data => {
        studentList.innerHTML = "";

        data.forEach(student => {
            let li = document.createElement("li");
            li.className = "list-group-item list-group-item-action";
            li.textContent = student.full_name + " ("+student.student_id+")";
            li.dataset.id = student.student_id;
            li.dataset.course = student.course;

            li.addEventListener("click", function(){
                studentName.value = student.full_name;
                studentId.value = student.student_id;
                course.value = student.course;
                studentList.innerHTML = "";

                loadHistory(student.student_id);
            });

            studentList.appendChild(li);
        });
    });

});

window.addRow = function(){
    let table = document.querySelector("#prescriptionTable tbody");
    let row = document.createElement("tr");
    row.innerHTML = `
    <td>
        <select name="medication[]" class="form-control" required>
            <option value="">Select Medication</option>
        </select>
    </td>
    <td><input name="prescribed_dosage[]" class="form-control" required></td>
    <td><input name="frequency[]" class="form-control" required></td>
    <td><input type="number" name="quantity[]" class="form-control" min="1" required></td>
    <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">Remove</button></td>
    `;

    table.appendChild(row);
    let select = row.querySelector("select");
    
    fetch("get_medications.php")
    .then(res => res.json())
    .then(data => {
        data.forEach(med => {
            let option = document.createElement("option");
            option.value = med.id; 
            option.textContent = med.name;
            select.appendChild(option);
        });
    });
}

window.loadHistory = function(student_id){

    fetch("get_history.php?id="+student_id)
    .then(res=>res.json())
    .then(data=>{
        let table = document.querySelector("#historyTable tbody");
        table.innerHTML="";

        data.forEach(row=>{
            table.innerHTML+=`
            <tr>
            <td>${row.created_at}</td>
            <td>${row.diagnosis}</td>
            <td>${row.treatment}</td>
            </tr>
            `;
        });
    });

}

const form = document.getElementById("treatmentForm");
if(form){
form.addEventListener("submit", function(e){
    e.preventDefault();
    const formData = new FormData(this);

    fetch("save_treat.php",{
        method:"POST",
        body:formData
    })

    .then(res => res.text())

    .then(data => {

        if(data.toLowerCase().includes("saved")){

            Swal.fire({
                icon:'success',
                title:'Success',
                text:'Treatment saved successfully'
            }).then(()=>{
                location.reload();
            });

        }else{

            window.Swal.fire({
                icon:'error',
                title:'Error',
                text:data
            });

        }

    });

});
}

}
//medication.php
function initMedicationPage(){
    setInterval(()=> location.reload(), 120000);
    const showBtn = document.getElementById("showAddMedication");
    const closeBtn = document.getElementById("closeModal");
    const modal = document.getElementById("addMedicationModal");
    const addBtn = document.getElementById("addRowBtn");

    if(!showBtn) return;

    showBtn.addEventListener("click", ()=>{
        modal.style.display = "flex";
    });

    closeBtn.addEventListener("click", ()=>{
        modal.style.display = "none";
    });

    window.onclick = function(e){
        if(e.target == modal){
            modal.style.display = "none";
        }
    }

    if (addBtn) {
        addBtn.addEventListener("click", addMedicationRow);
    }
}
document.addEventListener("DOMContentLoaded", function () {
    const expiryInput = document.getElementById("expiry_date");
    const statusText = document.getElementById("statusText");
    const statusInput = document.getElementById("status");

    if (expiryInput && statusText && statusInput) {
        function updateStatus() {
            const selectedDate = new Date(expiryInput.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (selectedDate < today) {
                statusText.innerText = "Expired";
                statusInput.value = "expired";
            } else {
                statusText.innerText = "Valid";
                statusInput.value = "valid";
            }
        }

        expiryInput.addEventListener("change", updateStatus);
        if (expiryInput.value) updateStatus();
    }
});

window.addMedicationRow = function() {
    let table = document.getElementById("medTable");

    if (!table) return;

    let row = table.insertRow();

    row.innerHTML = `
        <td><input type="text" name="med_name[]" required></td>
        <td><input type="text" name="dosage[]" required></td>
        <td><input type="number" name="quantity[]" required></td>
        <td><input type="number" name="max_stock_level[]" required></td>
        <td><input type="number" name="reorder_level[]" required></td>
        <td><input type="text" name="supplier[]" required></td>
        <td><input type="text" name="batch_number[]" required></td>
        <td><input type="text" name="storage_location[]" required></td>
        <td><input type="date" name="expiry_date[]" required></td>
        <td><button type="button" class="remove-btn">❌</button></td>
    `;
}

document.addEventListener("click", function(e) {
    if (e.target.classList.contains("remove-btn")) {
        e.target.closest("tr").remove();
    }
});

//searching js
function initStudentSearch() {
    const searchInput = document.getElementById("searchInput");
    const resultsBox = document.getElementById("searchResults");
    const clearBtn = document.getElementById("clearSearch");

    console.log("Search elements found:", {
        searchInput: !!searchInput,
        resultsBox: !!resultsBox,
        clearBtn: !!clearBtn
    });

    if (!searchInput || !resultsBox || !clearBtn) {
        console.warn("Search elements not found in DOM");
        return;
    }
    
    if (searchInput.dataset.initialized === "true") {
        console.log("Search already initialized");
        return;
    }
    
    console.log("Initializing search...");
    searchInput.dataset.initialized = "true";

    let timeout = null;

    searchInput.addEventListener("input", () => {
        clearBtn.style.display = searchInput.value ? "block" : "none";
    });

    clearBtn.addEventListener("click", () => {
        searchInput.value = "";
        resultsBox.style.display = "none";
        resultsBox.innerHTML = "";
        clearBtn.style.display = "none";
        searchInput.focus();
    });

    searchInput.addEventListener("keyup", (e) => {
        clearTimeout(timeout);

        timeout = setTimeout(() => {
            const query = searchInput.value.trim();
            console.log("Searching for:", query);

            if (query.length < 2) {
                resultsBox.style.display = "none";
                return;
            }
            
            const searchUrl = "search.php?q=" + encodeURIComponent(query);
            console.log("Fetching URL:", searchUrl);
            
            fetch(searchUrl)
                .then(res => {
                    console.log("Response status:", res.status);
                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                    return res.json();
                })
                .then(data => {
                    console.log("Received data:", data);
                    resultsBox.innerHTML = "";

                    if (!Array.isArray(data)) {
                        console.error("Data is not an array:", data);
                        resultsBox.innerHTML = "<div>Error: Invalid response format</div>";
                        resultsBox.style.display = "block";
                        return;
                    }

                    if (data.length === 0) {
                        resultsBox.innerHTML = "<div class='no-results'>No results found</div>";
                        resultsBox.style.display = "block";
                        return;
                    }

                    data.forEach(item => {
                        const div = document.createElement("div");
                        div.className = "search-result-item";
                        const displayName = item.full_name || item.name || "No name";
                        div.textContent = `[${item.type}] ${displayName}`;
                        div.style.cursor = "pointer";
                        div.style.padding = "10px";
                        div.style.borderBottom = "1px solid #eee";
                        
                        div.onclick = () => {
                            console.log("Clicked item:", item);
                            if (item.type === "student") {
                                window.location.href = `view_student.php?id=${item.id}`;
                            } 
                            else if (item.type === "appointment" || item.type === "status") {
                                window.location.href = `appointment.php?id=${item.id}`;
                            } 
                            else if (item.type === "doctor" || item.type === "nurse") {
                                window.location.href = `prescription.php?id=${item.id}`;
                            } 
                            else if (item.type === "notification") {
                                window.location.href = `notifications.php`;
                            }
                        };
                        
                        div.addEventListener("mouseenter", () => {
                            div.style.backgroundColor = "#f5f5f5";
                        });
                        div.addEventListener("mouseleave", () => {
                            div.style.backgroundColor = "white";
                        });
                        
                        resultsBox.appendChild(div);
                    });

                    resultsBox.style.display = "block";
                })
                .catch(err => {
                    console.error("Fetch error:", err);
                    resultsBox.innerHTML = "<div class='error-message'>Error loading results. Please try again.</div>";
                    resultsBox.style.display = "block";
                });

        }, 400);
    });
}