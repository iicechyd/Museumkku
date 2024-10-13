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
// function fetchTimeslots() {
//     const activityId = document.getElementById("fk_activity_id").value;
//     const timeslotSelect = document.getElementById("fk_timeslots_id");

//     timeslotSelect.innerHTML = '<option value="">เลือกรอบการเข้าชม</option>';

//     if (activityId) {
//         fetch(`/timeslots?activity_id=${activityId}`)
//             .then((response) => response.json())
//             .then((data) => {
//                 data.forEach((timeslot) => {
//                     const option = document.createElement("option");
//                     option.value = timeslot.timeslots_id;
//                     option.textContent = `${timeslot.start_time} - ${timeslot.end_time}`;
//                     timeslotSelect.appendChild(option);
//                 });
//             })
//             .catch((error) =>
//                 console.error("Error fetching timeslots:", error)
//             );
//     }
// }
function showProvinces() {
    let input_province = document.querySelector("#province");
    let url = "https://ckartisan.com/api/provinces";
    console.log(url);

    fetch(url)
        .then((response) => response.json())
        .then((result) => {
            console.log(result);
            input_province.innerHTML =
                '<option value="">กรุณาเลือกจังหวัด</option>';
            for (let item of result) {
                let option = document.createElement("option");
                option.text = item.province;
                option.value = item.province;
                input_province.appendChild(option);
            }
            // QUERY AMPHOES
            showAmphoes();
        });
}

function showAmphoes() {
    let input_province = document.querySelector("#province");
    let url =
        "https://ckartisan.com/api/amphoes?province=" + input_province.value;
    console.log(url);

    fetch(url)
        .then((response) => response.json())
        .then((result) => {
            console.log(result);
            // UPDATE SELECT OPTION
            let input_district = document.querySelector("#district");
            input_district.innerHTML =
                '<option value="">กรุณาเลือกเขต/อำเภอ</option>';
            for (let item of result) {
                let option = document.createElement("option");
                option.text = item.amphoe;
                option.value = item.amphoe;
                input_district.appendChild(option);
            }
            // QUERY TAMBONS
            showTambons();
        });
}

function showTambons() {
    let input_province = document.querySelector("#province");
    let input_district = document.querySelector("#district");
    let url =
        "https://ckartisan.com/api/tambons?province=" +
        input_province.value +
        "&amphoe=" +
        input_district.value;
    console.log(url);

    fetch(url)
        .then((response) => response.json())
        .then((result) => {
            console.log(result);
            // UPDATE SELECT OPTION
            let input_subdistrict = document.querySelector("#subdistrict");
            input_subdistrict.innerHTML =
                '<option value="">กรุณาเลือกแขวง/ตำบล</option>';
            for (let item of result) {
                let option = document.createElement("option");
                option.text = item.tambon;
                option.value = item.tambon;
                input_subdistrict.appendChild(option);
            }
            // QUERY ZIPCODE
            showZipcode();
        });
}

function showZipcode() {
    let input_province = document.querySelector("#province");
    let input_district = document.querySelector("#district");
    let input_subdistrict = document.querySelector("#subdistrict");
    let url =
        "https://ckartisan.com/api/zipcodes?province=" +
        input_province.value +
        "&amphoe=" +
        input_district.value +
        "&tambon=" +
        input_subdistrict.value;
    console.log(url);

    fetch(url)
        .then((response) => response.json())
        .then((result) => {
            console.log(result);
            // UPDATE ZIPCODE
            let input_zip = document.querySelector("#zip");
            input_zip.value = "";
            for (let item of result) {
                input_zip.value = item.zipcode;
                break;
            }
        });
}

// EVENTS
document.querySelector("#province").addEventListener("change", (event) => {
    showAmphoes();
});

document.querySelector("#district").addEventListener("change", (event) => {
    showTambons();
});

document.querySelector("#subdistrict").addEventListener("change", (event) => {
    showZipcode();
});

// เรียกใช้ฟังก์ชันเพื่อโหลดจังหวัดเมื่อเริ่มต้น
showProvinces();
