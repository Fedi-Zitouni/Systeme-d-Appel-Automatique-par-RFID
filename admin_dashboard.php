<?php
require 'connectDB.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les groupes de classe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-4">
    <h2>Gérer les groupes de classe</h2>
    
    <!-- Add Class Group Form -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
        Ajouter un nouveau groupe de classe
        </div>
        <div class="card-body">
            <form id="add-class-form">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="level">Niveau académique</label>
                            <select class="form-control" id="level" name="level" required>
                                <option value="">Sélectionnez le niveau</option>
                                <?php
                                $levels = $conn->query("SELECT * FROM academic_levels");
                                while ($level = $levels->fetch_assoc()) {
                                    echo "<option value='{$level['level_id']}'>{$level['abbreviation']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="specialty">Spécialité</label>
                            <select class="form-control" id="specialty" name="specialty">
                                <option value="">General (TC)</option>
                                <?php
                                $specialties = $conn->query("SELECT * FROM specialties");
                                while ($spec = $specialties->fetch_assoc()) {
                                    echo "<option value='{$spec['specialty_id']}'>{$spec['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="group_number">Numéro de groupe</label>
                            <input type="number" class="form-control" id="group_number" name="group_number" min="1" required>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Ajouter un groupe de classe</button>
            </form>
        </div>
    </div>

    <!-- Class Groups Table -->
    <div class="card">
        <div class="card-header bg-primary text-white">
        Groupes de classe existants
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="class-groups-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom de la classe</th>
                            <th>Niveau</th>
                            <th>Spécialité</th>
                            <th>Groupe</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT cg.group_id, al.abbreviation AS level, 
                                 IFNULL(s.name, 'TC') AS specialty, cg.group_number
                                 FROM course_groups cg
                                 JOIN academic_levels al ON cg.level_id = al.level_id
                                 LEFT JOIN specialties s ON cg.specialty_id = s.specialty_id
                                 ORDER BY al.level_id, s.specialty_id, cg.group_number";
                        $result = $conn->query($query);
                        
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $class_name = $row['level'] . 
                                            ($row['specialty'] != 'TC' ? '-' . $row['specialty'] : '') . 
                                            '-G' . $row['group_number'];
                                echo "<tr>
                                        <td>{$row['group_id']}</td>
                                        <td>{$class_name}</td>
                                        <td>{$row['level']}</td>
                                        <td>{$row['specialty']}</td>
                                        <td>{$row['group_number']}</td>
                                        <td>
                                            <button class='btn btn-sm btn-warning edit-class-btn' data-id='{$row['group_id']}'>Edit</button>
                                            <button class='btn btn-sm btn-danger delete-class-btn' data-id='{$row['group_id']}'>Delete</button>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>Aucun groupe de classe trouvé</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Class Modal -->
<div class="modal fade" id="editClassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier le groupe de classe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit-class-form">
                    <input type="hidden" id="edit-class-id" name="class_id">
                    <div class="form-group mb-3">
                        <label for="edit-level">Academic Level</label>
                        <select class="form-control" id="edit-level" name="level" required>
                            <?php
                            $levels = $conn->query("SELECT * FROM academic_levels");
                            while ($level = $levels->fetch_assoc()) {
                                echo "<option value='{$level['level_id']}'>{$level['abbreviation']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit-specialty">Specialty</label>
                        <select class="form-control" id="edit-specialty" name="specialty">
                            <option value="">General (TC)</option>
                            <?php
                            $specialties = $conn->query("SELECT * FROM specialties");
                            while ($spec = $specialties->fetch_assoc()) {
                                echo "<option value='{$spec['specialty_id']}'>{$spec['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit-group-number">Group Number</label>
                        <input type="number" class="form-control" id="edit-group-number" name="group_number" min="1" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save-class-changes">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Add new class group
    $('#add-class-form').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'manage_classes.php?action=add',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response === 'success') {
                    location.reload();
                } else {
                    alert('Error: ' + response);
                }
            }
        });
    });

    // Edit class group - open modal
    $('.edit-class-btn').click(function() {
        const classId = $(this).data('id');
        
        $.get('manage_classes.php?action=get&id=' + classId, function(data) {
            if (data) {
                const classData = JSON.parse(data);
                $('#edit-class-id').val(classData.group_id);
                $('#edit-level').val(classData.level_id);
                $('#edit-specialty').val(classData.specialty_id || '');
                $('#edit-group-number').val(classData.group_number);
                
                // Show the modal
                const editModal = new bootstrap.Modal(document.getElementById('editClassModal'));
                editModal.show();
            } else {
                alert('Error loading class data');
            }
        });
    });

    // Save edited class group
    $('#save-class-changes').click(function() {
        $.ajax({
            url: 'manage_classes.php?action=update',
            type: 'POST',
            data: $('#edit-class-form').serialize(),
            success: function(response) {
                if (response === 'success') {
                    location.reload();
                } else {
                    alert('Error: ' + response);
                }
            }
        });
    });

    // Delete class group
    $('.delete-class-btn').click(function() {
        if (confirm('Are you sure you want to delete this class group?')) {
            const classId = $(this).data('id');
            
            $.ajax({
                url: 'manage_classes.php?action=delete&id=' + classId,
                type: 'GET',
                success: function(response) {
                    if (response === 'success') {
                        location.reload();
                    } else {
                        alert('Error: ' + response);
                    }
                }
            });
        }
    });
});
</script>
</body>
</html>