setTimeout(() => {
    location.reload();
}, 90000000);

document.querySelectorAll(".clickable-row").forEach(row => {
    row.addEventListener("click", () => {
        const studentId = row.getAttribute("data-id");
        window.location.href = "view_student.php?id=" + studentId;
    });
});


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

                if(page.includes('treatment')){
                    initTreatmentPage();
                }
            })
            .catch(err => {
                content.innerHTML = "<p>Error loading page.</p>";
                console.error(err);
            });
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
            table.innerHTML += `
            <tr id="appointment-${a.appointment_id}">
                <td>${a.student_name}</td>
                <td>${a.appointment_date}</td>
                <td>${a.reason}</td>
                <td><span class="status ${a.status}">${a.status.charAt(0).toUpperCase() + a.status.slice(1)}</span></td>
                <td>
                    ${a.status === 'pending' ? `
                        <button class="btn confirm" onclick="updateStatus(${a.appointment_id}, 'confirmed', ${a.student_id})">Confirm</button>
                        <button class="btn reject" onclick="updateStatus(${a.appointment_id}, 'rejected', ${a.student_id})">Reject</button>
                    ` : '-'}
                </td>
            </tr>`;
        });
    });
}

function updateStatus(appointmentId, status, studentId) {
    if(!confirm(`Are you sure you want to ${status} this appointment?`)) return;

    fetch('update_appointment.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({appointment_id:appointmentId, status:status, student_id:studentId})
    })
    .then(res=>res.json())
    .then(data=>{
        if(data.success) fetchAppointments();
        else alert('Error: '+data.message);
    });
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

setInterval(fetchAppointments, 5000);
fetchAppointments();

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
    <td><input name="medication[]" class="form-control"></td>
    <td><input name="prescribed_dosage[]" class="form-control"></td>
    <td><input name="frequency[]" class="form-control"></td>
    <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">Remove</button></td>
    `;

    table.appendChild(row);
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

            Swal.fire({
                icon:'error',
                title:'Error',
                text:data
            });

        }

    });

});
}

}