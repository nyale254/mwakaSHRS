
const searchInput = document.getElementById("liveSearch");
const tableBody = document.getElementById("studentTable");

searchInput.addEventListener("keyup", function () {
    const query = this.value;

    const xhr = new XMLHttpRequest();
    xhr.open("GET", "live_search_students.php?query=" + query, true);
    xhr.onload = function () {
        if (this.status === 200) {
            tableBody.innerHTML = this.responseText;

            document.querySelectorAll(".clickable-row").forEach(row => {
                row.addEventListener("click", () => {
                    const studentId = row.getAttribute("data-id");
                    window.location.href = "view_student.php?id=" + studentId;
                });
            });
        }
    };
    xhr.send();
});

document.querySelectorAll(".clickable-row").forEach(row => {
        row.addEventListener("click", () => {
            const studentId = row.getAttribute("data-id");
            window.location.href = "view_student.php?id=" + studentId;
        });
});
