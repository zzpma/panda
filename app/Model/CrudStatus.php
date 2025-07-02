<?php
App::uses('AppModel', 'Model');

class CrudStatus extends AppModel {
    public $belongsTo = array(
        'Crud' => array(
            'className' => 'Crud',
            'foreignKey' => 'id' // Link to cruds.id
        )
    );
}