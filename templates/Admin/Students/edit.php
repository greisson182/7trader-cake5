<?php
$csrfToken = $this->request->getAttribute('csrfToken');
?>
<div class="students form content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-user-edit"></i> Editar Estudante</h3>
        <div>
            <a href="/admin/students" class="btn btn-secondary me-2 btn-with-icon">
                <i class="fas fa-list"></i>
                <span>Listar Estudantes</span>
            </a>
            <form method="POST" action="/students/delete/<?= isset($student['id']) ? $student['id'] : '' ?>" style="display: inline;"
                onsubmit="return confirm('Tem certeza que deseja excluir este estudante?')">
                <button type="submit" class="btn btn-danger btn-with-icon">
                    <i class="fas fa-trash"></i>
                    <span>Excluir</span>
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Student Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-user"></i> Informações do Estudante</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/students/edit/<?= isset($student['id']) ? $student['id'] : '' ?>" class="needs-validation" novalidate>
                        <input type="hidden" name="_csrfToken" value="<?= $csrfToken ?>">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="<?= isset($student['name']) ? htmlspecialchars($student['name']) : '' ?>" required>
                            <div class="invalid-feedback">
                                Por favor, forneça um nome válido.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= isset($student['email']) ? htmlspecialchars($student['email']) : '' ?>" required>
                            <div class="invalid-feedback">
                                Por favor, forneça um email válido.
                            </div>
                        </div>

                        <!-- User Access Control Section -->
                        <hr>
                        <h6><i class="fas fa-key"></i> User Access Control</h6>

                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                value="<?= isset($user['username']) ? htmlspecialchars($user['username']) : '' ?>" required>
                            <div class="form-text">Username for system login</div>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="student" <?= (isset($user['role']) && $user['role'] === 'student') ? 'selected' : '' ?>>Student</option>
                                <option value="admin" <?= (isset($user['role']) && $user['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                            </select>
                            <div class="form-text">User role in the system</div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="active" name="active" value="1"
                                    <?= (isset($user['active']) && $user['active']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="active">
                                    Active Account
                                </label>
                                <div class="form-text">Enable or disable user access to the system</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password (Optional)</label>
                            <input type="password" class="form-control" id="new_password" name="new_password"
                                placeholder="Leave blank to keep current password">
                            <div class="form-text">Only fill if you want to change the user's password</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="/admin/students" class="btn btn-secondary me-md-2 btn-with-icon">
                                <i class="fas fa-times"></i>
                                <span>Cancel</span>
                            </a>
                            <button type="submit" class="btn btn-primary btn-with-icon">
                                <i class="fas fa-save"></i>
                                <span>Update Student & User</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- User Information Display -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle"></i> Current User Status</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($user) && $user): ?>
                        <div class="mb-3">
                            <strong>Current Username:</strong>
                            <span class="badge bg-primary"><?= htmlspecialchars($user['username']) ?></span>
                        </div>
                        <div class="mb-3">
                            <strong>Current Role:</strong>
                            <span class="badge <?= $user['role'] === 'admin' ? 'bg-danger' : 'bg-info' ?>">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </div>
                        <div class="mb-3">
                            <strong>Account Status:</strong>
                            <span class="badge <?= $user['active'] ? 'bg-success' : 'bg-secondary' ?>">
                                <?= $user['active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </div>
                        <div class="mb-3">
                            <strong>User Created:</strong>
                            <small class="text-muted"><?= isset($user['created']) ? date('d/m/Y H:i', strtotime($user['created'])) : 'N/A' ?></small>
                        </div>
                        <div class="mb-3">
                            <strong>Last Modified:</strong>
                            <small class="text-muted"><?= isset($user['modified']) ? date('d/m/Y H:i', strtotime($user['modified'])) : 'N/A' ?></small>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            No user account found for this student. A user will be created automatically when you save.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Bootstrap form validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>