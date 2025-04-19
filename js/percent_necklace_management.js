$(document).ready(function () {
  $("#percent_necklace").DataTable();
  calculateTotals();
  $('#cut_weight, #hook_weight, input[name="pn_grams"]').on(
    "input",
    calculateTotals
  );
  $("#percentModal").on("hidden.bs.modal", resetModal);

  calculateRatios();
  $(document).on("input", ".wire-dimension, .parts-dimension", function () {
    calculateRatios();
  });
  // Baht และ Grams conversion
  $("#pn_baht").on("input", function () {
    const gramsValue = (parseFloat(this.value) || 0) * 15.2;
    $('input[name="pn_grams"]').val(gramsValue.toFixed(2));
    calculateTotals();
  });

  $('input[name="pn_grams"]').on("input", function () {
    if (document.activeElement === this) {
      $("#pn_baht").val("");
    }
    calculateTotals();
  });

  // Update Baht when editing
  if ($("#pn_id").val() && parseFloat($('input[name="pn_grams"]').val()) > 0) {
    $("#pn_baht").val(
      (parseFloat($('input[name="pn_grams"]').val()) / 15.2).toFixed(2)
    );
  }

  // Cut weight validation (must be negative)
  $("#cut_weight").on("input", function () {
    if (parseFloat(this.value) > 0) {
      Swal.fire({
        icon: "warning",
        title: "ค่าไม่ถูกต้อง",
        text: "น้ำหนักเผื่อตัดลายควรเป็นเลขติดลบ เช่น -0.01",
        confirmButtonText: "เข้าใจแล้ว",
      });
      $(this).val("-0.01");
      calculateTotals();
    }
  });

  // รูปภาพ Preview
  $("#image").on("change", function () {
    const $container = $("#image_preview_container");
    const $preview = $("#image_preview");

    if (this.files && this.files[0]) {
      const reader = new FileReader();
      reader.onload = (e) => {
        $preview.attr("src", e.target.result);
        $container.show();
        window.hasExistingImage = true;
      };
      reader.readAsDataURL(this.files[0]);

      // ตรวจสอบขนาดไฟล์
      if (this.files[0].size > 5 * 1024 * 1024) {
        Swal.fire({
          icon: "error",
          title: "ไฟล์มีขนาดใหญ่เกินไป",
          text: "กรุณาเลือกไฟล์ขนาดไม่เกิน 5MB",
        });
        this.value = "";
        $container.hide();
      }
    } else {
      $container.hide();
    }
  });
});

function resetModal() {
  // Reset form fields
  $(
    '#pn_id, input[name="pn_name"], #pn_baht, input[name="pn_grams"], #image'
  ).val("");
  $("#image_preview_container").hide();
  window.imageFileName = "";
  window.hasExistingImage = false;
  $(".detail-row").remove();

  // Reset ค่าในแถวค่าคงที่
  $('input[name="pnd_weight_special[]"]:eq(0)').val("-2.6");
  $('input[name="pnd_weight_special[]"]:eq(1)').val("3");
  $(
    'input[name="pnd_long_special[]"]:eq(0), input[name="pnd_long_special[]"]:eq(1)'
  ).val("");
  $(
    "#total_weight, #total_percent, #total_length, #cut_percent, #hook_percent"
  ).val("0");
  $(
    ".ratio-width, .ratio-thick, .parts-ratio-width, .parts-ratio-height, .parts-ratio-thick"
  ).val("");

  calculateTotals();
  calculateRatios();
}

