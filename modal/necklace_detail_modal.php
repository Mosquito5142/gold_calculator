            <!-- Modal -->
            <div class="modal fade" id="necklaceModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">จัดการข้อมูลสร้อย</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="necklaceForm">
                                <input type="hidden" id="necklace_detail_id" name="necklace_detail_id">

                                <!-- Necklace Details -->
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">ชื่อลาย</label>
                                        <input type="text" class="form-control" name="name" required>
                                    </div>

                                </div>

                                <!-- PTT Section -->
                                <div class="row mb-3">
                                    <h6>ข้อมูล สร้อยต้นแบบ</h6>
                                    <div class="col-md-12 mb-2">
                                        <label class="form-label">ประเภท</label>
                                        <select class="form-select" name="type" required>
                                            <option value="" disabled selected>เลือก</option>
                                            <option value="ตัน">ตัน</option>
                                            <option value="โปร่ง">โปร่ง</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">หนา</label>
                                        <input type="number" class="form-control" name="ptt_thick" step="0.01" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">ไส้</label>
                                        <input type="number" class="form-control" name="ptt_core" step="0.01" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="ptt_ratio" class="form-label">อัตราส่วน</label>
                                        <input type="number" class="form-control" id="ptt_ratio" name="ptt_ratio" step="0.01" required>
                                    </div>
                                </div>

                                <!-- AGPT Section -->
                                <div class="row mb-3">
                                    <h6>ข้อมูล ลวดอกาโฟโต้(ยังไม่สกัด)</h6>
                                    <div class="col-md-4">
                                        <label class="form-label">รูลวด</label>
                                        <input type="number" class="form-control" name="agpt_thick" step="0.01" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">น้ำหนักลวด(ก่อนสกัด)</label>
                                        <input type="number" class="form-control" name="agpt_core" step="0.01" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">ความยาวลวด (นิ้ว)</label>
                                        <input type="number" class="form-control" name="agpt_ratio" step="0.01" required>
                                    </div>
                                </div>

                                <!-- True Measurements -->
                                <div class="row mb-3">
                                    <h6>สร้อยต้นแบบ</h6>
                                    <div class="col-md-4">
                                        <label class="form-label">ความยาว (นิ้ว)</label>
                                        <input type="number" class="form-control" name="true_length" step="0.01" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="weight_ture" class="form-label">น้ำหนักสร้อย(ก่อนสกัด)</label>
                                        <input type="number" class="form-control" id="weight_ture" name="weight_ture" step="0.01" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="true_weight" class="form-label">น้ำหนัก(คาดการณ์ หลังสกัด)</label>
                                        <input type="number" class="form-control" id="true_weight" name="true_weight" step="0.01" readonly>
                                    </div>
                                </div>

                                <!-- Proportions Section -->
                                <div class="row mb-3">
                                    <h6>สัดส่วนสร้อย</h6>
                                    <div class="col-md-3">
                                        <label class="form-label">รูลวด</label>
                                        <input type="number" class="form-control" name="proportions_size" step="0.01" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">รูปร่างสร้อย</label>
                                        <select class="form-select" name="shapeshape_necklace" required>
                                            <option value="" selected>เลือก</option>
                                            <option value="สี่เหลี่ยม">สี่เหลี่ยม</option>
                                            <option value="วงกลม">วงกลม</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">หน้ากว้าง(มม.)</label>
                                        <input type="number" class="form-control" name="proportions_width" step="0.01" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">หนา(มม.)</label>
                                        <input type="number" class="form-control" name="proportions_thick" step="0.01" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <label class="form-label">หมายเหตุ</label>
                                        <textarea class="form-control" name="comment" rows="3"></textarea>
                                    </div>
                                </div>
                                <!-- เพิ่มในส่วน form -->
                                <div class="form-group">
                                    <label>รูปภาพ</label>
                                    <div id="image_preview" class="mt-2">
                                        <img id="preview" src="" style="max-width: 200px; display: none;">
                                    </div>
                                    <input type="file" class="form-control mt-2" id="necklace_image" name="necklace_image" accept="image/*">
                                </div>
                            </form>
                            <div class="modal-footer d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                <button type="submit" class="btn btn-primary" form="necklaceForm">บันทึก</button>
                            </div>
                        </div>
                    </div>
                </div>