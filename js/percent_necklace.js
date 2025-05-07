/**
 * JavaScript สำหรับหน้าแสดงเปอร์เซ็นต์สร้อย
 */

/**
 * ฟังก์ชันที่ทำงานเมื่อโหลดเอกสารเสร็จสมบูรณ์
 */
$(document).ready(function () {
  // กำหนดค่าเริ่มต้นสำหรับ Select2
  initializeSelect2();

  // เพิ่ม event listeners สำหรับปุ่มแสดง/ซ่อนรายละเอียด
  initializeToggleDetailsButton();

  // เพิ่ม event listeners สำหรับการคำนวณเรียลไทม์
  initializeCalculationInputs();

  // ตรวจสอบว่าผู้ใช้ได้ปรับแต่งค่า pxPerMM หรือไม่
  checkCalibration();
});

/**
 * ตั้งค่า Select2 dropdown
 */
function initializeSelect2() {
  $(".select2").select2({
    width: "100%",
    placeholder: "-- เลือกสร้อย --",
  });
}

/**
 * ตั้งค่าปุ่มแสดง/ซ่อนรายละเอียด
 */
function initializeToggleDetailsButton() {
  $("#toggleDetailsBtn").click(function () {
    const detailsDiv = $("#calculationDetails");
    const icon = $(this).find("i");

    if (detailsDiv.is(":visible")) {
      detailsDiv.slideUp();
      icon.removeClass("fa-chevron-up").addClass("fa-chevron-down");
      $(this).html(
        '<i class="fas fa-chevron-down"></i> แสดงรายละเอียดการคำนวณ'
      );
    } else {
      detailsDiv.slideDown();
      icon.removeClass("fa-chevron-down").addClass("fa-chevron-up");
      $(this).html('<i class="fas fa-chevron-up"></i> ซ่อนรายละเอียดการคำนวณ');
    }
  });
}

/**
 * ตั้งค่า input fields สำหรับการคำนวณอัตโนมัติ
 */
function initializeCalculationInputs() {
  // ผูกการคำนวณกับ input บาท
  $("#pn_baht").on("input", function () {
    const baht = parseFloat($(this).val()) || 0;
    // คำนวณน้ำหนักกรัมจากมูลค่าบาท (1 บาท = 15.2 กรัม)
    const calculatedGrams = baht * 15.2;

    // แสดงค่าในช่องกรอกน้ำหนักกรัม
    $("#pn_grams").val(calculatedGrams.toFixed(2));

    // ถ้ามีการกรอกค่าบาท ให้คำนวณและแสดงผลอัตโนมัติ
    if (baht > 0) {
      calculateAndShowResults(baht, calculatedGrams);
    } else {
      $("#result").hide();
    }
  });

  // ผูกการคำนวณกับ input กรัม
  $("#pn_grams").on("input", function () {
    const grams = parseFloat($(this).val()) || 0;
    // คำนวณบาทจากน้ำหนักกรัม (กรัม / 15.2 = บาท)
    const calculatedBaht = grams / 15.2;

    // แสดงค่าในช่องกรอกบาท
    $("#pn_baht").val(calculatedBaht.toFixed(2));

    // ถ้ามีการกรอกค่ากรัม ให้คำนวณและแสดงผลอัตโนมัติ
    if (grams > 0) {
      calculateAndShowResults(calculatedBaht, grams);
    } else {
      $("#result").hide();
    }
  });

  // ผูกการคำนวณกับ input Scale Wire Weight
  $("#custom_scale_wire_weight").on("input", function () {
    const scale_wire_weight = parseFloat($(this).val()) || 0;

    if (scale_wire_weight > 0) {
      calculateAndShowRatios(scale_wire_weight);
    } else {
      $("#ratioResult").hide();
    }
  });

  // เพิ่ม event listener สำหรับปุ่มคำนวณ
  $("#calculateBtn").click(function () {
    performAllCalculations();
  });
}

/**
 * ทำการคำนวณทั้งหมด
 */
