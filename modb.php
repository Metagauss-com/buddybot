<?php

namespace MetagaussOpenAI;

class MoDb
{   
    protected $config;
    protected $charset;
    
    public function setPreliminaries()
    {
        global $wpdb;
        $this->charset = $wpdb->get_charset_collate();
        $this->config = MoConfig::getInstance();
    }
    
    private function addThreadsTable()
    {
        $table_name = $this->config->getDbTable('threads');
        $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        thread_id varchar(40),
        user_id mediumint(9),
        PRIMARY KEY  (id)
        )  $this->charset;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($sql);
    }
    
    public function addTables()
    {
        $this->addThreadsTable();
    }
    
    public function installPlugin()
    {
        $this->setPreliminaries();
        $this->addTables();
    }
    
    public function __construct()
    {
    }
}