<?php

class Vibe_Positiva_Events_Activator
{
    /**
     * Cria a tabela personalizada no banco de dados
     */
    public static function create_events_table()
    {
        global $wpdb;

        // Nome da tabela personalizada
        $nome_tabela = $wpdb->prefix . 'events';

        // Comando SQL para criar a tabela
        $sql = "CREATE TABLE $nome_tabela (
            id INT(11) NOT NULL AUTO_INCREMENT,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            enabled TINYINT(1) DEFAULT 1,
            image VARCHAR(255),
            price DECIMAL(10,2) DEFAULT 0.00,
            page_path VARCHAR(255),
            event_date DATE,
            PRIMARY KEY (id)
        );";

        // Usando dbDelta() para criar a tabela, não sobrescreve dados existentes
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
