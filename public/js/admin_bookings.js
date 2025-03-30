document.addEventListener("DOMContentLoaded", function () {
    let maxSubactivities = window.maxSubactivities;
    let checkboxes = document.querySelectorAll(
        'input[name="sub_activity_id[]"]'
    );

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener("change", function () {
            let selectedCount = document.querySelectorAll(
                'input[name="sub_activity_id[]"]:checked'
            ).length;
            if (selectedCount > maxSubactivities) {
                alert(`คุณสามารถเลือกได้สูงสุด ${maxSubactivities} หลักสูตร`);
                checkbox.checked = false;
            }
        });
    });
    calculateTotal();
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
                document.getElementById("kid_price").value =
                    data.kid_price;
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
    const kidQty = document.getElementById("kidInput").value || 0;
    const disabledQty = document.getElementById("disabledInput").value || 0;
    const elderlyQty = document.getElementById("elderlyInput").value || 0;
    const monkQty = document.getElementById("monkInput").value || 0;

    const childrenPrice =
        parseFloat(document.getElementById("children_price").value) || 0;
    const studentPrice =
        parseFloat(document.getElementById("student_price").value) || 0;
    const adultPrice =
        parseFloat(document.getElementById("adult_price").value) || 0;
    const kidPrice =
        parseFloat(document.getElementById("kid_price").value) || 0;
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
        parseInt(kidQty) +
        parseInt(disabledQty) +
        parseInt(elderlyQty) +
        parseInt(monkQty);
    const totalPrice =
        childrenQty * childrenPrice +
        studentsQty * studentPrice +
        adultsQty * adultPrice +
        kidQty * kidPrice +
        disabledQty * disabledPrice +
        elderlyQty * elderlyPrice +
        monkQty * monkPrice;

    document.getElementById("totalVisitors").innerText = totalVisitors.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById("totalPrice").innerText = totalPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

$.Thailand({
    $district: $("#subdistrict"),
    $amphoe: $("#district"),
    $province: $("#province"),
    $zipcode: $("#zipcode"),
    onLoad: function () {
        $(".tt-menu").addClass("dropdown-scrollable");
    },
});

document.addEventListener("DOMContentLoaded", function () {
    let bookingDateInput = document.getElementById("booking_date");

    function formatDateToDMY(date) {
        let day = String(date.getDate()).padStart(2, "0");
        let month = String(date.getMonth() + 1).padStart(2, "0");
        let year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }

    function formatDateToYMD(date) {
        let day = String(date.getDate()).padStart(2, "0");
        let month = String(date.getMonth() + 1).padStart(2, "0");
        let year = date.getFullYear();
        return `${year}-${month}-${day}`;
    }

    let today = new Date();
    let formattedDateDMY = formatDateToDMY(today);
    let formattedDateYMD = formatDateToYMD(today);
    bookingDateInput.value = formattedDateDMY;

    let activityId = document.getElementById("fk_activity_id").value;
    fetchTmss(activityId, formattedDateYMD);

    flatpickr("#booking_date", {
        dateFormat: "d/m/Y",
        defaultDate: today,
        minDate: "today",
        maxDate: "today",
        disableMobile: true,
        disable: [
            function (date) {
                return date.getDay() === 1;
            },
        ],
        onDayCreate: function (dObj, dStr, fp, dayElem) {
            if (dayElem.dateObj.getDay() === 1) {
                dayElem.classList.add("disabled-day");
            }
        },
        onChange: function (selectedDates, dateStr, instance) {
            let [day, month, year] = dateStr.split("/");
            let formattedDate = `${year}-${month}-${day}`;
            let activityId = document.getElementById("fk_activity_id").value;

            fetchTmss(activityId, formattedDate);
        },
        onReady: function () {
            document.querySelector(".input-group-text").addEventListener("click", () => {
                document.querySelector("#booking_date")._flatpickr.open();
            });
        },
    });
});

function confirmSubmission() {
    const privacyCheckbox = document.getElementById("privacyPolicy");
    const privacyAlert = document.getElementById("privacyAlert");

    if (!privacyCheckbox.checked) {
        privacyAlert.innerText = "โปรดยอมรับนโยบายความเป็นส่วนตัวก่อนยืนยันข้อมูล";
        privacyAlert.style.display = "block";
        return;
    } else {
        privacyAlert.style.display = "none";
    }
    
    const childrenQty = document.getElementById("childrenInput").value || 0;
    const studentsQty = document.getElementById("studentInput").value || 0;
    const adultsQty = document.getElementById("adultsInput").value || 0;
    const kidQty = document.getElementById("kidInput").value || 0;
    const disabledQty = document.getElementById("disabledInput").value || 0;
    const elderlyQty = document.getElementById("elderlyInput").value || 0;
    const monkQty = document.getElementById("monkInput").value || 0;
    const totalVisitors = childrenQty + studentsQty + adultsQty + kidQty + disabledQty + elderlyQty + monkQty;

    if (totalVisitors === 0) {
        document.getElementById("errorMessage").innerText =
            "*กรุณาระบุจำนวนผู้เข้าชมอย่างน้อย 1 ประเภท";
        document.getElementById("errorMessage").style.display = "block";
        return;
    } else {
        document.getElementById("errorMessage").style.display = "none";
    }

    if (totalVisitors <= 0) {
        alert("จำนวนผู้เข้าชมต้องมากกว่า 0 คน");
        return;
    }

    if (window.subactivities.length > 0) {
        const selectedSubactivities = document.querySelectorAll(
            'input[name="sub_activity_id[]"]:checked'
        );
        const maxSubactivities = window.maxSubactivities;

        if (selectedSubactivities.length === 0) {
            alert("กรุณาเลือกหลักสูตร");
            return;
        }

        if (selectedSubactivities.length < maxSubactivities) {
            alert(`กรุณาเลือกให้ครบ ${maxSubactivities} หลักสูตร`);
            return;
        }
    }

    const formInput = {
        zipcode: document.getElementById("zipcode").value.trim(),
        subdistrict: document
            .getElementById("subdistrict")
            .value.trim()
            .toLowerCase(),
        district: document
            .getElementById("district")
            .value.trim()
            .toLowerCase(),
        province: document
            .getElementById("province")
            .value.trim()
            .toLowerCase(),
    };

    $.getJSON("/raw_database.json", function (data) {
        const filteredByZipcode = data.filter(
            (item) => String(item.zipcode) === formInput.zipcode
        );

        console.log("ค่าจากฟอร์ม:", formInput);
        console.log("ข้อมูลที่กรองด้วยรหัสไปรษณีย์:", filteredByZipcode);

        const isValid = filteredByZipcode.some((item) => {
            console.log("ตรวจสอบรายการ:", item);
            return (
                item.district.trim().toLowerCase() ===
                    formInput.subdistrict.toLowerCase() &&
                item.amphoe.trim().toLowerCase() ===
                    formInput.district.toLowerCase() &&
                item.province.trim().toLowerCase() ===
                    formInput.province.toLowerCase()
            );
        });

        if (!isValid) {
            alert(
                "ข้อมูลที่อยู่ของคุณไม่ถูกต้อง กรุณาตรวจสอบให้แน่ใจว่ารหัสไปรษณีย์, ตำบล, อำเภอ และจังหวัดให้ถูกต้อง"
            );
            return;
        }

        const isConfirmed = confirm("คุณต้องการยืนยันการส่งข้อมูลใช่หรือไม่?");
        if (isConfirmed) {
            document.querySelector("form").submit();
        }
    }).fail(function () {
        alert("ไม่สามารถโหลดข้อมูลจากระบบได้ กรุณาลองใหม่");
    });
}