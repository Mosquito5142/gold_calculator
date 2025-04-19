function saveNecklace() {
  const form = document.getElementById("necklaceForm");

  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  const formData = new FormData(form);
  sendRequest("actions/save_necklace.php", formData, "บันทึกสำเร็จ");
}

function editNecklace(id) {
  fetch(`actions/get_necklace.php?id=${id}`)
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const form = document.getElementById("necklaceForm");
        document.getElementById("necklace_detail_id").value = id;

        // Fill form with necklace data
        Object.keys(data.necklace).forEach((key) => {
          const input = form.elements[key];
          if (input) input.value = data.necklace[key];
        });

        // คำนวณน้ำหนักสร้อย(ก่อนสกัด) ย้อนกลับ
        if (data.necklace.true_weight && data.necklace.ptt_ratio) {
          const trueWeight = parseFloat(data.necklace.true_weight);
          const pttRatio = parseFloat(data.necklace.ptt_ratio);
          if (!isNaN(trueWeight) && !isNaN(pttRatio)) {
            form.elements["weight_ture"].value = (
              trueWeight * pttRatio
            ).toFixed(2);
          } else {
            form.elements["weight_ture"].value = trueWeight;
          }
        }
        // แสดงรูปภาพถ้ามี
        const preview = document.getElementById("preview");
        if (data.necklace.image) {
          // เปลี่ยนเป็น data.necklace.image
          preview.src = "uploads/img/necklace_detail/" + data.necklace.image;
          preview.style.display = "block";
        } else {
          preview.style.display = "none";
        }

        // Trigger change events
        $('select[name="type"]').trigger("change");
        $('select[name="shapeshape_necklace"]').trigger("change");

        // Show modal
        const modal = new bootstrap.Modal(
          document.getElementById("necklaceModal")
        );
        modal.show();
      } else {
        Swal.fire({
          icon: "error",
          title: "เกิดข้อผิดพลาด",
          text: data.message || "ไม่สามารถโหลดข้อมูลได้",
        });
      }
    })
    .catch((error) => handleError(error));
}
function handleError(error) {
  console.error("Error:", error);
  Swal.fire({
    icon: "error",
    title: "เกิดข้อผิดพลาด",
    text: error.message || "ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์",
  });
}
function deleteNecklace(id) {
  Swal.fire({
    title: "ยืนยันการลบ",
    text: "คุณต้องการลบข้อมูลนี้ใช่หรือไม่?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "ใช่, ลบ",
    cancelButtonText: "ยกเลิก",
  }).then((result) => {
    if (result.isConfirmed) {
      sendRequest(`actions/delete_necklace.php?id=${id}`, null, "ลบสำเร็จ");
    }
  });
}

function sendRequest(url, data, successMessage) {
  const options = {
    method: data ? "POST" : "GET",
    body: data,
  };

  fetch(url, options)
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        Swal.fire({
          icon: "success",
          title: successMessage,
          text: "ดำเนินการเรียบร้อยแล้ว",
        }).then(() => location.reload());
      } else {
        throw new Error(data.message);
      }
    })
    .catch(handleError);
}
