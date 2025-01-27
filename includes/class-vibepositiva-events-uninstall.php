<?php

class Vibe_Positiva_Events_Uninstall
{
    /**
     * Deleta a tabela personalizada do banco de dados
     */
    public static function delete_events_table()
    {
        global $wpdb;

        // Nome da tabela personalizada
        $nome_tabela = $wpdb->prefix . 'events';

        // Comando SQL para deletar a tabela
        $sql = "DROP TABLE IF EXISTS $nome_tabela;";

        // Executa o comando SQL
        $wpdb->query($sql);
    }
}