function calculateTotals() {
  const $pnGramsInput = $('input[name="pn_grams"]');
  const $totalWeightEl = $("#total_weight");
  const $totalPercentEl = $("#total_percent");

  // Get values from special fields
  const cutWeight =
    parseFloat($('input[name="pnd_weight_special[]"]:eq(0)').val()) || 0;
  const hookWeight =
    parseFloat($('input[name="pnd_weight_special[]"]:eq(1)').val()) || 0;
  const cutLength =
    parseFloat($('input[name="pnd_long_special[]"]:eq(0)').val()) || 0;
  const hookLength =
    parseFloat($('input[name="pnd_long_special[]"]:eq(1)').val()) || 0;

  // คำนวณผลรวม
  let totalWeight = cutWeight + hookWeight;
  let totalLength = cutLength + hookLength;
  let totalPercent = 0;

  // Calculate totals from custom rows
  $('input[name="pnd_weight_grams[]"]').each(function (index) {
    const weight = parseFloat($(this).val()) || 0;
    const length =
      parseFloat($('input[name="pnd_long_inch[]"]').eq(index).val()) || 0;
    totalWeight += weight;
    totalLength += length;
  });

  // Update total values
  $totalWeightEl.val(totalWeight.toFixed(2));
  $("#total_length").val(totalLength.toFixed(2));

  // คำนวณเปอร์เซ็นต์
  if (totalWeight !== 0) {
    // คำนวณเปอร์เซ็นต์สำหรับแต่ละแถว
    $('input[name="pnd_weight_grams[]"]').each(function () {
      const weight = parseFloat($(this).val()) || 0;
      const percent = (weight / totalWeight) * 100;
      $(this).parent().next().find("input").val(percent.toFixed(2));
      totalPercent += percent;
    });

    // คำนวณเปอร์เซ็นต์สำหรับแถวพิเศษ
    $("#cut_percent").val(((cutWeight / totalWeight) * 100).toFixed(2));
    $("#hook_percent").val(((hookWeight / totalWeight) * 100).toFixed(2));
    totalPercent +=
      (cutWeight / totalWeight) * 100 + (hookWeight / totalWeight) * 100;
    $totalPercentEl.val(totalPercent.toFixed(2));
  }

  // เปรียบเทียบน้ำหนักรวมกับน้ำหนักที่กรอก
  const pnGrams = parseFloat($pnGramsInput.val()) || 0;
  const weightMatch = Math.abs(totalWeight - pnGrams) < 0.01;

  // ปรับสีและสถานะปุ่มบันทึก
  $totalWeightEl.css("backgroundColor", weightMatch ? "#58FA04" : "#D84040");
  $pnGramsInput.css("backgroundColor", weightMatch ? "#58FA04" : "#D84040");
  $(".modal-footer .btn-primary")
    .prop("disabled", !weightMatch)
    .attr(
      "title",
      weightMatch ? "" : "น้ำหนักรวมต้องเท่ากับน้ำหนัก (กรัม) ที่ระบุ"
    );
}

