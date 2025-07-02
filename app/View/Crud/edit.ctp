<?php $this->assign('script', $this->fetch('script')); ?>
<h1>Edit CRUD Record</h1>
<?php
    // Open the form with file upload enabled; model data is auto-populated from $crud
    echo $this->Form->create('Crud', array('type' => 'file'));
?>
<fieldset>
    <legend>General Information</legend>
    <?= $this->Form->input('name', array('label' => 'Name')); ?>
    <?= $this->Form->input('email', array('label' => 'Email')); ?>
    <?= $this->Form->input('birth_date', array(
        'label' => 'Birth Date',
        'id' => 'CrudBirthDate',
        'type' => 'text'
    )); ?>
    <label style="margin-left: 10px;">Age: <span id="CrudAgeDisplay">--</span></label>
</fieldset>

<!-- Uploaded Files Section -->
<h3>Uploaded Files</h3>
<?php if (!empty($files)): ?>
    <ul>
    <?php foreach ($files as $file): ?>
        <li>
            <?php echo h($file['CrudFile']['file_name']); ?>
            <?php echo $this->Html->link('Download', '/' . $file['CrudFile']['file_path'], array('target' => '_blank')); ?>
            <button type="button" class="delete-file" data-file-id="<?= h($file['CrudFile']['id']); ?>">Delete</button>
            <!-- Hidden input to flag the file for deletion -->
            <?= $this->Form->hidden("CrudFile.{$file['CrudFile']['id']}.delete", [
                'value' => '0', // Default to "don't delete"
                'class' => 'delete-file-flag'
            ]); ?>
        </li>
    <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No files uploaded.</p>
<?php endif; ?>
<?= $this->Form->input('files.', array('type' => 'file', 'multiple' => true, 'required' => false)); ?>

<!-- Beneficiaries Section -->
<h3>Beneficiaries</h3>
<table id="beneficiaryTable">
    <thead>
        <tr>
            <th>Name</th>
            <th>Birth Date</th>
            <th>Age</th>
            <th>Relationship</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($beneficiaries as $index => $beneficiary): ?>
        <tr>
        <?= $this->Form->hidden("beneficiaries.{$index}.id", [
            'value' => h($beneficiary['Beneficiary']['id'])
        ]); ?>
        <!-- Marks row for deletion -->
        <?= $this->Form->hidden("beneficiaries.{$index}.delete", [
            'value' => '0', // Default to "don't delete"
            'class' => 'delete-flag'
        ]); ?>
        <td>
            <?= $this->Form->input("beneficiaries.{$index}.name", [
                'value' => h($beneficiary['Beneficiary']['name']),
                'required' => true,
                'div' => false,
                'label' => false
            ]); ?>
        </td>
            <td>
                <?= $this->Form->input("beneficiaries.{$index}.birth_date", [
                    'type' => 'text',
                    'class' => 'beneficiary-birthdate',
                    'value' => h($beneficiary['Beneficiary']['birth_date']),
                    'required' => true,
                    'div' => false,
                    'label' => false
                ]); ?>
            </td>
            <td><span class="beneficiary-age">--</span></td>
            <td>
                <?= $this->Form->input("beneficiaries.{$index}.relationship", [
                    'value' => h($beneficiary['Beneficiary']['relationship']),
                    'required' => true,
                    'div' => false,
                    'label' => false
                ]); ?>
            </td>
            <td>
                <button type="button" class="removeBeneficiary" data-id="<?= h($beneficiary['Beneficiary']['id']); ?>">Remove</button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<button type="button" id="addBeneficiary">+ Add Beneficiary</button>

<?= $this->Form->end('Update'); ?>