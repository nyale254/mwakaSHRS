const links = document.querySelectorAll('.nav-link');
const content = document.getElementById('main-content');

function loadPage(url, addToHistory = true) {

    fetch(url)
        .then(res => res.text())
        .then(html => {
            content.innerHTML = html;

            initDynamicContent(url);

            if (addToHistory) {
                history.pushState({ page: url }, "", url);
            }
        })
        .catch(err => {
            content.innerHTML = "<p>Error loading page.</p>";
            console.error(err);
        });
}

links.forEach(link => {
    link.addEventListener('click', function(e){
        e.preventDefault();

        links.forEach(l => l.classList.remove('active'));
        this.classList.add('active');

        const page = this.getAttribute('data-page');
        loadPage(page);
    });
});
function initDashboardChart() {
    const canvas = document.getElementById("trendChart");
    if (!canvas) return;
    const labels = JSON.parse(canvas.dataset.labels);
    const totals = JSON.parse(canvas.dataset.totals);
    const ctx = canvas.getContext("2d");
    new Chart(ctx, {
        type: "line",
        data: {
            labels: labels,
            datasets: [{
                label: "Health Records",
                data: totals,
                borderColor: "#2563eb",
                backgroundColor: "rgba(37,99,235,0.1)",
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            }
        }
    });
}

initDashboardChart();


function initReportChart() {
    const canvas = document.getElementById("reportChart");
    if (!canvas) return;

    const labels = JSON.parse(canvas.dataset.labels);
    const totals = JSON.parse(canvas.dataset.totals);
    const period = canvas.dataset.period;

    const ctx = canvas.getContext("2d");

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Visits',
                data: totals,
                backgroundColor: '#3498db'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Number of Visits' } },
                x: { title: { display: true, text: period } }
            }
        }
    });
}
document.addEventListener("click", function(e){

    if(e.target.classList.contains("delete-btn")){

        e.preventDefault();

        const userId = e.target.dataset.id;
        const username = e.target.dataset.name;
        const row = e.target.closest("tr");

        Swal.fire({
            title: "Are you sure?",
            text: `Delete user ${username}?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete"
        }).then((result)=>{

            if(result.isConfirmed){

                fetch(`/Mwaka.SHRS.2/admin/delete.php?id=${userId}`)
                .then(res => res.json())
                .then(data => {

                    if(data.success){

                        row.remove();

                        Swal.fire({
                            icon:"success",
                            title:"Deleted!",
                            text:data.message,
                            timer:2000,
                            showConfirmButton:false
                        });

                    }else{

                        Swal.fire({
                            icon:"error",
                            title:"Error",
                            text:data.message
                        });

                    }

                })
                .catch(err=>{
                    Swal.fire({
                        icon:"error",
                        title:"Error",
                        text:"Delete failed"
                    });
                });

            }
        });

    }
   
    const viewBtn = e.target.closest(".view-btn");
    if (viewBtn) {
        e.preventDefault();
        const url = viewBtn.getAttribute("href");
        loadPage(url);
        initDynamicContent("view_student");
    }

    const editBtn = e.target.closest(".edit-btn");
    if (editBtn) {
        e.preventDefault();
        const url = editBtn.getAttribute("href");
        loadPage(url);
        initDynamicContent("edit_student");
    }

    const addBtn = e.target.closest(".btn-add");
    if(addBtn){
        e.preventDefault();
        const url = addBtn.getAttribute("href");
        loadPage(url);
        initDynamicContent("add_student");}

});

// admin-messages.js
let adminEventsInitialized = false;
function initAdminMessages() {
    const searchForm = document.getElementById("searchForm");
    if (searchForm && !searchForm.dataset.bound) {
        searchForm.dataset.bound = "true";
        searchForm.addEventListener("submit", function (e) {
            e.preventDefault();
            const searchValue = document.getElementById("searchInput").value;

            fetch(`contact_message.php?search=${encodeURIComponent(searchValue)}`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById("main-content").innerHTML = html;
                    initAdminMessages(); 
                });
        });
    }
    const replyForm = document.getElementById("replyForm");
    if(replyForm && !replyForm.dataset.bound){
        replyForm.dataset.bound = "true";
        replyForm.addEventListener("submit", function(e){
            e.preventDefault();
            const formData = new FormData(this);
            fetch('send-mail.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                const statusDiv = document.getElementById('emailStatus');
                if(!statusDiv){
                    const div = document.createElement('div');
                    div.id = 'emailStatus';
                    div.style.marginTop = '10px';
                    div.style.fontWeight = 'bold';
                    replyForm.appendChild(div);
                }

                if(data.success){
                    const emailStatus = data.email_sent 
                        ? "✅ Email sent successfully to the user."
                        : `❌ Email failed: ${data.email_error}`;
                    document.getElementById('emailStatus').textContent = emailStatus;
                    setTimeout(()=>{
                        document.getElementById('replyModal').style.display = 'none';
                        fetch('contact_message.php')
                            .then(res => res.text())
                            .then(html => {
                                document.getElementById("main-content").innerHTML = html;
                                initAdminMessages();
                            });
                    }, 2000);
                } else {
                    document.getElementById('emailStatus').textContent = "❌ " + (data.error || "Reply failed");
                }
            })
            .catch(err => console.error(err));
        });
    }

    if (!adminEventsInitialized) {
        adminEventsInitialized = true;

        document.addEventListener('click', function (e) {
            const viewBtn = e.target.closest('.btn-view');
            if (viewBtn) {
                viewMessage(viewBtn.dataset.id);
            }

            const replyBtn = e.target.closest('.btn-reply');
            if (replyBtn) {
                replyMessage(replyBtn.dataset.id);
            }

           const markBtn = e.target.closest('.btn-mark');
            if (markBtn) {
                e.preventDefault();
                const messageId = markBtn.dataset.id;

                fetch(`mark-read-message.php?id=${messageId}`, {
                    method: 'GET'
                })
                .then(res => res.json()) 
                .then(data => {
                    if (data.success) { 
                        const row = markBtn.closest('tr');
                        const statusSpan = row.querySelector('.status-badge');
                        statusSpan.textContent = 'Read';
                        statusSpan.className = 'status-badge status-Read';

                        markBtn.remove();
                    } else {
                        alert(data.error || "Failed to mark as read");
                    }
                })
                .catch(err => console.error(err));
            }

            if (e.target.classList.contains('modal')) {
                e.target.style.display = 'none';
            }
        });
    }
}

function filterMessages(status) {
    fetch(`contact_message.php?status=${status}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById("main-content").innerHTML = html;
            initAdminMessages();
        });
}

function viewMessage(messageId) {
    fetch(`get_message.php?id=${messageId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const modalBody = document.getElementById('viewModalBody');
                modalBody.innerHTML = `
                    <div class="message-detail">
                        <p><strong>From:</strong> ${escapeHtml(data.message.fullname)}</p>
                        <p><strong>Email:</strong> ${escapeHtml(data.message.email)}</p>
                        <p><strong>Subject:</strong> ${escapeHtml(data.message.subject)}</p>
                        <p><strong>Sent Date:</strong> ${new Date(data.message.created_at).toLocaleString()}</p>
                        <p><strong>Status:</strong> 
                            <span class="status-badge status-${data.message.status}">
                                ${data.message.status}
                            </span>
                        </p>
                        <hr>
                        <p><strong>Message:</strong></p>
                        <p style="white-space: pre-wrap;">${escapeHtml(data.message.message)}</p>
                    </div>
                `;

                document.getElementById('viewModal').style.display = 'block';
            }
        })
        .catch(err => console.error(err));
}

function replyMessage(messageId) {
    fetch(`get_message.php?id=${messageId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {

                document.getElementById('replyMessageId').value = messageId;
                document.getElementById('replyMessageInfo').innerHTML = `
                    <div class="message-detail">
                        <p><strong>From:</strong> ${escapeHtml(data.message.fullname)}</p>
                        <p><strong>Email:</strong> ${escapeHtml(data.message.email)}</p>
                        <p><strong>Subject:</strong> ${escapeHtml(data.message.subject)}</p>
                        <p><strong>Message:</strong></p>
                        <p style="white-space: pre-wrap;">${escapeHtml(data.message.message)}</p>
                    </div>
                `;

                loadReplyHistory(messageId);

                document.getElementById('replyModal').style.display = 'block';
            }
        })
        .catch(err => console.error(err));
}
function loadReplyHistory(messageId) {
    fetch(`get_replies.php?id=${messageId}`)
        .then(res => res.json())
        .then(replies => {
            const container = document.getElementById('replyHistory');
            if (replies.length > 0) {
                let html = '<div class="reply-history"><h4>Reply History</h4>';

                replies.forEach(reply => {
                    html += `
                        <div class="reply-item">
                            <div class="reply-meta">
                                <strong>Admin:</strong> ${escapeHtml(reply.admin_name)} |
                                <strong>${new Date(reply.created_at).toLocaleString()}</strong>
                            </div>
                            <p>${escapeHtml(reply.reply_text)}</p>
                        </div>
                    `;
                });

                html += '</div>';
                container.innerHTML = html;

            } else {
                container.innerHTML = '';
            }
        })
        .catch(err => console.error(err));
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.style.display = 'none';
}

function escapeHtml(text) {
    if (!text) return '';
    return text.replace(/[&<>"']/g, function (m) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[m];
    });
}
window.AdminMessages = {
    init: initAdminMessages
};

const toggleBtn = document.getElementById("menuToggle");
const sidebar = document.querySelector(".sidebar");

toggleBtn.addEventListener("click", () => {
    sidebar.classList.toggle("active");
});

const SearchModule = (() => {
    let input, container, items;

    function init(config = {}) {
        const {
            inputSelector = "#searchBox",
            containerSelector = "#main-content",
            itemSelector = ".search-item"
        } = config;

        input = document.querySelector(inputSelector);
        container = document.querySelector(containerSelector);

        if (!input || !container) return;

        input.addEventListener("input", handleSearch);

        items = container.querySelectorAll(itemSelector);
    }

    function handleSearch() {
        const query = input.value.toLowerCase();
        items = container.querySelectorAll(".search-item"); 

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(query)) {
                item.style.display = "";
            } else {
                item.style.display = "none";
            }
        });
    }
    function refresh() {
        items = container.querySelectorAll(".search-item");
    }

    return { init, refresh };
})();