function addDetailRow() {
  const row = $(`
        <div class="detail-row">
            <div class="row mt-1 bg-dark-emphasis">
                <div class="col-md-2">
                    <label class="form-label">ประเภท</label>
                    <select class="form-select type-select" name="pnd_type[]" required style="background-color: #fff9c4;">
                        <option value="" disabled selected>เลือกประเภท</option>
                        <option value="สร้อย">สร้อย</option>
                        <option value="อะไหล่">อะไหล่</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">ชื่อ</label>
                    <input type="text" class="form-control" name="pnd_name[]" required style="background-color: #fff9c4;">
                </div>
                <div class="col-md-2">
                    <label class="form-label">น้ำหนัก</label>
                    <input type="number" class="form-control" name="pnd_weight_grams[]" step="0.01" required style="background-color: #fff9c4;">
                </div>
                <div class="col-md-2">
                    <label class="form-label">%</label>
                    <input type="number" class="form-control" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label">ความยาว</label>
                    <input type="number" class="form-control" name="pnd_long_inch[]" step="0.01" style="background-color: #fff9c4;">
                </div>
            </div>
            
            <!-- ส่วนรายละเอียดเพิ่มเติมตามประเภท -->
            <div class="row mt-2 detail-parts necklace-parts" style="display:none;">
                <input type="hidden" name="ndp_id[]" value="">
                <div class="col-md-2">
                    <label class="form-label">รูลวด</label>
                    <input type="number" class="form-control" name="wire_hole[]" step="0.01" required style="background-color: #fff9c4;">
                </div>
                <div class="col-md-2">
                    <label class="form-label">หนา</label>
                    <input type="number" class="form-control" name="wire_thick[]" step="0.01" required style="background-color: #fff9c4;">
                </div>
                <div class="col-md-2">
                    <label class="form-label">ไส้</label>
                    <input type="number" class="form-control" name="wire_core[]" step="0.01" required style="background-color: #fff9c4;">
                </div>
                <div class="col-md-2">
                    <label class="form-label">กว้าง(มม.)</label>
                    <input type="number" class="form-control wire-dimension" name="scale_wire_weight[]" step="0.01" required style="background-color: #fff9c4;">
                </div>
                <div class="col-md-2">
                    <label class="form-label">หนา(มม.)</label>
                    <input type="number" class="form-control wire-dimension" name="scale_wire_thick[]" step="0.01" required style="background-color: #fff9c4;">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeDetailRow(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>     
            <div class="row mt-2 detail-parts parts-parts" style="display:none;">
                <div class="col-md-2 col-12">
                    <label class="form-label">กว้าง(มม.)</label>
                    <input type="number" class="form-control parts-dimension" name="parts_weight[]" step="0.01" required style="background-color: #fff9c4;">
                    <div class="mt-1 text-info small text-end">
                        ratio(กว้าง): <span class="parts-ratio-width">-</span>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <label class="form-label">สูง(มม.)</label>
                    <input type="number" class="form-control parts-dimension" name="parts_height[]" step="0.01" required style="background-color: #fff9c4;">
                    <div class="mt-1 text-info small text-end">
                        ratio(สูง): <span class="parts-ratio-height">-</span>
                    </div>
                </div>
                <div class="col-md-2 col-12">
                    <label class="form-label">หนา(มม.)</label>
                    <input type="number" class="form-control parts-dimension" name="parts_thick[]" step="0.01" required style="background-color: #fff9c4;">
                    <div class="mt-1 text-info small text-end">
                        ratio(หนา): <span class="parts-ratio-thick">-</span>
                    </div>
                </div>
                <div class="col-md-2">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeDetailRow(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <hr>
        </div>
        `).appendTo("#detailsContainer");

  // เพิ่ม event listener สำหรับการคำนวณทั้งหมด
  row.find("input").on("input", function () {
    calculateTotals();
    calculateRatios();
  });

  // เพิ่ม event listener สำหรับการเปลี่ยนประเภท
  row.find(".type-select").on("change", function () {
    const detailRow = $(this).closest(".detail-row");
    updateDetailPartsVisibility(detailRow, this.value);
    calculateRatios(); // คำนวณ ratio ใหม่เมื่อเปลี่ยนประเภท
  });

  // เริ่มต้นแสดงตามค่าเริ่มต้น
  updateDetailPartsVisibility(row, row.find(".type-select").val());

  calculateTotals();
  calculateRatios(); // คำนวณ ratio ครั้งแรกเมื่อเพิ่มแถว
  return row[0];
}
// ฟังก์ชันคำนวณ ratio โดยใช้ค่ากว้างของสร้อย
function calculateRatios() {
  let firstNecklaceWidth = null;
  $('input[name="scale_wire_weight[]"]').each(function () {
    const val = parseFloat($(this).val());
    if (!isNaN(val) && val !== 0) {
      firstNecklaceWidth = val;
      return false;
    }
  });

  if (firstNecklaceWidth === null) {
    $(".parts-ratio-width, .parts-ratio-height, .parts-ratio-thick").text("-");
    return;
  }

  $('input[name="parts_weight[]"]').each(function (index) {
    const val = parseFloat($(this).val());
    const $ratioField = $(".parts-ratio-width").eq(index);
    $ratioField.text(
      !isNaN(val) && val !== 0 ? (val / firstNecklaceWidth).toFixed(2) : "-"
    );
  });

  $('input[name="parts_height[]"]').each(function (index) {
    const val = parseFloat($(this).val());
    const $ratioField = $(".parts-ratio-height").eq(index);
    $ratioField.text(
      !isNaN(val) && val !== 0 ? (val / firstNecklaceWidth).toFixed(2) : "-"
    );
  });

  $('input[name="parts_thick[]"]').each(function (index) {
    const val = parseFloat($(this).val());
    const $ratioField = $(".parts-ratio-thick").eq(index);
    $ratioField.text(
      !isNaN(val) && val !== 0 ? (val / firstNecklaceWidth).toFixed(2) : "-"
    );
  });
}
// ฟังก์ชันคำนวณ ratio โดยเปรียบเทียบกับค่าแรกของประเภทเดียวกัน
function calculateRatioForSameType(inputName, ratioClass) {
  // หาค่าแรกที่ไม่เป็น 0
  const $allInputs = $(`input[name="${inputName}[]"]`);
  let firstValue = null;

  $allInputs.each(function () {
    const val = parseFloat($(this).val());
    if (!isNaN(val) && val !== 0) {
      firstValue = val;
      return false; // หยุด loop เมื่อเจอค่าแรก
    }
  });

  // ถ้าไม่มีค่าแรก ให้เคลียร์ค่า ratio
  if (firstValue === null) {
    $(`.${ratioClass}`).val("");
    return;
  }

  // คำนวณ ratio สำหรับทุกรายการ
  $allInputs.each(function (index) {
    const val = parseFloat($(this).val());
    const $ratioField = $(`.${ratioClass}`).eq(index);

    if (!isNaN(val) && val !== 0) {
      const ratio = val / firstValue;
      $ratioField.val(ratio.toFixed(2));
    } else {
      $ratioField.val("");
    }
  });
}
// ฟังก์ชันสำหรับแสดง/ซ่อนฟิลด์ตามประเภท
function updateDetailPartsVisibility(row, type) {
  if (type === "สร้อย") {
    $(row).find(".necklace-parts").show();
    $(row).find(".parts-parts").hide();
    // เคลียร์ค่าในส่วนที่ซ่อน
    $(row).find(".parts-parts input").val("");
  } else if (type === "อะไหล่") {
    $(row).find(".necklace-parts").hide();
    $(row).find(".parts-parts").show();
    // เคลียร์ค่าในส่วนที่ซ่อน
    $(row).find(".necklace-parts input").val("");
  }
}

