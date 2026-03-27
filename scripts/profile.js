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
}
