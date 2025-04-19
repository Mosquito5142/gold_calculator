$(document).ready(function () {
  initializeFormControls();
  initializeEventListeners();
});
function searchNecklaces() {
  const searchName = document.getElementById("searchName").value.toLowerCase();
  const searchType = document.getElementById("searchType").value;
  const table = document.getElementById("necklaceTable");
  const rows = table.getElementsByTagName("tr");

  for (let i = 1; i < rows.length; i++) {
    const nameCell = rows[i].getElementsByTagName("td")[1];
    const typeCell = rows[i].getElementsByTagName("td")[2];

    if (nameCell && typeCell) {
      const name = nameCell.textContent.toLowerCase();
      const type = typeCell.textContent;

      const nameMatch = name.includes(searchName);
      const typeMatch = searchType === "" || type === searchType;
      const shouldShow = nameMatch && typeMatch;

      // Show/hide the main row
      rows[i].style.display = shouldShow ? "" : "none";

      // หา collapse row ที่เกี่ยวข้อง
      let nextRow = rows[i + 1];
      if (nextRow && nextRow.querySelector(".collapse")) {
        nextRow.style.display = shouldShow ? "" : "none";

        // ถ้าแสดงแถว และ collapse กำลังเปิดอยู่
        if (shouldShow) {
          const collapseElement = nextRow.querySelector(".collapse");
          if (collapseElement.classList.contains("show")) {
            collapseElement.style.display = "block";
          }
        }

        // ข้าม row ที่เป็น collapse เพื่อไปตรวจสอบ row ถัดไป
        i++;
      }
    }
  }
}

document.addEventListener("DOMContentLoaded", function () {
  document
    .getElementById("searchName")
    .addEventListener("keyup", searchNecklaces);
  document
    .getElementById("searchType")
    .addEventListener("change", searchNecklaces);
});
function initializeFormControls() {
  // ควบคุมการแสดง/ซ่อนฟิลด์ตามประเภทสร้อย
  $('select[name="type"]').on("change", handleTypeChange);

  // ควบคุมการแสดง/ซ่อนฟิลด์ตามรูปร่างสร้อย
  $('select[name="shapeshape_necklace"]').on("change", handleShapeChange);

  // เรียกใช้ฟังก์ชันครั้งแรก
  $('select[name="type"]').trigger("change");
  $('select[name="shapeshape_necklace"]').trigger("change");
}

function initializeEventListeners() {
  // Form submission
  $("#necklaceForm").on("submit", function (e) {
    e.preventDefault();
    saveNecklace();
  });

  // คำนวณน้ำหนักอัตโนมัติ
  $("#weight_ture, #ptt_ratio").on("input", calculateTrueWeight);

  // Expand/Collapse buttons
  $(".expand-button").on("click", function () {
    $(this).toggleClass("fa-plus-circle fa-minus-circle");
  });
}

function handleTypeChange() {
  const hollowFields = [
    'input[name="ptt_thick"]',
    'input[name="ptt_core"]',
    'input[name="ptt_ratio"]',
  ].map((selector) => $(selector).closest(".col-md-4"));

  if (this.value === "ตัน") {
    hideFields(hollowFields);
  } else {
    showFields(hollowFields);
  }
}

function handleShapeChange() {
  const widthField = $('input[name="proportions_width"]').closest(".col-md-3");
  const thickField = $('input[name="proportions_thick"]').closest(".col-md-3");

  if (this.value === "วงกลม") {
    showFields([widthField]);
    hideFields([thickField]);
  } else if (this.value === "สี่เหลี่ยม") {
    showFields([widthField, thickField]);
  }
}

function hideFields(fields) {
  fields.forEach((field) => {
    field.hide();
    field.find("input").val("").prop("required", false);
  });
}

function showFields(fields) {
  fields.forEach((field) => {
    field.show();
    field.find("input").prop("required", true);
  });
}

function calculateTrueWeight() {
  const weightTure = parseFloat($("#weight_ture").val());
  const pttRatio = parseFloat($("#ptt_ratio").val());

  if (!isNaN(weightTure) && !isNaN(pttRatio) && pttRatio > 0) {
    $("#true_weight").val((weightTure / pttRatio).toFixed(2));
  } else {
    $("#true_weight").val(weightTure);
  }
}
// เพิ่ม preview รูปภาพ
document.getElementById('necklace_image').addEventListener('change', function(e) {
  const preview = document.getElementById('preview');
  const file = e.target.files[0];
  
  if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
      }
      reader.readAsDataURL(file);
  }
});