function removeDetailRow(button) {
  $(button).closest(".detail-row").remove();
  calculateTotals();
}

function showFullImage(src, title) {
  $("#fullImage").attr("src", src);
  $("#imageModalTitle").text(title);
  $("#imageModal").modal("show");
}

function savePercent() {
  // ก่อนตรวจสอบฟอร์ม ให้ลบ required จากฟิลด์ที่ซ่อนอยู่
  $(".detail-row").each(function () {
    const type = $(this).find(".type-select").val();

    if (type === "สร้อย") {
      // ถ้าเป็นประเภทสร้อย ให้ลบ required จากฟิลด์อะไหล่ที่ซ่อนอยู่
      $(this).find(".parts-parts input[required]").prop("required", false);
    } else if (type === "อะไหล่") {
      // ถ้าเป็นประเภทอะไหล่ ให้ลบ required จากฟิลด์สร้อยที่ซ่อนอยู่
      $(this).find(".necklace-parts input[required]").prop("required", false);
    } else {
      // ถ้ายังไม่ได้เลือกประเภท ให้ลบ required จากทั้งหมด
      $(this).find(".detail-parts input[required]").prop("required", false);
    }
  });

  // ตรวจสอบฟอร์ม
  const form = $("#percentForm")[0];
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  // Check weights match
  const totalWeight = parseFloat($("#total_weight").val()) || 0;
  const pnGrams = parseFloat($('input[name="pn_grams"]').val()) || 0;

  if (Math.abs(totalWeight - pnGrams) >= 0.01) {
    Swal.fire({
      icon: "error",
      title: "น้ำหนักไม่ตรงกัน",
      text: "กรุณาตรวจสอบให้น้ำหนักรวมเท่ากับน้ำหนัก (กรัม) ที่ระบุ",
    });
    return;
  }

  // If validation passes, confirm save action
  Swal.fire({
    title: "ยืนยันการบันทึก?",
    text: "คุณแน่ใจหรือไม่ว่าต้องการบันทึกข้อมูลนี้",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "ใช่, บันทึก",
    cancelButtonText: "ยกเลิก",
  }).then((result) => {
    if (result.isConfirmed) {
      // Show loading and submit form
      Swal.fire({
        title: "กำลังบันทึกข้อมูล...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
      });

      $.ajax({
        url: "actions/save_percent.php",
        type: "POST",
        data: new FormData($("#percentForm")[0]),
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (data) {
          Swal.fire({
            icon: data.success ? "success" : "error",
            title: data.success ? "บันทึกสำเร็จ" : "เกิดข้อผิดพลาด",
            text: data.success
              ? "ข้อมูลถูกบันทึกเรียบร้อยแล้ว"
              : data.message || "ไม่สามารถบันทึกข้อมูลได้",
          }).then(() => {
            if (data.success) location.reload();
          });
        },
        error: function (xhr, status, error) {
          let errorMessage = error;
          try {
            const jsonResponse = JSON.parse(xhr.responseText);
            if (jsonResponse && jsonResponse.message) {
              errorMessage = jsonResponse.message;
            }
          } catch (e) {}

          Swal.fire({
            icon: "error",
            title: "เกิดข้อผิดพลาด",
            text: errorMessage,
            footer: "หากยังพบปัญหา กรุณาติดต่อผู้ดูแลระบบ",
          });
        },
      });
    }
  });
}

