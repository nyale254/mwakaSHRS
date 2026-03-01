
function loadPage(page) {
    fetch(page)
        .then(response => {
            if (!response.ok) throw new Error("Page not found");
            return response.text();
        })
        .then(data => {
            const main = document.getElementById("mainContent");
            if (!main) return;

            main.innerHTML = data;

            if (page.includes("appointment.php") && typeof initAppointmentPage === "function") {
                initAppointmentPage();
            }
            if (page.includes("some_other_page.php") && typeof initOtherPage === "function") {
                initOtherPage();
            }

            window.scrollTo(0, 0);
        })
        .catch(err => {
            const main = document.getElementById("mainContent");
            if (main) main.innerHTML = "<h3 style='color:red;'>Error loading page.</h3>";
            console.error(err);
        });
}

function loadNotifications() {
    const dropdownList = document.querySelector("#notifDropdown ul");
    const pageBox = document.getElementById("notifications");
    const badge = document.getElementById("notificationBadge");

    if (!dropdownList || !pageBox || !badge) return;

    fetch("/Mwaka.SHRS.2/student/fetch_notification.php")
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
function toggleNotifications() {
    const dropdown = document.getElementById("notifDropdown");
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

// ----------------------------
// Appointments
// ----------------------------
function loadAppointments() {
    const container = document.getElementById("appointmentsList");
    if (!container) return;

    fetch("/Mwaka.SHRS.2/student/fetch_appointment.php")
        .then(res => res.json())
        .then(data => {
            let table = `
                <div class="table-section">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Reason</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            if (!Array.isArray(data) || data.length === 0) {
                table += `<tr><td colspan="4">No appointments found.</td></tr>`;
            } else {
                data.forEach(app => {
                    const status = app.status || "Pending";
                    const formattedDate = new Date(app.appointment_date).toLocaleString();

                    table += `
                        <tr>
                            <td>${app.appointment_id}</td>
                            <td>${formattedDate}</td>
                            <td>${app.reason}</td>
                            <td class="status ${app.status}">${app.status.charAt(0).toUpperCase() + app.status.slice(1)}</td>

                        </tr>
                    `;
                });
            }

            table += `</tbody></table></div>`;
            container.innerHTML = table;
        })
        .catch(err => console.error("Error fetching appointments:", err));
}

function initAppointmentPage() {
    loadAppointments();
    loadNotifications();

    const form = document.getElementById("appointmentForm");
    if (!form) return;

    form.addEventListener("submit", function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch("/Mwaka.SHRS.2/student/submit_appointment.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.message) alert(data.message);
            if (data.success) {
                form.reset();
                loadAppointments();
            }
        })
        .catch(err => console.error("Error submitting appointment:", err));
    });
}

document.addEventListener("DOMContentLoaded", function() {

    const notifBtn = document.querySelector("[data-page='notifications']");
    const dropdown = document.getElementById("notifDropdown");

    if (notifBtn && dropdown) {
        notifBtn.addEventListener("click", function(e) {
            e.stopPropagation();
            toggleNotifications();
        });

        document.addEventListener("click", function(e) {
            if (!dropdown.contains(e.target) && !notifBtn.contains(e.target)) {
                dropdown.classList.remove("show");
            }
        });
    }

    document.querySelectorAll("a[data-page]").forEach(link => {
        link.addEventListener("click", function(e) {
            e.preventDefault();
            const page = this.getAttribute("data-page");
            if (page) loadPage(page);
        });
    });

    loadNotifications();
    loadAppointments();

    setInterval(() => {
        loadNotifications();
        if (document.getElementById("appointmentsList")) loadAppointments();
    }, 5000);
});