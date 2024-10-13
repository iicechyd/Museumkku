function toggleCommentsAndButton() {
    var status = document.getElementById("statusSelect").value;
    var commentsField = document.getElementById("commentsField");
    var updateButton = document.getElementById("updateButton");

    // แสดงฟิลด์ comments และปุ่มยืนยันเมื่อสถานะเป็น 'cancel' (ค่าเท่ากับ 2)
    if (status == "cancel") {
        commentsField.style.display = "block";
        updateButton.style.display = "block";
    } else {
        commentsField.style.display = "none";
        updateButton.style.display = "none";
    }
}

// เรียกใช้ฟังก์ชันนี้เมื่อหน้าเพจโหลด เพื่อให้แน่ใจว่าฟิลด์ comments และปุ่มถูกตั้งค่าอย่างถูกต้อง
document.addEventListener("DOMContentLoaded", function () {
    toggleCommentsAndButton();
});