function performAllCalculations() {
  const baht = parseFloat($("#pn_baht").val()) || 0;
  const grams = parseFloat($("#pn_grams").val()) || 0;
  const scale_wire_weight =
    parseFloat($("#custom_scale_wire_weight").val()) || 0;

  let hasError = false;
  let message = "";

  if (baht <= 0 && grams <= 0 && scale_wire_weight <= 0) {
    hasError = true;
    message = "กรุณากรอกข้อมูลอย่างน้อยหนึ่งรายการ";
  }

  if (hasError) {
    Swal.fire({
      title: "ข้อมูลไม่ถูกต้อง",
      text: message,
      icon: "warning",
    });
    return;
  }

  // ถ้ามีการกรอกค่าบาทหรือน้ำหนัก
  if (grams > 0 || baht > 0) {
    if (grams <= 0) {
      grams = baht * 15.2;
      $("#pn_grams").val(grams.toFixed(2));
    }
    if (baht <= 0) {
      baht = grams / 15.2;
      $("#pn_baht").val(baht.toFixed(2));
    }
    calculateAndShowResults(baht, grams);
  }

  // ถ้ามีการกรอก scale_wire_weight
  if (scale_wire_weight > 0) {
    calculateAndShowRatios(scale_wire_weight);
  }
}

/**
 * คำนวณและแสดงผลลัพธ์มูลค่าแต่ละส่วน
 * @param {number} baht - มูลค่าเป็นบาท
 * @param {number} grams - น้ำหนักเป็นกรัม
 */
function calculateAndShowResults(baht, grams) {
  if (grams <= 0) {
    return;
  }

  // คำนวณราคาต่อกรัม (ใช้เพื่ออ้างอิงในการคำนวณต่อไป)
  const pricePerGram = baht / grams;

  // สร้างแถวสำหรับผลรวม
  const totalRow = `
        <tr class="total-row text-center">
            <td>-</td>
            <td>ทั้งหมด</td>
            <td>${grams.toFixed(2)}</td>
            <td>100%</td>
            <td>${baht.toFixed(2)}</td>
        </tr>
    `;

  // สร้างแถวสำหรับรายละเอียดแต่ละชิ้นส่วนจากข้อมูลที่มี
  const detailRows = generateDetailRows(grams, pricePerGram);

  // รวมแถวทั้งหมดและแสดงผล
  $("#resultTableBody").html(totalRow + detailRows);
  $("#result").show();
}

/**
 * สร้างแถวสำหรับรายละเอียดแต่ละชิ้นส่วน
 * @param {number} totalGrams - น้ำหนักรวมทั้งหมด
 * @param {number} pricePerGram - ราคาต่อกรัม
 * @returns {string} - HTML สำหรับแถวของตาราง
 */
function generateDetailRows(totalGrams, pricePerGram) {
  let rows = "";

  // วนลูปผ่านรายการรายละเอียดชิ้นส่วนและสร้างแถวสำหรับแต่ละชิ้น
  // ส่วนนี้จะถูกนำเข้าจากข้อมูลที่มีอยู่ในหน้า PHP

  return rows;
}

function calculateAndShowRatios(scale_wire_weight) {
  if (scale_wire_weight <= 0) {
    return;
  }

  // สร้างแถวสำหรับรายละเอียดแต่ละชิ้นส่วน
  const tableRows = generateRatioRows(scale_wire_weight);

  // แสดงผลลัพธ์
  $("#ratioTableBody").html(tableRows);
  $("#visualizationContainer").html("").hide();
  $("#ratioResult").show();

  // เพิ่มคำอธิบายการคำนวณ ratio จากประเภทมัลติ (ถ้ามี)
  const referenceWidth = calculateReferenceWidth();
  const referenceType =
    referenceWidth > 0 && referenceWidth !== scale_wire_weight
      ? "มัลติ"
      : "สร้อย";

  // เพิ่ม note เกี่ยวกับที่มาของค่า ratio
  $("#ratioTableBody").after(`
    <tr>
      <td colspan="9" class="text-muted small text-end">
        <i class="fas fa-info-circle"></i> 
        อัตราส่วนคำนวณจากความกว้างอ้างอิง (${referenceWidth.toFixed(
          2
        )} มม.) จากประเภท ${referenceType}
      </td>
    </tr>
  `);

  // เพิ่ม event listeners สำหรับปุ่มแสดง/ซ่อนภาพ
  setupVisualizationButtons();
}

/**
 * คำนวณและแสดงผลลัพธ์อัตราส่วนอะไหล่
 * @param {number} scale_wire_weight - ความกว้างของสร้อย
 */
function calculateAndShowRatios(scale_wire_weight) {
  if (scale_wire_weight <= 0) {
    return;
  }

  // สร้างแถวสำหรับรายละเอียดแต่ละชิ้นส่วน
  const tableRows = generateRatioRows(scale_wire_weight);

  // แสดงผลลัพธ์
  $("#ratioTableBody").html(tableRows);
  $("#visualizationContainer").html("").hide();
  $("#ratioResult").show();

  // เพิ่ม event listeners สำหรับปุ่มแสดง/ซ่อนภาพ
  setupVisualizationButtons();
}

