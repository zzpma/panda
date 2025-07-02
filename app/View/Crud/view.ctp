<?php $this->assign('title', 'View CRUD Record'); ?>

<h1>View CRUD Record</h1>

<!-- General Information -->
<fieldset class="crud-fieldset">
    <legend class="crud-legend">General Information</legend>
    <div class="crud-info">
        <div class="info-item">
            <label>Name:</label>
            <span><?= h($crud['Crud']['name']) ?></span>
        </div>
        <div class="info-item">
            <label>Email:</label>
            <span><?= h($crud['Crud']['email']) ?></span>
        </div>
        <div class="info-item">
            <label>Birth Date:</label>
            <span><?= date('m/d/Y', strtotime($crud['Crud']['birth_date'])) ?></span>
        </div>
        <div class="info-item">
            <label>Age:</label>
            <span id="userAge">--</span>
            <span id="userBirthDate" data-birthdate="<?= h($crud['Crud']['birth_date']) ?>" hidden></span>
        </div>
        <div class="info-item">
            <label>Status:</label>
            <span class="status <?= strtolower($crud['CrudStatus']['status'] ?? 'pending') ?>">
                <?= h($crud['CrudStatus']['status'] ?? 'PENDING') ?>
            </span>
        </div>
    </div>
</fieldset>

<!-- Approve/Disapprove Buttons -->
<?php if (empty($crud['CrudStatus']['status']) || $crud['CrudStatus']['status'] === 'PENDING'): ?>
    <div class="form-group">
        <?= $this->Form->postLink('Approve', [
            'action' => 'approve', $crud['Crud']['id']
        ], [
            'class' => 'btn btn-success',
            'confirm' => 'Are you sure?'
        ]) ?>

        <?= $this->Form->postLink('Disapprove', [
            'action' => 'disapprove', $crud['Crud']['id']
        ], [
            'class' => 'btn btn-danger',
            'confirm' => 'Are you sure?'
        ]) ?>
    </div>
<?php endif; ?>


<!-- Uploaded Files -->
<?php if (!empty($crud['CrudFile'])): ?>
    <h3 style="margin-top:20px">Uploaded Files</h3>
    <ul>
        <?php foreach ($crud['CrudFile'] as $file): ?>
            <li>
                <?= h($file['file_name']) ?>
                <?= $this->Html->link('Download', '/' . $file['file_path'], [
                    'target' => '_blank',
                    'class' => 'btn btn-sm btn-primary'
                ]) ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<!-- Beneficiaries Table -->
<h3 style="margin-top:20px">Beneficiaries</h3>
<table class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Birth Date</th>
            <th>Age</th>
            <th>Relationship</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($crud['Beneficiary'])): ?>
            <?php foreach ($crud['Beneficiary'] as $beneficiary): ?>
                <tr>
                    <td><?= h($beneficiary['name']) ?></td>
                    <td><?= date('m/d/Y', strtotime($beneficiary['birth_date'])) ?></td>
                    <td class="beneficiary-age">--</td>
<td class="beneficiary-birthdate" data-birthdate="<?= h($beneficiary['birth_date']) ?>" hidden></td>
                    <td><?= h($beneficiary['relationship']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No beneficiaries found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>


<div class="form-group">
    <!-- Edit/Delete Buttons (Disabled if not PENDING) -->
    <?php if (isset($crud['CrudStatus']['status'])): ?>
        <button class="btn btn-warning" disabled>Edit</button>
        <button class="btn btn-danger" disabled>Delete</button>
    <?php else: ?>
        <?= $this->Html->link('Edit', [
            'action' => 'edit', $crud['Crud']['id']
        ], [
            'class' => 'btn btn-warning'
        ]) ?>

        <?= $this->Form->postLink('Delete', [
            'action' => 'delete', $crud['Crud']['id']
        ], [
            'class' => 'delete',
            'confirm' => 'Are you sure?'
        ]) ?>
    <?php endif; ?>
    <!-- Print Button (Enabled only if Approved) -->
<?php if ($crud['CrudStatus']['status'] === 'APPROVED'): ?>
    <a href="<?php echo $this->Html->url(['action' => 'print', $crud['Crud']['id']]); ?>" class="btn btn-primary">
        Print
    </a>
<?php else: ?>
    <button class="btn btn-primary" disabled>Print</button>
<?php endif; ?>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    function computeAge(birthDateString) {
        if (!birthDateString) return "--";
        
        const birthDate = new Date(birthDateString);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        return age >= 0 ? age : "--";
    }

    // Main user age computation
    const birthDateElement = document.getElementById("userBirthDate");
    const ageElement = document.getElementById("userAge");

    if (birthDateElement && ageElement) {
        ageElement.textContent = computeAge(birthDateElement.dataset.birthdate);
    }

    // Beneficiary age computation
    document.querySelectorAll(".beneficiary-birthdate").forEach(function (birthDateElement) {
        const ageElement = birthDateElement.closest("tr").querySelector(".beneficiary-age");
        if (ageElement) {
            ageElement.textContent = computeAge(birthDateElement.dataset.birthdate);
        }
    });
});
</script>