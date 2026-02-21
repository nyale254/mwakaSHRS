setTimeout(() => {
    location.reload();
}, 900000);

function toggleNotifications() {
    const dropdown = document.getElementById("notifDropdown");
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
}

document.addEventListener("click", function (e) {
    const btn = document.querySelector(".notification-btn");
    const dropdown = document.getElementById("notifDropdown");

    if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.style.display = "none";
    }
});
