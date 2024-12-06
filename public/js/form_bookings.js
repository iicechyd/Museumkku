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
                document.getElementById("adult_price").value = data.adult_price;
                calculateTotal(); // Recalculate total when price is updated
            })
            .catch((error) =>
                console.error("Error fetching activity price:", error)
            );
    }
}

function toggleInput(inputId) {
    const inputField = document.getElementById(inputId);
    inputField.disabled = !inputField.disabled;
    if (inputField.disabled) {
        inputField.value = "";
    }
    calculateTotal();
}
function calculateTotal() {
    const childrenQty = document.getElementById("childrenInput").value || 0;
    const studentsQty = document.getElementById("studentInput").value || 0;
    const adultsQty = document.getElementById("adultsInput").value || 0;

    const childrenPrice =
        parseFloat(document.getElementById("children_price").value) || 0;
    const studentPrice =
        parseFloat(document.getElementById("student_price").value) || 0;
    const adultPrice =
        parseFloat(document.getElementById("adult_price").value) || 0;

    const totalVisitors =
        parseInt(childrenQty) + parseInt(studentsQty) + parseInt(adultsQty);
    const totalPrice =
        childrenQty * childrenPrice +
        studentsQty * studentPrice +
        adultsQty * adultPrice;

    document.getElementById("totalVisitors").innerText = totalVisitors;
    document.getElementById("totalPrice").innerText = totalPrice.toFixed(2);
}

function fetchTimeslots() {
    const activityId = document.getElementById("fk_activity_id").value;

    // สมมติว่าคุณมี route ที่จัดการการดึง timeslots โดยใช้ activity_id
    fetch(`/timeslots/${activityId}`)
        .then((response) => response.json())
        .then((data) => {
            const timeslotSelect = document.getElementById("fk_timeslots_id");
            timeslotSelect.innerHTML =
                '<option value="">เลือกรอบการเข้าชม</option>';
            data.forEach((timeslot) => {
                const option = document.createElement("option");
                option.value = timeslot.id;
                option.text = `${timeslot.start_time} - ${timeslot.end_time}`;
                timeslotSelect.appendChild(option);
            });
        });
}
$.Thailand({
    $district: $('#district'),
    $amphoe: $('#subdistrict'),
    $province: $('#province'),
    $zipcode: $('#zipcode'),
    onLoad: function () {
        $('.tt-menu').addClass('dropdown-scrollable'); // เพิ่ม class
    }
});

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("booking_date").value = "2024-11-30";
    document.getElementById("instituteName").value = "โรงเรียนเด่นดี";
    document.getElementById("instituteAddress").value =
        "123/45 หมู่ 2 บ้านหมีน้อย";
    document.getElementById("visitorName").value = "นางสาวแก้วกานต์ แก่นดี";
    document.getElementById("visitorEmail").value = "kaewkarn@gmail.com";
    document.getElementById("tel").value = "0812345678";
    document.getElementById("children_qty").checked = true;
    document.getElementById("childrenInput").disabled = false;
    document.getElementById("childrenInput").value = 10;
    calculateTotal();
});
