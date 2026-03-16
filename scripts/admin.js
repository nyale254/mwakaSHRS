const links = document.querySelectorAll('.nav-link');
const content = document.getElementById('main-content');

links.forEach(link => {

    link.addEventListener('click', function(e){

        e.preventDefault();

        links.forEach(l => l.classList.remove('active'));
        this.classList.add('active');

        const page = this.getAttribute('data-page');

        fetch(page)
        .then(response => response.text())
        .then(html => {

            content.innerHTML = html;

            if(page.includes('dashboard')){
                initDashboardChart();
            }
            if(page.includes('report')){ 
                initReportChart();
            } 

        })
        .catch(err => {
            content.innerHTML = "<p>Error loading page.</p>";
            console.error(err);
        });
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

const searchBox = document.getElementById("searchBox");

if(searchBox){

    searchBox.addEventListener("keyup", function(){

        let value = this.value.toLowerCase();
        console.log("Searching:", value);

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

});