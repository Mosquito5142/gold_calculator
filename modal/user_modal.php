<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">เพิ่มผู้ใช้งาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">ชื่อ</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">นามสกุล</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="users_level" class="form-label">ระดับ</label>
                            <select class="form-control" id="users_level" name="users_level" required>
                                <option value="User">User</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="users_depart" class="form-label">สถานะ</label>
                            <select class="form-control" id="users_depart" name="users_depart" required>
                                <option value="">เลือกแผนก</option>
                                <option value="SG">SG</option>
                                <option value="บ้านช่าง">บ้านช่าง</option>
                                <option value="YS">YS</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="users_status" class="form-label">สถานะ</label>
                            <select class="form-control" id="users_status" name="users_status" required>
                                <option value="Enable">เปิดใช้งาน</option>
                                <option value="Disable">ปิดใช้งาน</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="username" class="form-label">ชื่อผู้ใช้</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">รหัสผ่าน</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirm_password" class="form-label">ยืนยันรหัสผ่าน</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary">เพิ่มผู้ใช้งาน</button>
                        <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">ปิด</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">แก้ไขผู้ใช้งาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="edit_users_id" name="users_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_first_name" class="form-label">ชื่อ</label>
                            <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_last_name" class="form-label">นามสกุล</label>
                            <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label for="edit_users_level" class="form-label">ระดับ</label>
                            <select class="form-control" id="edit_users_level" name="users_level" required>
                                <option value="User">User</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_users_depart" class="form-label">แผนก</label>
                            <select class="form-control" id="edit_users_depart" name="users_depart" required>
                                <option value="SG">SG</option>
                                <option value="บ้านช่าง">บ้านช่าง</option>
                                <option value="YS">YS</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_users_status" class="form-label">สถานะ</label>
                            <select class="form-control" id="edit_users_status" name="users_status" required>
                                <option value="Enable">เปิดใช้งาน</option>
                                <option value="Disable">ปิดใช้งาน</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="edit_username" class="form-label">ชื่อผู้ใช้</label>
                            <input type="text" class="form-control" id="edit_username" name="username" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_password" class="form-label">รหัสผ่านใหม่</label>
                            <input type="password" class="form-control" id="edit_password" name="password">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_confirm_password" class="form-label">ยืนยันรหัสผ่านใหม่</label>
                            <input type="password" class="form-control" id="edit_confirm_password" name="confirm_password">
                        </div>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                        <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">ปิด</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">ลบผู้ใช้งาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p>คุณแน่ใจหรือไม่ว่าต้องการลบผู้ใช้งานนี้?</p>
                <form id="deleteUserForm">
                    <input type="hidden" id="delete_users_id" name="users_id">
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-danger me-2">ลบ</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>