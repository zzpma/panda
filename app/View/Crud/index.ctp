<div class="header">
    <div>
        <h1>CRUDS</h1>
    </div>

    <!-- Advanced Search Form -->
    <div>
        <?= $this->Form->create(null, [
            'type' => 'get',
            'url' => ['controller' => 'crud', 'action' => 'index'],
            'class' => 'search-form-container'
        ]); ?>
            <?= $this->Form->input('search', [
                'label' => false,
                'placeholder' => 'Search',
                'class' => 'search-input'
            ]); ?>
            <?= $this->Form->input('status', [
                'label' => false,
                'type' => 'select',
                'options' => [
                    'PENDING' => 'Pending',
                    'APPROVED' => 'Approved',
                    'DISAPPROVED' => 'Disapproved'
                ],
                'empty' => 'All Statuses',
                'class' => 'status-dropdown'
            ]); ?>
            <?= $this->Form->submit('Search', ['class' => 'search-button']); ?>
        <?= $this->Form->end(); ?>
    </div>
</div>

<div class="bar">
    <div class="status-tabs">
        <?php
            $currentStatus = $this->request->query['status'] ?? 'All';
            $statuses = [
                'PENDING' => 'Pending',
                'APPROVED' => 'Approved',
                'DISAPPROVED' => 'Disapproved',
            ];
        ?>
        <ul><?php foreach ($statuses as $key => $label): ?>
            <?php if ($currentStatus === $key): ?>
                <li class="active-tab">
                    <?= $this->Html->link($label,['action' => 'index']); ?>
                </li>
            <?php else: ?> 
                <li>
                    <?php echo $this->Html->link($label,
                        ['action' => 'index',
                        '?' => ['status' => $key]]
                    ); ?>
                </li>
            <?php endif; ?>
        <?php endforeach; ?></ul>
    </div>
    <div class="buttons">
        <?= $this->Html->link('Add Record',
            ['action' => 'add'],
            ['class' => 'add-btn'],
        ); ?>
        <?=
            // Pass current query parameters to the print action so the print output is filtered too.
            $this->Html->link('Print',
            ['action' => 'print_index', '?' => $this->request->query], 
            ['class' => 'print-btn', 'target' => '_blank'],
        ); ?>
    </div>
</div>

<!-- CRUD Table -->
<table class="crud-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($cruds)): ?>
            <?php $page = 1 + 10 * ($this->request->params['paging']['Crud']['page'] - 1) ?>
			<?php foreach ($cruds as $index => $crud): ?>
                <tr>
                    <td><?= h($index + $page); ?></td>
                    <td><?= h($crud['Crud']['name']); ?></td>
                    <td><span class="status <?= strtolower($crud['CrudStatus']['status'] ?? 'pending') ?>">
                        <?= h($crud['CrudStatus']['status'] ?? 'PENDING') ?>
                    </span></td>
                    <td class="action-buttons">
                        <?= $this->Html->link('View', array('action' => 'view', $crud['Crud']['id'])); ?>
                        <?php if ($crud['CrudStatus']['status']): ?>
                            <button disabled>Edit</button>
                            <button disabled>Delete</button>
                        <?php else: ?>
                            <?= $this->Html->link('Edit', array('action' => 'edit', $crud['Crud']['id'])); ?>
                            <?= $this->Form->postLink('Delete', [
            'action' => 'delete', $crud['Crud']['id']
        ], [
            'class' => 'delete',
            'confirm' => 'Are you sure?'
        ]) ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No records found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Paginator -->
<div class="paginator">
    <?= $this->Paginator->prev('« Previous', ['url' => ['controller' => 'crud', 'action' => '']]) ?>
    <?= $this->Paginator->numbers(['url' => ['controller' => 'crud', 'action' => '']]) ?>
    <?= $this->Paginator->next('Next »', ['url' => ['controller' => 'crud', 'action' => '']]) ?>
</div>