/**
 * ตั้งค่าปุ่มสำหรับแสดง/ซ่อนภาพจำลอง
 */
function setupVisualizationButtons() {
  $(".toggle-vis-btn").click(function () {
    const partId = $(this).data("part-id");
    $(`#vis_row_${partId}`).show();
    $(this).parents("tr").addClass("active-row");
  });

  $(".hide-vis-btn").click(function () {
    const partId = $(this).data("part-id");
    $(`#vis_row_${partId}`).hide();
    $(`#row_${partId}`).removeClass("active-row");
  });
}

function checkCalibration() {
  const savedCalibration = localStorage.getItem("pxPerMMCalibration");

  // ต้องการแสดงข้อความก่อนแบบฟอร์ม หรือตรงที่เหมาะสม
  const targetElement = $(".card-body form").first(); // หรือแทนที่ด้วย selector ที่เหมาะสม

  if (savedCalibration !== null) {
    // มีการปรับแต่งแล้ว แสดงข้อความสีฟ้า
    const customValue = parseFloat(savedCalibration).toFixed(2);
    const message = `
      <div class="alert alert-info alert-dismissible fade show">
        <i class="fas fa-info-circle"></i> คุณกำลังใช้การปรับแต่งขนาดแสดงผล (${customValue} พิกเซล/มม.) หากรูปที่ได้ไม่ตรงปรับแก้ที่
        <a href="profile.php" class="alert-link">โปรไฟล์ของคุณ</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>`;

    // ลบแจ้งเตือนเดิม (ถ้ามี)
    $(".alert-warning, .alert-info").remove();

    // เพิ่มข้อความแจ้งเตือนที่ด้านบนของฟอร์ม
    if (targetElement.length > 0) {
      targetElement.before(message);
    } else {
      // ถ้าไม่พบ targetElement ให้แทรกใต้ card-header แทน
      $(".card-header").first().after(message);
    }
  } else {
    // ยังไม่มีการปรับแต่ง แสดงข้อความสีเหลือง
    const message = `
      <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>คำเตือน!</strong> คุณยังไม่ได้ปรับแต่งการแสดงขนาดจริง การแสดงผลอาจไม่ตรงกับความเป็นจริง 
        <a href="profile.php" class="alert-link">คลิกที่นี่เพื่อปรับแต่ง</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>`;

    // ลบแจ้งเตือนเดิม (ถ้ามี)
    $(".alert-warning, .alert-info").remove();

    // เพิ่มข้อความแจ้งเตือน
    if (targetElement.length > 0) {
      targetElement.before(message);
    } else {
      // ถ้าไม่พบ targetElement ให้แทรกใต้ card-header แทน
      $(".card-header").first().after(message);
    }
  }
}

/**
 * เพิ่มฟังก์ชันดึงค่า PxPerMM จาก localStorage
 * @returns {number} ค่า pixel ต่อ mm ที่ใช้ในการแสดงผล
 */
function getPxPerMM() {
  // ตรวจสอบค่าที่บันทึกไว้ใน localStorage
  const savedCalibration = localStorage.getItem("pxPerMMCalibration");
  // ถ้ามีค่าที่บันทึกไว้ ให้ใช้ค่านั้น
  if (savedCalibration !== null) {
    return parseFloat(savedCalibration);
  }
  // วิธีคำนวณจริงจากหน้าจอ
  const div = document.createElement("div");
  div.style.width = "1mm";
  div.style.position = "absolute";
  div.style.visibility = "hidden";
  document.body.appendChild(div);
  const pxPerMM = div.offsetWidth;
  document.body.removeChild(div);
  return pxPerMM;
}

/**
 * สร้าง SVG สี่เหลี่ยมตามขนาดที่กำหนด
 * ถ้าขนาดเกินกรอบ ให้คืนข้อความเตือนแทน
 * @param {number} width - ความกว้างในหน่วย มม.
 * @param {number} height - ความสูงในหน่วย มม.
 * @returns {string} - SVG content หรือข้อความเตือน
 */
