document.addEventListener('DOMContentLoaded', function() {
    calculateTotal();
    let maxSubactivities = window.maxSubactivities;
    let checkboxes = document.querySelectorAll('input[name="sub_activity_id[]"]');

    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            let selectedCount = document.querySelectorAll(
                'input[name="sub_activity_id[]"]:checked').length;
            if (selectedCount > maxSubactivities) {
                alert(`คุณสามารถเลือกได้สูงสุด ${maxSubactivities} หลักสูตร`);
                checkbox.checked = false;
            }
        });
    });
});

function fetchActivityPrice() {
    const activityId = document.getElementById("fk_activity_id").value;
    if (activityId) {
        fetch(`/getActivityPrice/${activityId}`)
            .then((response) => response.json())
            .then((data) => {
                document.getElementById("children_price").value =
                    data.children_price;
                document.getElementById("student_price").value =
                    data.student_price;
                document.getElementById("adult_price").value = 
                    data.adult_price;
                document.getElementById("disabled_price").value = 
                    data.disabled_price;
                document.getElementById("elderly_price").value = 
                    data.elderly_price;
                document.getElementById("monk_price").value = 
                    data.monk_price;
                calculateTotal();
            })
            .catch((error) =>
                console.error("Error fetching activity price:", error)
            );
    }
}

function calculateTotal() {
    const childrenQty = document.getElementById("childrenInput").value || 0;
    const studentsQty = document.getElementById("studentInput").value || 0;
    const adultsQty = document.getElementById("adultsInput").value || 0;
    const disabledQty = document.getElementById("disabledInput").value || 0;
    const elderlyQty = document.getElementById("elderlyInput").value || 0;
    const monkQty = document.getElementById("monkInput").value || 0;

    const childrenPrice =
        parseFloat(document.getElementById("children_price").value) || 0;
    const studentPrice =
        parseFloat(document.getElementById("student_price").value) || 0;
    const adultPrice =
        parseFloat(document.getElementById("adult_price").value) || 0;
    const disabledPrice =
        parseFloat(document.getElementById("disabled_price").value) || 0;
    const elderlyPrice =
        parseFloat(document.getElementById("elderly_price").value) || 0;
    const monkPrice =
        parseFloat(document.getElementById("monk_price").value) || 0;

    const totalVisitors =
        parseInt(childrenQty) + 
        parseInt(studentsQty) + 
        parseInt(adultsQty) + 
        parseInt(disabledQty) + 
        parseInt(elderlyQty) + 
        parseInt(monkQty);
    const totalPrice =
        childrenQty * childrenPrice +
        studentsQty * studentPrice +
        adultsQty * adultPrice +
        disabledQty * disabledPrice +
        elderlyQty * elderlyPrice +
        monkQty * monkPrice;

    document.getElementById("totalVisitors").innerText = totalVisitors;
    document.getElementById("totalPrice").innerText = totalPrice.toFixed(2);
}

function toggleInput(inputId) {
    const inputField = document.getElementById(inputId);
    inputField.disabled = !inputField.disabled;
    if (inputField.disabled) {
        inputField.value = "";
    }
    calculateTotal();
}

$.Thailand({
    $district: $('#subdistrict'),
    $amphoe: $('#district'),
    $province: $('#province'),
    $zipcode: $('#zipcode'),
    onLoad: function () {
        $('.tt-menu').addClass('dropdown-scrollable');
    }
});

flatpickr("#booking_date", {
    dateFormat: "d/m/Y",
    minDate: new Date().fp_incr(3),
    disable: [
        function(date) {
            return date.getDay() === 1;
        }
    ],
    onDayCreate: function(dObj, dStr, fp, dayElem) {
        if (dayElem.dateObj.getDay() === 1) {
            dayElem.classList.add("disabled-day");
        }
    },
    onChange: function(selectedDates, dateStr, instance) {
        let [day, month, year] = dateStr.split('/');
        let formattedDate = `${year}-${month}-${day}`;

        let activityId = document.getElementById('fk_activity_id').value;

        if (formattedDate) {
            fetch(`/available-timeslots/${activityId}/${formattedDate}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(timeslots => {
                    let timeslotsSelect = document.getElementById('fk_timeslots_id');
                    timeslotsSelect.innerHTML = '';

                    if (timeslots.length === 0) {
                        let option = document.createElement('option');
                        option.value = "";
                        option.text = "ไม่เปิดให้จองในวันนี้";
                        timeslotsSelect.appendChild(option);
                        timeslotsSelect.disabled = true;
                    } else {
                        let option = document.createElement('option');
                        option.value = "";
                        option.text = "เลือกรอบการเข้าชม";
                        timeslotsSelect.appendChild(option);

                        timeslots.forEach((timeslot, index) => {
                            let option = document.createElement('option');
                            let startTime = new Date(`1970-01-01T${timeslot.start_time}Z`);
                            let endTime = new Date(`1970-01-01T${timeslot.end_time}Z`);
                            let startFormatted = `${startTime.getUTCHours().toString().padStart(2, '0')}:${startTime.getUTCMinutes().toString().padStart(2, '0')}`;
                            let endFormatted = `${endTime.getUTCHours().toString().padStart(2, '0')}:${endTime.getUTCMinutes().toString().padStart(2, '0')}`;

                            option.value = timeslot.timeslots_id;
                            option.text = `รอบที่ ${index + 1} ${startFormatted} น. - ${endFormatted} น.`;
                            
                        if (timeslot.remaining_capacity === 0) {
                            option.disabled = true;
                            option.text += " (เต็ม)";
                        } else {
                            option.text += ` (เหลือ ${timeslot.remaining_capacity} ที่นั่ง)`;
                        }
                            timeslotsSelect.appendChild(option);
                        });

                        timeslotsSelect.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
        }
    },
    onReady: function() {
        document.querySelector('.input-group-text').addEventListener('click', () => {
            document.querySelector("#booking_date")._flatpickr.open();
        });
    }
});

function confirmSubmission() {
    const childrenQty = document.getElementById("childrenInput").value || 0;
    const studentsQty = document.getElementById("studentInput").value || 0;
    const adultsQty = document.getElementById("adultsInput").value || 0;
    const disabledQty = document.getElementById("disabledInput").value || 0;
    const elderlyQty = document.getElementById("elderlyInput").value || 0;
    const monkQty = document.getElementById("monkInput").value || 0;

    if (
        childrenQty == 0 &&
        studentsQty == 0 &&
        adultsQty == 0 &&
        disabledQty == 0 &&
        elderlyQty == 0 &&
        monkQty == 0
    ) {
        document.getElementById("errorMessage").innerText = "*กรุณาระบุจำนวนผู้เข้าชมอย่างน้อย 1 ประเภท";
        document.getElementById("errorMessage").style.display = "block";
        return;
    } else {
        document.getElementById("errorMessage").style.display = "none";
    }
    document.querySelectorAll('input[disabled]').forEach(input => {
        input.disabled = false;
    });
    const isConfirmed = confirm("คุณต้องการยืนยันการส่งข้อมูลใช่หรือไม่?");
    if (isConfirmed) {
        document.querySelector('form').submit();
    }
}
