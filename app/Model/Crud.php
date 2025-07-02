<?php
App::uses('AppModel', 'Model');

class Crud extends AppModel {
    public $useTable = 'cruds';

    public $validate = array(
        'name' => array(
            'rule' => 'notBlank',
            'message' => 'Name is required'
        ),
        'email' => array(
            'rule' => 'email',
            'message' => 'Please enter a valid email address.',
            'allowEmpty' => false
        ),
        'birth_date' => array(
            'rule' => 'date',
            'message' => 'Enter a valid date',
            'allowEmpty' => false,
        ),
        'file' => array(
            'rule' => array('fileSize', '<=', '2MB'), // Optional file validation
            'message' => 'File must be less than 2MB'
        )
    );


    public $hasOne = array(
        'CrudStatus' => array(
            'className' => 'CrudStatus',
            'foreignKey' => 'id' // Direct link: crud_statuses.id = cruds.id
        )
    );

    public $hasMany = array(
        'Beneficiary' => array(
            'className' => 'Beneficiary',
            'foreignKey' => 'cruds_id'
        ),
        'CrudFile' => array(
            'className' => 'CrudFile',
            'foreignKey' => 'crud_id'
        )
    );
}