function generateRectSVG(width, height) {
  // กำหนดขนาดสูงสุด (มม.)
  const maxWidthMM = 80;
  const maxHeightMM = 50;

  if (width > maxWidthMM || height > maxHeightMM) {
    return `<div class="alert alert-warning text-center mt-2 mb-0">
            ขนาดใหญ่เกินกรอบ กรุณาดูเฉพาะค่าตัวเลข
        </div>`;
  }

  const pxPerMM = 3.78;
  const widthPx = width * pxPerMM;
  const heightPx = height * pxPerMM;
  const centerX = 150;
  const centerY = 100;
  const x = centerX - widthPx / 2;
  const y = centerY - heightPx / 2;

  let svgContent = `<rect x="${x}" y="${y}" width="${widthPx}" height="${heightPx}" stroke="#000" stroke-width="1" fill="#FFC0CB" />`;
  // scale bar 10 มม.
  svgContent += `
        <line x1="20" y1="180" x2="${
          20 + 10 * pxPerMM
        }" y2="180" stroke="#000" stroke-width="1" />
        <text x="${
          20 + 5 * pxPerMM
        }" y="175" text-anchor="middle" font-size="10">10 มม.</text>
    `;
  return svgContent;
}

/**
 * เลื่อนไปที่ภาพจำลอง
 * @param {string} id - ID ของ element ที่ต้องการเลื่อนไป
 */
function scrollToVisualization(id) {
  const element = document.getElementById(id);
  if (element) {
    element.scrollIntoView({
      behavior: "smooth",
      block: "center",
    });
    // เพิ่มการไฮไลท์ชั่วคราว
    element.style.backgroundColor = "#fff3cd";
    setTimeout(() => {
      element.style.backgroundColor = "#f8f9fa";
    }, 2000);
  }
}

/**
 * แสดงรูปภาพเต็มในโมดัล
 * @param {string} src - ที่อยู่ของรูปภาพ
 * @param {string} title - ชื่อรูปภาพ
 */
function showFullImage(src, title) {
  document.getElementById("fullImage").src = src;
  document.getElementById("imageModalTitle").textContent = title;
  const modal = new bootstrap.Modal(document.getElementById("imageModal"));
  modal.show();
}

$("#saveAsCopyBtn").click(function () {
  // ดึงค่าจากฟอร์มคำนวณ
  const pn_name = prompt("กรุณาตั้งชื่อรายการใหม่ (สำเนา):");
  if (!pn_name) return;

  const pn_grams = $("#pn_grams").val();
  const custom_scale_wire_weight = $("#custom_scale_wire_weight").val();
  const selected_pn_id = new URLSearchParams(window.location.search).get(
    "pn_id"
  );

  // เพิ่มข้อมูลประเภทมัลติเข้าไปเพื่อรองรับการคำนวณที่ถูกต้อง
  const referenceWidth = calculateReferenceWidth();

  // ตรวจสอบว่ามีการกรอกข้อมูลที่จำเป็นครบถ้วน
  if (!pn_grams || !selected_pn_id) {
    Swal.fire("ผิดพลาด", "กรุณากรอกข้อมูลน้ำหนัก (กรัม) ให้ครบถ้วน", "error");
    return;
  }

  // แสดง loading
  Swal.fire({
    title: "กำลังบันทึกข้อมูล",
    html: "กรุณารอสักครู่...",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  // ส่ง AJAX ไป save_percent_copy.php
  $.ajax({
    url: "actions/save_percent_copy.php",
    type: "POST",
    data: {
      pn_name: pn_name,
      pn_grams: pn_grams,
      custom_scale_wire_weight: custom_scale_wire_weight,
      reference_width: referenceWidth, // เพิ่มค่านี้เข้าไป
      original_pn_id: selected_pn_id,
    },
    dataType: "json",
    success: function (data) {
      if (data.success) {
        Swal.fire({
          title: "บันทึกสำเร็จ",
          text: "สร้างรายการใหม่เรียบร้อย",
          icon: "success",
          confirmButtonText: "ตกลง",
        }).then(() => {
          // ถ้าต้องการเปิดรายการใหม่ที่สร้างขึ้น
          if (data.new_id) {
            window.location.href = `percent_necklace.php?pn_id=${data.new_id}`;
          } else {
            location.reload();
          }
        });
      } else {
        Swal.fire("ผิดพลาด", data.message || "เกิดข้อผิดพลาด", "error");
      }
    },
    error: function (xhr, status, error) {
      Swal.fire("ผิดพลาด", "ไม่สามารถบันทึกข้อมูลได้: " + error, "error");
      console.error(xhr.responseText);
    },
  });
});
