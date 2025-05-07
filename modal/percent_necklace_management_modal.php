    <!-- Modal -->
    <div class="modal fade" id="percentModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">จัดการข้อมูล % สร้อย</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="percentForm" enctype="multipart/form-data">
                        <input type="hidden" name="pn_id" id="pn_id">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">ชื่อ</label>
                                <input type="text" class="form-control" name="pn_name" required style="background-color: #fff9c4;">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">บาท</label>
                                <input type="number" class="form-control" id="pn_baht" step="0.01" style="background-color: #fff9c4;">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">น้ำหนัก (กรัม)</label>
                                <input type="number" class="form-control" name="pn_grams" id="pn_grams" step="0.01" required style="background-color: #fff9c4;">
                            </div>
                        </div>

                        <!-- เพิ่มส่วนของการอัปโหลดรูปภาพ -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label">รูปภาพ</label>
                                <input type="file" class="form-control" name="image" id="image" accept="image/jpeg,image/png,image/webp">
                            </div>
                            <div class="col-md-4">
                                <div id="image_preview_container" class="mt-2 text-center" style="display: none;">
                                    <img id="image_preview" class="img-thumbnail mb-2" style="max-height: 150px;">
                                </div>
                            </div>
                        </div>
                        <hr class="mb-1">
                        <div class="mb-3">
                            <h6>รายละเอียด</h6>

                            <div id="detailsContainer">
                                <!-- แถวรวม -->
                                <div class="row">
                                    <div class="col-md-5">
                                        <label class="form-label">ชื่อ</label>
                                        <input type="text" class="form-control" value="ทั้งหมด" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">น้ำหนัก</label>
                                        <input type="number" id="total_weight" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">%</label>
                                        <input type="number" id="total_percent" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">ความยาว (นิ้ว)</label>
                                        <input type="number" id="total_length" class="form-control" readonly>
                                    </div>
                                </div>
                                <hr class="mb-1">
                                <!-- แถวเผื่อตัดลาย -->
                                <div class="row mt-1">
                                    <input type="hidden" name="pnd_type_special[]" value="">
                                    <div class="col-md-5">
                                        <label class="form-label">ชื่อ <span style="color: red;">(* เลขติดลบ)</span></label>
                                        <input type="text" class="form-control" name="pnd_name_special[]" value="เผื่อตัดลาย" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">น้ำหนัก (กรัม)</label>
                                        <input type="number" id="cut_weight" class="form-control" name="pnd_weight_special[]" step="0.01" max="0" style="background-color: #fff9c4; --placeholder-color: red;" placeholder="เลขติดลบ" value=0 required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">%</label>
                                        <input type="number" id="cut_percent" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">ความยาว (นิ้ว)</label>
                                        <input type="number" id="cut_length" class="form-control" name="pnd_long_special[]" step="0.01" style="background-color: #fff9c4;">
                                    </div>
                                </div>
                                <hr class="mb-1">
                                <!-- แถวตะขอ -->
                                <div class="row mt-1">
                                    <input type="hidden" name="pnd_type_special[]" value="">
                                    <div class="col-md-5">
                                        <label class="form-label">ชื่อ</label>
                                        <input type="text" class="form-control" name="pnd_name_special[]" value="ตะขอ" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">น้ำหนัก (กรัม)</label>
                                        <input type="number" id="hook_weight" class="form-control" name="pnd_weight_special[]" step="0.01" style="background-color: #fff9c4;" value=0 required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">%</label>
                                        <input type="number" id="hook_percent" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">ความยาว (นิ้ว)</label>
                                        <input type="number" id="hook_length" class="form-control" name="pnd_long_special[]" step="0.01" style="background-color: #fff9c4;">
                                    </div>
                                </div>
                                <hr class="mb-1">
                            </div>
                            <button type="button" class="btn btn-success btn-sm mt-1" onclick="addDetailRow()">
                                <i class="fas fa-plus"></i> เพิ่มรายการ
                            </button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-primary" onclick="savePercent()" data-bs-toggle="tooltip" data-bs-placement="top" title="น้ำหนักสร้อยและน้ำหนักรวมชิ้นส่วนต้องเท่ากันเพื่อบันทึก">
                        บันทึก
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal แสดงรูปเต็ม -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img id="fullImage" class="img-fluid" src="">
                </div>
            </div>
        </div>
    </div>
    <!-- Modal แสดงรายละเอียด -->
    <div class="modal fade" id="percentViewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">รายละเอียด % สร้อย</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="percentViewContent">
                    <!-- Content will be loaded dynamically here -->
                </div>
                <div class="modal-footer d-flex justify-content-end" id="percentViewFooter">
                    <!-- ปุ่มจะถูกเพิ่มโดย JavaScript -->
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>