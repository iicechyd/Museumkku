flatpickr("#closed_on", {
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
    onReady: function() {
        document.querySelector('.input-group-text').addEventListener('click', () => {
            document.querySelector("#closed_on")._flatpickr.open();
        });
    }
});