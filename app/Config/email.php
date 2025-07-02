<?php
class EmailConfig {

    public $default = array(
        'transport' => 'Mail',
        'from' => 'you@localhost',
    );

    public $gmail = array(
        'transport' => 'Smtp',
        'host' => 'ssl://smtp.gmail.com',
        'port' => 465,
        'username' => 'pma.mcp.stagingserver@gmail.com',
        'password' => 'xhrb gnjy jwqg xdun',
        'client' => null,
        'log' => false,
        'emailFormat' => 'both', // Sends both HTML and plain text
        'from' => array('pma.mcp.stagingserver@gmail.com' => ''), // Replace with your sender name
        'charset' => 'utf-8',
        'headerCharset' => 'utf-8',
    );

}