function editPercent(id) {
  $.getJSON(`actions/get_percent.php?id=${id}`, function (data) {
    if (!data.success) {
      Swal.fire({
        icon: "error",
        title: "เกิดข้อผิดพลาด",
        text: data.message || "ไม่สามารถโหลดข้อมูลได้",
      });
      return;
    }

    $("#percentModal").modal("show");

    setTimeout(() => {
      // เติมข้อมูลพื้นฐาน
      $("#pn_id").val(id);
      $('input[name="pn_name"]').val(data.percent.pn_name);
      $('input[name="pn_grams"]').val(data.percent.pn_grams);
      $("#pn_baht").val((parseFloat(data.percent.pn_grams) / 15.2).toFixed(2));

      // แสดงรูปภาพ
      if (data.percent.image) {
        $("#image_preview").attr(
          "src",
          `uploads/img/percent_necklace/${data.percent.image}`
        );
        $("#image_preview_container").show();
        window.imageFileName = data.percent.image;
        window.hasExistingImage = true;
      }

      // ลบแถวเดิม
      $(".detail-row").remove();

      // กำหนดค่าให้กับรายการพิเศษ
      if (data.specialDetails?.length) {
        const cutDetail = data.specialDetails.find(
          (d) => d.pnd_name === "เผื่อตัดลาย"
        );
        const hookDetail = data.specialDetails.find(
          (d) => d.pnd_name === "ตะขอ"
        );

        if (cutDetail) {
          $('input[name="pnd_weight_special[]"]:eq(0)').val(
            cutDetail.pnd_weight_grams
          );
          $('input[name="pnd_long_special[]"]:eq(0)').val(
            cutDetail.pnd_long_inch || ""
          );
        }

        if (hookDetail) {
          $('input[name="pnd_weight_special[]"]:eq(1)').val(
            hookDetail.pnd_weight_grams
          );
          $('input[name="pnd_long_special[]"]:eq(1)').val(
            hookDetail.pnd_long_inch || ""
          );
        }
      }

      // เพิ่มข้อมูลรายละเอียด
      if (data.details?.length) {
        $.each(data.details, function (i, detail) {
          const row = addDetailRow();
          $(row).find('select[name="pnd_type[]"]').val(detail.pnd_type);
          $(row).find('input[name="pnd_name[]"]').val(detail.pnd_name);
          $(row)
            .find('input[name="pnd_weight_grams[]"]')
            .val(detail.pnd_weight_grams);
          $(row)
            .find('input[name="pnd_long_inch[]"]')
            .val(detail.pnd_long_inch || "");

          // เพิ่มการโหลดข้อมูล necklace_detail_parts ถ้ามี
          if (detail.parts) {
            // ข้อมูล ndp_id
            $(row)
              .find('input[name="ndp_id[]"]')
              .val(detail.parts.ndp_id || "");

            if (detail.pnd_type === "สร้อย") {
              $(row)
                .find('input[name="wire_hole[]"]')
                .val(detail.parts.wire_hole || "");
              $(row)
                .find('input[name="wire_thick[]"]')
                .val(detail.parts.wire_thick || "");
              $(row)
                .find('input[name="wire_core[]"]')
                .val(detail.parts.wire_core || "");
              $(row)
                .find('input[name="scale_wire_weight[]"]')
                .val(detail.parts.scale_wire_weight || "");
              $(row)
                .find('input[name="scale_wire_thick[]"]')
                .val(detail.parts.scale_wire_thick || "");
            } else if (detail.pnd_type === "อะไหล่") {
              $(row)
                .find('input[name="parts_weight[]"]')
                .val(detail.parts.parts_weight || "");
              $(row)
                .find('input[name="parts_height[]"]')
                .val(detail.parts.parts_height || "");
              $(row)
                .find('input[name="parts_thick[]"]')
                .val(detail.parts.parts_thick || "");
            }
          }

          // อัพเดทการแสดงตามประเภท
          updateDetailPartsVisibility(row, detail.pnd_type);
        });
      }

      calculateTotals();
      calculateRatios(); // เพิ่มการคำนวณ ratio หลังจากโหลดข้อมูล
    }, 200);
  }).fail(function (jqXHR, textStatus, errorThrown) {
    Swal.fire({
      icon: "error",
      title: "เกิดข้อผิดพลาด",
      text: errorThrown,
    });
  });
}

