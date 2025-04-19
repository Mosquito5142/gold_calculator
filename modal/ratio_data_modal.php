<!-- Add Ratio Modal -->
<div class="modal fade" id="addRatioModal" tabindex="-1" aria-labelledby="addRatioModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRatioModalLabel">เพิ่มอัตราส่วน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addRatioForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ratio_thick" class="form-label">หนา</label>
                            <input type="text" class="form-control" id="ratio_thick" name="ratio_thick" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ratio_data" class="form-label">อัตราส่วน</label>
                            <input type="number" step="0.01" class="form-control" id="ratio_data" name="ratio_data" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ratio_size" class="form-label">รูลวด</label>
                            <input type="number" step="0.01" class="form-control" id="ratio_size" name="ratio_size" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ratio_gram" class="form-label">นน.ลวดก่อนสกัด (กรัม)</label>
                            <input type="number" step="0.01" class="form-control" id="ratio_gram" name="ratio_gram" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ratio_inch" class="form-label">ค.ยาวลวด (นิ้ว)</label>
                            <input type="number" step="0.01" class="form-control" id="ratio_inch" name="ratio_inch" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary " data-bs-dismiss="modal">ปิด</button>
                        <button type="submit" class="btn btn-primary ms-2">เพิ่มอัตราส่วน</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Ratio Modal -->
<div class="modal fade" id="editRatioModal" tabindex="-1" aria-labelledby="editRatioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRatioModalLabel">แก้ไขอัตราส่วน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editRatioForm">
                    <input type="hidden" id="edit_ratio_id" name="ratio_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_ratio_thick" class="form-label">หนา</label>
                            <input type="text" class="form-control" id="edit_ratio_thick" name="ratio_thick" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_ratio_data" class="form-label">อัตราส่วน</label>
                            <input type="number" step="0.01" class="form-control" id="edit_ratio_data" name="ratio_data" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_ratio_size" class="form-label">รูลวด</label>
                            <input type="number" step="0.01" class="form-control" id="edit_ratio_size" name="ratio_size" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_ratio_gram" class="form-label">นน.ลวดก่อนสกัด (กรัม)</label>
                            <input type="number" step="0.01" class="form-control" id="edit_ratio_gram" name="ratio_gram" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_ratio_inch" class="form-label">ค.ยาวลวด (นิ้ว)</label>
                            <input type="number" step="0.01" class="form-control" id="edit_ratio_inch" name="ratio_inch" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                        <button type="submit" class="btn btn-primary ms-2">บันทึกการเปลี่ยนแปลง</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Ratio Modal -->
<div class="modal fade" id="deleteRatioModal" tabindex="-1" aria-labelledby="deleteRatioModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteRatioModalLabel">ลบอัตราส่วน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p>คุณแน่ใจหรือไม่ว่าต้องการลบอัตราส่วนนี้?</p>
                <form id="deleteRatioForm">
                    <input type="hidden" id="delete_ratio_id" name="ratio_id">
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-danger me-2">ลบ</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>