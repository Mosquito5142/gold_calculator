$(document).ready(function () {
  // กำหนดค่าเริ่มต้นสำหรับ Select2
  $("#share_users").select2({
    dropdownParent: $("#ShareModal"),
    placeholder: "เลือกผู้ใช้งาน",
    width: "100%",
  });

  // เมื่อคลิกปุ่มแชร์ที่รายการสร้อย
  $(document).on("click", ".share-btn", function () {
    const necklaceId = $(this).data("id");
    const necklaceName = $(this).data("name");

    // กำหนดค่าให้กับ modal
    $("#share_necklace_id").val(necklaceId);
    $("#share_necklace_name").text(necklaceName);

    // โหลดข้อมูลผู้ใช้
    loadUsersList();

    // โหลดข้อมูลการแชร์ปัจจุบัน
    loadCurrentSharing(necklaceId);

    // แสดง modal
    $("#ShareModal").modal("show");
  });

  // เมื่อคลิกปุ่มบันทึกการแชร์
  $("#btn_save_share").click(function () {
    saveSharing();
  });

  // เมื่อคลิกปุ่มลบการแชร์
  $(document).on("click", ".delete-sharing", function () {
    const sharingId = $(this).data("sharing-id");
    deleteSharing(sharingId);
  });
});

// ฟังก์ชันโหลดรายชื่อผู้ใช้
function loadUsersList() {
  $.ajax({
    url: "actions/get_users_list.php",
    type: "GET",
    dataType: "json",
    success: function (response) {
      if (response.success) {
        const select = $("#share_users");
        select.empty();

        // เพิ่มตัวเลือกผู้ใช้
        response.users.forEach((user) => {
          const option = new Option(
            `${user.first_name} ${user.last_name} (${user.users_depart})`,
            user.users_id,
            false,
            false
          );
          select.append(option);
        });

        select.trigger("change");
      } else {
        showError("ไม่สามารถโหลดรายชื่อผู้ใช้ได้");
      }
    },
    error: function (xhr, status, error) {
      showError("เกิดข้อผิดพลาดในการโหลดรายชื่อผู้ใช้: " + error);
    },
  });
}

// ฟังก์ชันโหลดข้อมูลการแชร์ปัจจุบัน
function loadCurrentSharing(necklaceId) {
  $.ajax({
    url: "actions/get_current_sharing.php",
    type: "GET",
    data: {
      necklace_id: necklaceId,
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        const tableBody = $("#currentSharingTable tbody");
        tableBody.empty();

        if (response.sharing.length > 0) {
          // เพิ่มข้อมูลผู้ใช้ที่มีสิทธิ์
          response.sharing.forEach((item) => {
            // ตรวจสอบว่ามีข้อมูลชื่อผู้แชร์หรือไม่
            const sharerName =
              item.sharer_first_name && item.sharer_last_name
                ? `${item.sharer_first_name} ${item.sharer_last_name}`
                : "ไม่ระบุ";

            // จัดรูปแบบวันที่และเวลา
            let formattedDate = "ไม่ระบุ";
            if (item.updated_at) {
              const dateObj = new Date(item.updated_at);
              if (!isNaN(dateObj.getTime())) {
                const hours = String(dateObj.getHours()).padStart(2, "0");
                const minutes = String(dateObj.getMinutes()).padStart(2, "0");
                const seconds = String(dateObj.getSeconds()).padStart(2, "0");
                const day = String(dateObj.getDate()).padStart(2, "0");
                const month = String(dateObj.getMonth() + 1).padStart(2, "0");
                const year = dateObj.getFullYear();

                formattedDate = `${hours}:${minutes}:${seconds} ${day}/${month}/${year}`;
              }
            }

            tableBody.append(`
                  <tr class="text-center">
                    <td>${item.first_name} ${item.last_name}</td>
                    <td>${item.users_depart}</td>
                    <td>${sharerName}</td>
                    <td>${formattedDate}</td>
                    <td class="text-center">
                      <button type="button" class="btn btn-danger btn-sm delete-sharing" data-sharing-id="${item.sharing_id}">
                        <i class="fas fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                `);
          });
        } else {
          // ถ้าไม่มีข้อมูลการแชร์
          tableBody.append(
            '<tr><td colspan="5" class="text-center">ยังไม่มีการแชร์ข้อมูล</td></tr>'
          );
        }
      } else {
        showError("ไม่สามารถโหลดข้อมูลการแชร์ได้");
      }
    },
    error: function (xhr, status, error) {
      showError("เกิดข้อผิดพลาดในการโหลดข้อมูลการแชร์: " + error);
    },
  });
}

// ฟังก์ชันบันทึกการแชร์
function saveSharing() {
  const necklaceId = $("#share_necklace_id").val();
  const selectedUsers = $("#share_users").val();

  if (!selectedUsers || selectedUsers.length === 0) {
    showError("กรุณาเลือกผู้ใช้ที่ต้องการแชร์");
    return;
  }

  $.ajax({
    url: "actions/save_sharing.php",
    type: "POST",
    data: {
      necklace_id: necklaceId,
      users_id: selectedUsers,
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        Swal.fire({
          icon: "success",
          title: "บันทึกสำเร็จ",
          text: "บันทึกการแชร์เรียบร้อยแล้ว",
        }).then(() => {
          // โหลดข้อมูลการแชร์ใหม่
          loadCurrentSharing(necklaceId);
          // รีเซ็ตการเลือกผู้ใช้
          $("#share_users").val(null).trigger("change");
        });
      } else {
        showError(response.message || "ไม่สามารถบันทึกการแชร์ได้");
      }
    },
    error: function (xhr, status, error) {
      showError("เกิดข้อผิดพลาดในการบันทึกการแชร์: " + error);
    },
  });
}

// ฟังก์ชันลบการแชร์
function deleteSharing(sharingId) {
  Swal.fire({
    title: "ยืนยันการลบ",
    text: "คุณต้องการยกเลิกการแชร์นี้หรือไม่?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "ใช่, ลบ",
    cancelButtonText: "ยกเลิก",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "actions/delete_sharing.php",
        type: "POST",
        data: {
          sharing_id: sharingId,
        },
        dataType: "json",
        success: function (response) {
          if (response.success) {
            // โหลดข้อมูลการแชร์ใหม่
            loadCurrentSharing($("#share_necklace_id").val());

            Swal.fire({
              icon: "success",
              title: "ลบสำเร็จ",
              text: "ลบการแชร์เรียบร้อยแล้ว",
            });
          } else {
            showError(response.message || "ไม่สามารถลบการแชร์ได้");
          }
        },
        error: function (xhr, status, error) {
          showError("เกิดข้อผิดพลาดในการลบการแชร์: " + error);
        },
      });
    }
  });
}

// แสดงข้อผิดพลาด
function showError(message) {
  Swal.fire({
    icon: "error",
    title: "เกิดข้อผิดพลาด",
    text: message,
  });
}
