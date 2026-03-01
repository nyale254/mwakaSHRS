setTimeout(() => {
    location.reload();
}, 90000000);

document.querySelectorAll(".clickable-row").forEach(row => {
    row.addEventListener("click", () => {
        const studentId = row.getAttribute("data-id");
        window.location.href = "view_student.php?id=" + studentId;
    });
});

/*function toggleNotifications() {
    const dropdown = document.getElementById("notificationDropdown");
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
}*/

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

/*function fetchNotifications() {
    fetch('fetch_notification.php')
    .then(res => res.json())
    .then(data => {
        const container = document.getElementById('notifications');
        container.innerHTML = '';
        data.forEach(note => {
            container.innerHTML += `<p>${note.message} <small>(${note.created_at})</small></p>`;
        });
    });
}*/

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

