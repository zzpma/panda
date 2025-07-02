<?php $this->assign('script', $this->fetch('script')); ?>
<h1>Add CRUD Record</h1>
    <?php
        // Open the form with file upload enabled
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
    <?= $this->Form->input('files.', array('type' => 'file', 'multiple' => true, 'required' => false)); ?>
</fieldset>

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
    <tbody></tbody>
</table>

<button type="button" id="addBeneficiary">+ Add Beneficiary</button>

<?= $this->Form->end('Submit'); ?>
