"use strict";

console.log("Sticky Note JS Loaded");

let noteZindex = 1;
let saveTimeout;

// ฟังก์ชันบันทึกโน้ต
function saveNote($note) {
    let id = $note.data("id");
    const title = $note.find(".title").val().trim();
    const content = $note.find(".cnt").val().trim();
    const posX = parseInt($note.css("left")) || 0;
    const posY = parseInt($note.css("top")) || 0;
    const zIndex = parseInt($note.css("z-index")) || ++noteZindex;

    console.log("Saving note:", { id, title, content, posX, posY, zIndex });

    if (!id || id === 'new') {
        id = 'new';
    }

    $.post('api/notes.php', {
        action: 'save',
        id: id,
        title: title,
        content: content,
        pos_x: posX,
        pos_y: posY,
        z_index: zIndex
    }, function (response) {
        console.log("Response from API:", response);
        try {
            const jsonResponse = JSON.parse(response);
            if (jsonResponse.error) {
                Swal.fire('ข้อผิดพลาด', jsonResponse.error, 'error');
            } else {
                if (id === 'new') {
                    $note.attr("data-id", jsonResponse);
                    $note.data("id", jsonResponse);
                }
            }
        } catch (e) {
            Swal.fire('ข้อผิดพลาด', 'การตอบกลับจากเซิร์ฟเวอร์ไม่ถูกต้อง', 'error');
        }
    }).fail(function () {
        Swal.fire('ข้อผิดพลาด', 'ไม่สามารถบันทึกโน้ตได้', 'error');
    });
}


// ฟังก์ชันสร้างโน้ต
function createNote(id = 'new', title = '', content = '', posX = 0, posY = 0, zIndex = 1, createdTime = '-', lastEditedTime = '-') {
    console.log("Creating note:", { id, title, content, posX, posY, zIndex, createdTime, lastEditedTime });

    const noteTemplate = `
        <div class="note" data-id="${id}">
            <a href="javascript:;" class="button remove">X</a>
            <div class="note_cnt">
                <textarea class="title" placeholder="กรอกหัวข้อโน้ต"></textarea>
                <textarea class="cnt" placeholder="กรอกรายละเอียดโน้ตที่นี่"></textarea>
                <div class="note_timestamps">
                    <small>แก้ไขล่าสุด: <span class="last-edited-time">${lastEditedTime}</span></small>
                </div>
            </div>
        </div>
    `;

    const $note = $(noteTemplate).hide().appendTo("#board").show("fade", 300);
    $note.find(".title").val(title);
    $note.find(".cnt").val(content);
    $note.css({ top: posY + 'px', left: posX + 'px', "z-index": zIndex });
    $note.data("id", id);

    $note.draggable({
        stop: function () {
            saveNote($note);
        }
    });

    $note.find(".remove").click(function () {
        confirmDelete($note);
    });

    if (id === 'new') {
        saveNote($note);
    }
}


// ฟังก์ชันยืนยันการลบโน้ตด้วย SweetAlert
function confirmDelete($note) {
    Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: "คุณจะไม่สามารถกู้คืนโน้ตนี้ได้!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ใช่, ลบเลย!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteNote($note);
            Swal.fire('ลบแล้ว!', 'โน้ตของคุณถูกลบเรียบร้อยแล้ว', 'success');
        }
    });
}

// ฟังก์ชันลบโน้ต
function deleteNote($note) {
    const id = $note.data("id");
    $note.hide("puff", { percent: 133 }, 250, function () {
        $(this).remove();
    });
    if (id !== 'new') {
        $.post('api/notes.php', { action: 'delete', id: id });
    }
}

// ฟังก์ชันดึงข้อมูลโน้ต
function fetchNotes() {
    console.log("Fetching notes...");
    $.post('api/notes.php', { action: 'fetch' }, function (data) {
        console.log("Data fetched:", data);
        try {
            const notes = JSON.parse(data);
            notes.forEach(note => {
                createNote(
                    note.id,
                    note.title,
                    note.content,
                    parseInt(note.pos_x),
                    parseInt(note.pos_y),
                    parseInt(note.z_index),
                    note.created_at || '-',
                    note.updated_at || '-'
                );
            });
        } catch (e) {
            console.error("Error parsing fetched data:", e);
            Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดโน้ตได้', 'error');
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.error("AJAX Error in fetchNotes:", textStatus, errorThrown);
        Swal.fire('ข้อผิดพลาด', 'ไม่สามารถดึงข้อมูลโน้ตได้', 'error');
    });
}



// เมื่อเริ่มต้น
$(document).ready(function () {
    console.log("Document ready.");

    $("#add_new").click(function () {
        createNote('new', '', '');
    });

    fetchNotes();

    $("body").on("input", "textarea", function () {
        const $note = $(this).closest(".note");
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => {
            console.log("Input detected, saving note:", $note);
            saveNote($note);
        }, 3000);
    });
});