function deletePercent(id) {
  Swal.fire({
    title: "ยืนยันการลบ?",
    text: "คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลนี้ ข้อมูลที่ถูกลบไม่สามารถกู้คืนได้",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "ใช่, ลบข้อมูล",
    cancelButtonText: "ยกเลิก",
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: "กำลังลบข้อมูล...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
      });

      $.getJSON(`actions/delete_percent.php?id=${id}`)
        .done(function (data) {
          Swal.fire({
            icon: data.success ? "success" : "error",
            title: data.success ? "ลบสำเร็จ" : "เกิดข้อผิดพลาด",
            text: data.success
              ? "ข้อมูลถูกลบเรียบร้อยแล้ว"
              : data.message || "ไม่สามารถลบข้อมูลได้",
          }).then(() => {
            if (data.success) location.reload();
          });
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
          Swal.fire({
            icon: "error",
            title: "เกิดข้อผิดพลาด",
            text: errorThrown,
            footer: "หากยังพบปัญหา กรุณาติดต่อผู้ดูแลระบบ",
          });
        });
    }
  });
}

function viewPercent(id) {
  $.getJSON(`actions/get_percent.php?id=${id}`, function (data) {
    if (!data.success) {
      Swal.fire({
        icon: "error",
        title: "เกิดข้อผิดพลาด",
        text: data.message || "ไม่สามารถโหลดข้อมูลได้",
      });
      return;
    }

    let html = `
    <div class="row mb-3">
        <div class="col-md-4 text-center">
            ${
              data.percent.image
                ? `<img src="uploads/img/percent_necklace/${data.percent.image}" class="img-thumbnail mb-2" style="max-height:150px;">`
                : '<i class="fas fa-image fa-4x text-muted"></i>'
            }
        </div>
        <div class="col-md-8">
            <h5>${data.percent.pn_name}</h5>
            <div>น้ำหนัก (กรัม): <b>${data.percent.pn_grams}</b></div>
            <div>บาท: <b>${(parseFloat(data.percent.pn_grams) / 15.2).toFixed(
              2
            )}</b></div>
        </div>
    </div>
    <hr>
    <h6>รายละเอียด</h6>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ประเภท</th>
                    <th>ชื่อ</th>
                    <th>น้ำหนัก (กรัม)</th>
                    <th>%</th>
                    <th>ความยาว</th>
                </tr>
            </thead>
            <tbody>`;

    // Calculate total weight for percentage calculation
    let totalWeight = 0;
    if (data.specialDetails?.length) {
      data.specialDetails.forEach(
        (d) => (totalWeight += parseFloat(d.pnd_weight_grams) || 0)
      );
    }
    if (data.details?.length) {
      data.details.forEach(
        (d) => (totalWeight += parseFloat(d.pnd_weight_grams) || 0)
      );
    }

    // รายการพิเศษ (special items)
    if (data.specialDetails?.length) {
      data.specialDetails.forEach(function (d) {
        const percent = (
          ((parseFloat(d.pnd_weight_grams) || 0) / totalWeight) *
          100
        ).toFixed(2);
        html += `<tr class="table-light">
            <td><span class="badge bg-secondary">พิเศษ</span></td>
            <td>${d.pnd_name}</td>
            <td>${d.pnd_weight_grams}</td>
            <td>${percent}%</td>
            <td>${d.pnd_long_inch || "-"}</td>
        </tr>`;
      });
    }

    // รายการปกติ (regular items)
    if (data.details?.length) {
      data.details.forEach(function (d) {
        const percent = (
          ((parseFloat(d.pnd_weight_grams) || 0) / totalWeight) *
          100
        ).toFixed(2);
        html += `<tr>
            <td>${d.pnd_type || "-"}</td>
            <td>${d.pnd_name}</td>
            <td>${d.pnd_weight_grams}</td>
            <td>${percent}%</td>
            <td>${d.pnd_long_inch || "-"}</td>
        </tr>`;
      });
    }

    // รายการรวม (total row)
    html += `<tr class="table-primary">
    <td colspan="2" class="text-end fw-bold">รวม</td>
    <td class="fw-bold">${totalWeight.toFixed(2)}</td>
    <td class="fw-bold">100%</td>
    <td></td>
    </tr>`;

    html += `</tbody></table></div>`;

    // ค้นหาค่า reference สำหรับการคำนวณ ratio
    let referenceWidth = null;
    if (data.details?.length) {
      // หาค่ากว้างแรกของสร้อย
      for (const d of data.details) {
        if (d.pnd_type === "สร้อย" && d.parts?.scale_wire_weight) {
          referenceWidth = parseFloat(d.parts.scale_wire_weight);
          break;
        }
      }
    }

    // Display ratio information
    if (data.details?.some((d) => d.parts)) {
      html += `<h6 class="mt-4">ข้อมูลสัดส่วน</h6>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ชื่อ</th>
                        <th>ประเภท</th>
                        <th>ขนาด</th>
                        <th>Ratio</th>
                    </tr>
                </thead>
                <tbody>`;

      // First show necklace items
      const necklaceItems = data.details.filter(
        (d) => d.pnd_type === "สร้อย" && d.parts
      );
      if (necklaceItems.length > 0) {
        html += `<tr class="table-secondary"><td colspan="4" class="fw-bold">สร้อย</td></tr>`;

        necklaceItems.forEach(function (d) {
          const wireWidth = parseFloat(d.parts.scale_wire_weight) || 0;
          const wireThick = parseFloat(d.parts.scale_wire_thick) || 0;

          html += `<tr>
                <td>${d.pnd_name}</td>
                <td>สร้อย</td>
                <td>
                    <div>รูลวด: ${d.parts.wire_hole || "-"}</div>
                    <div>หนา: ${d.parts.wire_thick || "-"}</div>
                    <div>ไส้: ${d.parts.wire_core || "-"}</div>
                    <div>กว้าง: ${d.parts.scale_wire_weight || "-"} มม.</div>
                    <div>หนา: ${d.parts.scale_wire_thick || "-"} มม.</div>
                </td>
                <td></td>
            </tr>`;
        });
      }

      // Then show parts items
      const partsItems = data.details.filter(
        (d) => d.pnd_type === "อะไหล่" && d.parts
      );
      if (partsItems.length > 0) {
        html += `<tr class="table-secondary"><td colspan="4" class="fw-bold">อะไหล่</td></tr>`;

        partsItems.forEach(function (d) {
          const partsWidth = parseFloat(d.parts.parts_weight) || 0;
          const partsHeight = parseFloat(d.parts.parts_height) || 0;
          const partsThick = parseFloat(d.parts.parts_thick) || 0;

          let ratioInfo = "";
          if (referenceWidth && partsWidth) {
            const widthRatio = (partsWidth / referenceWidth).toFixed(2);
            ratioInfo += `<div>กว้าง: ${widthRatio}</div>`;
          }

          if (referenceWidth && partsHeight) {
            const heightRatio = (partsHeight / referenceWidth).toFixed(2);
            ratioInfo += `<div>สูง: ${heightRatio}</div>`;
          }

          if (referenceWidth && partsThick) {
            const thickRatio = (partsThick / referenceWidth).toFixed(2);
            ratioInfo += `<div>หนา: ${thickRatio}</div>`;
          }

          html += `<tr>
                <td>${d.pnd_name}</td>
                <td>อะไหล่</td>
                <td>
                    <div>กว้าง: ${d.parts.parts_weight || "-"} มม.</div>
                    <div>สูง: ${d.parts.parts_height || "-"} มม.</div>
                    <div>หนา: ${d.parts.parts_thick || "-"} มม.</div>
                </td>
                <td>${ratioInfo || "-"}</td>
            </tr>`;
        });
      }

      html += `</tbody></table></div>`;
    }

    let firstNecklaceWidth = null;
    if (data.details?.length) {
      for (const detail of data.details) {
        if (detail.pnd_type === "สร้อย" && detail.parts?.scale_wire_weight) {
          firstNecklaceWidth = parseFloat(detail.parts.scale_wire_weight);
          break;
        }
      }
    }

    // สร้างลิงค์พร้อมพารามิเตอร์ทั้งหมด
    let calculationUrl = `percent_necklace.php?pn_id=${id}&baht=${(
      parseFloat(data.percent.pn_grams) / 15.2
    ).toFixed(2)}&grams=${data.percent.pn_grams}`;

    // เพิ่มพารามิเตอร์ความกว้างถ้ามีค่า
    if (firstNecklaceWidth !== null) {
      calculationUrl += `&width=${firstNecklaceWidth}`;
    }

    $("#percentViewFooter").html(`
        <a href="${calculationUrl}" class="btn btn-success">
            <i class="fas fa-calculator"></i> ไปหน้าคำนวน
        </a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
    `);
    $("#percentViewContent").html(html);
    $("#percentViewModal").modal("show");
  }).fail(function (jqXHR, textStatus, errorThrown) {
    Swal.fire({
      icon: "error",
      title: "เกิดข้อผิดพลาด",
      text: errorThrown || "ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้",
    });
  });
}
