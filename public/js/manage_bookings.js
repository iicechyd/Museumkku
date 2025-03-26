function openVisitorModal(bookingId) {
    $('#visitorModal_' + bookingId).modal('show');
}

function submitVisitorCount(bookingId) {
    var childrenQty = document.getElementById('children_qty_' + bookingId).value;
    var studentsQty = document.getElementById('students_qty_' + bookingId).value;
    var adultsQty = document.getElementById('adults_qty_' + bookingId).value;
    var kidQty = document.getElementById('kid_qty_' + bookingId).value;
    var disabledQty = document.getElementById('disabled_qty_' + bookingId).value;
    var elderlyQty = document.getElementById('elderly_qty_' + bookingId).value;
    var monkQty = document.getElementById('monk_qty_' + bookingId).value;
    var freeTeachersQty = document.getElementById('teacher_free_qty_' + bookingId).value;

    if (childrenQty >= 0 && studentsQty >= 0 && adultsQty >= 0 && kidQty >= 0 && disabledQty >= 0 && elderlyQty >= 0 && monkQty >= 0 && freeTeachersQty >= 0) {
        document.getElementById('actual_children_qty_' + bookingId).value = childrenQty;
        document.getElementById('actual_students_qty_' + bookingId).value = studentsQty;
        document.getElementById('actual_adults_qty_' + bookingId).value = adultsQty;
        document.getElementById('actual_kid_qty_' + bookingId).value = kidQty;
        document.getElementById('actual_disabled_qty_' + bookingId).value = disabledQty;
        document.getElementById('actual_elderly_qty_' + bookingId).value = elderlyQty;
        document.getElementById('actual_monk_qty_' + bookingId).value = monkQty;
        document.getElementById('actual_free_teachers_qty_' + bookingId).value = freeTeachersQty;
        document.getElementById('status_' + bookingId).value = 'checkin';
        document.getElementById('statusForm_' + bookingId).submit();
    } else {
        alert('กรุณากรอกจำนวนผู้เข้าชมที่ถูกต้อง');
    }
}

function openCancelModal(bookingId) {
    $('#cancelModal_' + bookingId).modal('show');
}

function submitCancelForm(bookingId) {
    var reason = document.getElementById('reason_' + bookingId).value;

    if (!reason) {
        alert("กรุณากรอกหมายเหตุการยกเลิก");
        return;
    }
    document.getElementById('comments_' + bookingId).value = reason;
    document.getElementById('status_' + bookingId).value = 'cancel';

    var fields = ['actual_children_qty', 'actual_students_qty', 'actual_adults_qty', 'actual_kid_qty', 'actual_disabled_qty', 'actual_elderly_qty', 'actual_monk_qty', 'actual_free_teachers_qty'];
    fields.forEach(function (field) {
        var input = document.getElementById(field + '_' + bookingId);
        if (!input.value) {
            input.value = 0;
        }
    });

    document.getElementById('statusForm_' + bookingId).submit();
    $('#cancelModal_' + bookingId).modal('hide');
}

function calculateTotal(bookingId) {
    let totalVisitors = 0;
    let totalPrice = 0;

    document.querySelectorAll(`#visitorModal_${bookingId} .visitor-input`).forEach(input => {
        let qty = parseInt(input.value) || 0;
        let price = parseFloat(input.dataset.price) || 0;

        totalVisitors += qty;
        totalPrice += qty * price;
    });

    document.getElementById(`totalVisitors_${bookingId}`).textContent = totalVisitors;
    document.getElementById(`totalPrice_${bookingId}`).textContent = totalPrice.toFixed(2);
}

function updateFreeTeachers(bookingId) {
    let studentQty = parseInt(document.getElementById(`students_qty_${bookingId}`).value) || 0;
    let childrenQty = parseInt(document.getElementById(`children_qty_${bookingId}`).value) || 0;
    
    let totalEligibleQty = studentQty + childrenQty;
    
    let maxFreeTeachers = Math.floor(totalEligibleQty / 10);
    
    document.getElementById(`teacher_free_qty_${bookingId}`).max = maxFreeTeachers;
    document.getElementById(`maxFreeTeachers_${bookingId}`).textContent = maxFreeTeachers;

    let teacherInput = document.getElementById(`teacher_free_qty_${bookingId}`);
    if (parseInt(teacherInput.value) > maxFreeTeachers) {
        teacherInput.value = maxFreeTeachers;
    }
}

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('shown.bs.modal', function () {
            let bookingId = this.id.split("_")[1];
            updateFreeTeachers(bookingId);
            calculateTotal(bookingId);
            document.querySelectorAll(`#visitorModal_${bookingId} .visitor-input`).forEach(input => {
                input.dataset.oldValue = input.value; 
            });
        });
    });
});

function resetVisitorForm(bookingId) {
    document.querySelectorAll(`#visitorModal_${bookingId} .visitor-input`).forEach(input => {
        input.value = input.dataset.oldValue || 0;
    });

    updateFreeTeachers(bookingId);
    calculateTotal(bookingId);
}

document.querySelectorAll('.visitor-input').forEach(input => {
    input.addEventListener('input', function () {
        let bookingId = this.dataset.bookingId;
        updateFreeTeachers(bookingId);
        calculateTotal(bookingId);

    });
});

function validateFreeTeachersInput(input, bookingId) {
    let maxFreeTeachers = parseInt(document.getElementById(`maxFreeTeachers_${bookingId}`).textContent) || 0;
    input.value = Math.min(Math.max(parseInt(input.value) || 0, 0), maxFreeTeachers);
}
