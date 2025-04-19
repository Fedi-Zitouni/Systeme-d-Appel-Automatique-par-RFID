<div class="table-responsive-sm" style="max-height: 870px;">
  <table class="table" id="users-table">
    <thead class="table-primary">
      <tr>
        <th>Card UID</th>
        <th>Name</th>
        <th>Gender</th>
        <th>Student ID</th>
        <th>Class Group</th>
        <th>Department</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody class="table-secondary">
      <?php
      require 'connectDB.php';
      $sql = "SELECT u.*, 
              CONCAT(al.abbreviation, 
                    IF(s.name IS NULL, '', CONCAT('-', s.name)), 
                    '-TD', cg.group_number, '-TP', cg.group_number) AS class_name
              FROM users u
              LEFT JOIN course_groups cg ON u.class_group_id = cg.group_id
              LEFT JOIN academic_levels al ON cg.level_id = al.level_id
              LEFT JOIN specialties s ON cg.specialty_id = s.specialty_id
              ORDER BY u.id DESC";
      
      $result = mysqli_stmt_init($conn);
      if (!mysqli_stmt_prepare($result, $sql)) {
        echo '<p class="error">SQL Error: ' . mysqli_error($conn) . '</p>';
      } else {
        mysqli_stmt_execute($result);
        $resultl = mysqli_stmt_get_result($result);
        if (mysqli_num_rows($resultl) > 0) {
          while ($row = mysqli_fetch_assoc($resultl)) {
            ?>
            <TR>
              <TD>
                <?php if ($row['card_select'] == 1): ?>
                  <span><i class='glyphicon glyphicon-ok' title='Selected UID'></i></span>
                <?php endif; ?>
                <button type="button" class="select_btn" id="<?= htmlspecialchars($row['card_uid']) ?>" 
                  title="select this UID"><?= htmlspecialchars($row['card_uid']) ?></button>
              </TD>
              <TD><?= htmlspecialchars($row['username']) ?></TD>
              <TD><?= htmlspecialchars($row['gender']) ?></TD>
              <TD><?= htmlspecialchars($row['serialnumber']) ?></TD>
              <TD><?= isset($row['class_name']) ? htmlspecialchars($row['class_name']) : 'Not assigned' ?></TD>
              <TD><?= ($row['device_dep'] == "0") ? "All" : htmlspecialchars($row['device_dep']) ?></TD>
              <TD><?= date("d/m/Y", strtotime($row['user_date'])) ?></TD>
            </TR>
            <?php
          }
        }
      }
      ?>
    </tbody>
  </table>
</div>

<div class="mt-3">
  <button id="export-pdf" class="btn btn-secondary">Export to PDF</button>
  <button id="export-excel" class="btn btn-success">Export to Excel</button>
  <button id="import-data" class="btn btn-info">Bulk Import</button>
</div>

<script>
$(document).ready(function() {

  $('#export-pdf').click(function() {
    window.open('export_users.php?type=pdf', '_blank');
  });

  $('#export-excel').click(function() {
    window.open('export_users.php?type=excel', '_blank');
  });

  $('#import-data').click(function() {
    $('#importModal').modal('show');
  });

  $('#confirm-import').click(function() {
    const formData = new FormData($('#import-form')[0]);
    $.ajax({
      url: 'import_users.php',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function(response) {
        alert(response);
        location.reload();
      }
    });
  });
});
</script>