document.addEventListener("DOMContentLoaded", () => {
    SearchModule.init();
});

/*student management page js*/

function initStudentSearch() {
    const form = document.getElementById("studentSearchForm");
    if (!form || form.dataset.bound) return;
    form.dataset.bound = "true";

    form.addEventListener("submit", function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();
        loadPage(`/Mwaka.SHRS.2/admin/student_management.php?${params}`);
        initDynamicContent("students");
        SearchModule.refresh();
    });
}

function initDynamicContent(page) {

    SearchModule.init({
        inputSelector: "#searchBox",
        containerSelector: "#main-content",
        itemSelector: ".search-item"
    });

    if (page.includes('dashboard')) initDashboardChart();
    if (page.includes('report')) initReportChart();

    if (window.AdminMessages) {
        AdminMessages.init();
    }
    initStudentSearch();
    PasswordModule.init(document.getElementById("main-content"));
}

function initEditForm() {
    const form = document.getElementById("editStudentForm");
    if (!form || form.dataset.bound) return;
    form.dataset.bound = "true";

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(form);

        fetch(form.action, {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {

                Swal.fire("Success", "Student updated", "success");

                fetch("students.php")
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById("main-content").innerHTML = html;
                        initDynamicContent("students");
                    });

            } else {
                Swal.fire("Error", data.message, "error");
            }
        });
    });
}
document.addEventListener("click", function (e) {
    const backBtn = e.target.closest("#backBtn");
    if (!backBtn) return;

    e.preventDefault();

    if (history.length > 1) {
        history.back();
    } else {
        loadPage("dashboard.php");
    }
});

