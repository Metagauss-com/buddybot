<?php

namespace BuddyBot;

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
        thread_id varchar(100),
        user_id mediumint(9),
        session_id varchar(255) NULL,
        user_type ENUM('logged_in', 'visitor') DEFAULT 'visitor',
        thread_name varchar(100),
        created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
        )  $this->charset;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($sql);
    }
    
    private function addChatbotTable()
    {
        $table_name = $this->config->getDbTable('chatbot');
        $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        chatbot_name varchar(256),
        chatbot_description varchar(1024),
        external BOOLEAN NOT NULL DEFAULT 0,
        assistant_id varchar(100),
        assistant_model VARCHAR(100) NOT NULL,
        personalized_options BOOLEAN NOT NULL DEFAULT 0,
        fallback_behavior VARCHAR(50) NOT NULL,
        emotion_detection BOOLEAN NOT NULL DEFAULT 0,
        greeting_message VARCHAR(256) NULL,
        multilingual_support BOOLEAN NOT NULL DEFAULT 0,
        supported_languages TEXT NULL,
        openai_search BOOLEAN NOT NULL DEFAULT 0,
        author mediumint(9),
        created_on TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        last_editor mediumint(9),
        edited_on TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
        )  $this->charset;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($sql);
    }

    private function addSettingsTable()
    {
        $table_name = $this->config->getDbTable('settings');
        $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        option_name varchar(256),
        option_value text,
        last_editor mediumint(9),
        edited_on datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
        )  $this->charset;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($sql);
    }
    
    private function addLogsTable()
    {
        $table_name = $this->config->getDbTable('logs');
        $sql = "CREATE TABLE $table_name (
            log_id INT NOT NULL AUTO_INCREMENT,
            log_event ENUM('sync', 'error', 'training', 'system', 'custom') NOT NULL,
            log_status ENUM('success', 'failure', 'pending', 'skipped') NOT NULL,
            log_description TEXT,
            log_bot_id INT NULL,
            log_details JSON NULL,
            log_ip_address VARCHAR(45) NULL,
            log_severity ENUM('INFO', 'WARNING', 'ERROR') DEFAULT 'INFO',
            log_component VARCHAR(50) NULL,
            log_referrer_url TEXT NULL,
            log_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (log_id)
        ) $this->charset;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    public function addTables()
    {
        $this->addThreadsTable();
        $this->addChatbotTable();
        $this->addSettingsTable();
        $this->addLogsTable();
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