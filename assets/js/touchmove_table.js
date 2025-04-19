document.addEventListener("DOMContentLoaded", function () {
    const tableContainer = document.querySelector(".table-responsive");

    let isDragging = false;
    let startX, startY, scrollLeft, scrollTop;

    tableContainer.addEventListener("mousedown", (e) => {
        if (!e.shiftKey) return; // ต้องกด Shift ค้างไว้ก่อนถึงจะลากได้
        e.preventDefault(); // ป้องกันข้อความถูกเลือก
        isDragging = true;
        startX = e.pageX - tableContainer.offsetLeft;
        startY = e.pageY - tableContainer.offsetTop;
        scrollLeft = tableContainer.scrollLeft;
        scrollTop = tableContainer.scrollTop;
        tableContainer.style.cursor = "grabbing"; // เปลี่ยน cursor เป็นจับ
    });

    tableContainer.addEventListener("mouseleave", () => {
        isDragging = false;
        tableContainer.style.cursor = "default";
    });

    tableContainer.addEventListener("mouseup", () => {
        isDragging = false;
        tableContainer.style.cursor = "default";
    });

    tableContainer.addEventListener("mousemove", (e) => {
        if (!isDragging) return;
        e.preventDefault(); // ป้องกันการคลุมข้อความ
        const x = e.pageX - tableContainer.offsetLeft;
        const y = e.pageY - tableContainer.offsetTop;
        const walkX = (x - startX) * 1.5; // ปรับค่าความไวของการเลื่อนแนวนอน
        const walkY = (y - startY) * 1.5; // ปรับค่าความไวของการเลื่อนแนวตั้ง
        tableContainer.scrollLeft = scrollLeft - walkX;
        tableContainer.scrollTop = scrollTop - walkY;
    });

    tableContainer.addEventListener("touchstart", (e) => {
        if (e.touches.length < 2) return;
        isDragging = true;
        startX = e.touches[0].pageX - tableContainer.offsetLeft;
        startY = e.touches[0].pageY - tableContainer.offsetTop;
        scrollLeft = tableContainer.scrollLeft;
        scrollTop = tableContainer.scrollTop;
    });

    tableContainer.addEventListener("touchmove", (e) => {
        if (!isDragging) return;
        e.preventDefault();
        const x = e.touches[0].pageX - tableContainer.offsetLeft;
        const y = e.touches[0].pageY - tableContainer.offsetTop;
        const walkX = (x - startX) * 1.5;
        const walkY = (y - startY) * 1.5;
        tableContainer.scrollLeft = scrollLeft - walkX;
        tableContainer.scrollTop = scrollTop - walkY;
    });

    tableContainer.addEventListener("touchend", () => {
        isDragging = false;
    });
});