window.addEventListener("DOMContentLoaded", () => {
    history.replaceState({ page: "dashboard.php" }, "", "dashboard.php");
    PasswordModule.init();
});

window.addEventListener("popstate", function(e) {
    if (e.state && e.state.page) {
        loadPage(e.state.page, false);
    }
});

// student-password.js
const PasswordModule = (() => {

    function init(container = document) {
        const password = container.querySelector("#password");
        const toggle = container.querySelector("#togglePassword");
        const strength = container.querySelector("#strength");

        if (!password) return; 
        password.addEventListener("input", () => {
            checkStrength(password, strength);
        });

        if (toggle) {
            toggle.addEventListener("click", () => {
                const type = password.getAttribute("type") === "password" ? "text" : "password";
                password.setAttribute("type", type);
                toggle.textContent = type === "password" ? "👁️" : "🙈";
            });
        }

        const form = container.querySelector("form");
        if (form) {
            form.addEventListener("submit", (e) => {
                const confirmed = confirm("Are you sure you want to add this user?");
                if (!confirmed) e.preventDefault();
            });
        }
    }

    function checkStrength(passwordEl, strengthEl) {
        if (!strengthEl) return;

        const password = passwordEl.value;
        let score = 0;

        if (password.length >= 8) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[a-z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;

        if (password.length === 0) {
            strengthEl.textContent = "";
            strengthEl.className = "";
            return;
        }

        if (score <= 2) {
            strengthEl.textContent = "Weak password";
            strengthEl.className = "weak";
        } else if (score === 3 || score === 4) {
            strengthEl.textContent = "Medium strength password";
            strengthEl.className = "medium";
        } else {
            strengthEl.textContent = "Strong password";
            strengthEl.className = "strong";
        }
    }

    return { init };

})();


