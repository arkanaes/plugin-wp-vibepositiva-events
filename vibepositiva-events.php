<?php
/**
 * Plugin Name: Vibe Positiva - Eventos
 * Description: Um plugin para gerenciar os eventos da Vibe Positiva
 * Version: 1.0
 * Author: Yuri Geiger
 */

//Permitir o acesso ao arquivo se o WordPress estiver carregado 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Verifica se a classe WP_List_Table já existe (Classe nativa do WordPress)
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

add_thickbox();

require_once plugin_dir_path( __FILE__ ) . 'includes/class-vibepositiva-events-activator.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-vibepositiva-events-uninstall.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/function.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/ajax.php';

// Ao ativar o plugin, cria a tabela de eventos
register_activation_hook(__FILE__, ['Vibe_Positiva_Events_Activator', 'create_events_table']);

// Ao excluir o plugin, deleta a tabela de eventos
register_uninstall_hook(__FILE__, ['Vibe_Positiva_Events_Uninstall', 'delete_events_table']);

// Adiciona Menu de Opções
add_action( 'admin_menu', 'vibepositiva_events_register_menus' );

// Adiciona ação para criar um evento na página de "criar evento"
add_action( 'wp_ajax_create_event', 'create_event_ajax' );
// Adiciona ação para atualizar um evento na página de "ver evento"
add_action('wp_ajax_update_event', 'update_event_ajax');
// Adiciona ação para excluir um evento na página de "ver evento"
add_action( 'wp_ajax_delete_event', 'delete_event_ajax' );

// Adiciona Estilos e Scripts
add_action( 'admin_enqueue_scripts', 'vibepositiva_admin_assets' );