setTimeout(() => {
    location.reload();
}, 900000);

function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("collapsed");
}

document.querySelectorAll(".clickable-row").forEach(row => {
    row.addEventListener("click", () => {
        const studentId = row.getAttribute("data-id");
        window.location.href = "view_student.php?id=" + studentId;
    });
});

function toggleNotifications() {
    const dropdown = document.getElementById("notificationDropdown");
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
}

document.addEventListener("click", function (e) {
    const btn = document.querySelector(".notification-btn");
    const dropdown = document.getElementById("notificationDropdown");

    if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.style.display = "none";
    }
});
