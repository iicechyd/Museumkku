function updateEndTime(startTimeInput) {
    const startTime = startTimeInput.value;
    const endTimeInput = startTimeInput
        .closest(".modal-body")
        .querySelector('input[name="end_time"]');

    if (startTime) {
        const startDate = new Date();
        const [hours, minutes] = startTime.split(":");
        startDate.setHours(hours);
        startDate.setMinutes(minutes);
        startDate.setMinutes(startDate.getMinutes() + 30); // Add 30 minutes

        const formattedHours = String(startDate.getHours()).padStart(2, "0");
        const formattedMinutes = String(startDate.getMinutes()).padStart(
            2,
            "0"
        );
        endTimeInput.value = `${formattedHours}:${formattedMinutes}`;
    }
}

function updateInsertEndTime(startTimeInput) {
    const startTime = startTimeInput.value;
    const endTimeInput = document.getElementById("insert_end_time");

    if (startTime) {
        const startDate = new Date();
        const [hours, minutes] = startTime.split(":");
        startDate.setHours(hours);
        startDate.setMinutes(minutes);
        startDate.setMinutes(startDate.getMinutes() + 30); // Add 30 minutes

        const formattedHours = String(startDate.getHours()).padStart(2, "0");
        const formattedMinutes = String(startDate.getMinutes()).padStart(
            2,
            "0"
        );
        endTimeInput.value = `${formattedHours}:${formattedMinutes}`;
    }
}
