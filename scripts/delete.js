
document.addEventListener("DOMContentLoaded", () => {
    const deleteForm = document.querySelector("form.actions");
    if (deleteForm) {
        deleteForm.addEventListener("submit", (e) => {
            const confirmDelete = confirm(
                "Are you absolutely sure you want to delete this record? This action cannot be undone."
            );
            if (!confirmDelete) {
                e.preventDefault(); 
            }
        });
    }
});
