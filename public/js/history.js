flatpickr("#date_range", {
        mode: "range",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d/m/Y",
        allowInput: true,
        onReady: function(selectedDates, dateStr, instance) {
            instance.altInput.style.width = "230px";
            instance.altInput.style.padding = "6px";
            instance.altInput.style.border = "1px solid #ccc";
            instance.altInput.style.borderRadius = "4px";
            instance.altInput.style.textAlign = "center";
        }
 });

 document.addEventListener('DOMContentLoaded', function () {
    let dateRangeInput = document.getElementById('date_range');
    let dateRangeFields = document.getElementById('dateRangeFields');
    let toggleButton = document.getElementById('toggleDateRange');

    if (localStorage.getItem('dateRangeVisible') === 'true') {
        dateRangeFields.style.display = "flex";
    }

    toggleButton.addEventListener('click', function () {
        if (dateRangeFields.style.display === "none" || dateRangeFields.style.display === "") {
            dateRangeFields.style.display = "flex";
            localStorage.setItem('dateRangeVisible', 'true');
        } else {
            dateRangeFields.style.display = "none";
            localStorage.setItem('dateRangeVisible', 'false');
        }
    });

    document.querySelectorAll('.filter-btn').forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();

            let form = document.getElementById('filterForm');
            let url = new URL(form.action, window.location.origin);

            let activityName = document.getElementById("activity_name").value;
            let status = document.getElementById("status").value;

            url.searchParams.delete('date_range');
            url.searchParams.set(this.getAttribute('data-filter'), 'true');

            if (activityName) url.searchParams.set('activity_name', activityName);
            if (status) url.searchParams.set('status', status);

            window.location.href = url.toString();
        });
    });
});

