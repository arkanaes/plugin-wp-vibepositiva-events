<?php

// Html das páginas criar evento e ver eventos
require_once plugin_dir_path(dirname(__FILE__)) . 'views/admin/create-event.php';

require_once 'class-vibepositiva-events-list-table.php';

/**
 * Função de callback para registra os menus de opções do plugin
 */
function vibepositiva_events_register_menus()
{
    add_menu_page(
        'Vibe Positiva - Eventos', // Título da página
        'Eventos', // Título do menu
        'manage_options', // Permissão necessária
        'vibe-positiva-events-menu', // Slug único
        'vibepositiva_events_options_page_html', // Função de renderização
        plugin_dir_url(dirname(__FILE__)) . '/assets/images/person-hiking.svg', // Ícone do menu
        20 // Posição no menu
    );

    // Adicionar submenu 1
    add_submenu_page(
        'vibe-positiva-events-menu', // Slug do menu principal
        'Lista de Eventos', // Título da página do submenu
        'Ver Eventos', // Título do item do submenu
        'manage_options', // Capacidade necessária
        'vibe-positiva-events-see-events', // Slug da página do submenu
        'vibepositiva_events_see_events_html' // Função de callback para renderizar o conteúdo
    );

    add_submenu_page(
        'vibe-positiva-events-menu', // Slug do menu principal
        'Criar Evento', // Título da página do submenu
        'Criar Evento', // Título do item do submenu
        'manage_options', // Capacidade necessária
        'vibe-positiva-events-create-events', // Slug da página do submenu
        'vibepositiva_events_create_event_html' // Função de callback para renderizar o conteúdo
    );

    //Remove o Menu Principal dos Sub-menus
    remove_submenu_page('vibe-positiva-events-menu', 'vibe-positiva-events-menu');
}

/**
 * Função de callback para carregar os styles e scripts do plugin
 */
function vibepositiva_admin_assets()
{
    // Verifica se a página atual é a página do plugin
    $currentScreen = get_current_screen();
    // Verifica se a página atual é a página de criar evento ou ver eventos
    if ($currentScreen->id === "eventos_page_vibe-positiva-events-create-events" || $currentScreen->id === "eventos_page_vibe-positiva-events-see-events") {
        wp_enqueue_media(); // Carrega os scripts necessários para a Media Library

        wp_enqueue_style(
            'fontawesome', // Handle para o CSS
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css', // URL do CSS
            array(), // Dependências (não há nenhuma)
            '6.7.2', // Versão do CSS
            'all' // Tipo de mídia
        );

        wp_enqueue_script(
            'sweetalert2', // Nome único para o script
            'https://cdn.jsdelivr.net/npm/sweetalert2@11', // URL da CDN
            array(), // Dependências (se precisar, como jQuery)
            null,
            true // Coloca o script no final do body
        );

    }

    if ($currentScreen->id === "eventos_page_vibe-positiva-events-see-events") {
        wp_enqueue_style('custom-admin-style', plugin_dir_url(dirname(__FILE__)) . 'assets/css/admin/see-events.css');
        wp_enqueue_script('see-events-script', plugin_dir_url(dirname(__FILE__)) . 'assets/js/admin/see-events.js', array('jquery', 'sweetalert2'), '1.0', true);
    }

    if ($currentScreen->id === "eventos_page_vibe-positiva-events-create-events") {
        wp_enqueue_script('create-event-script', plugin_dir_url(dirname(__FILE__)) . 'assets/js/admin/create-event.js', array('jquery', 'sweetalert2'), '1.0', true);
    }
}


function vibepositiva_events_see_events_html()
{
    $vibe_positiva_events_list_table = new Vibe_Positiva_Events_List_Table();

    $vibe_positiva_events_list_table->prepare_items();

    include plugin_dir_path(dirname(__FILE__)) . 'views/admin/see-events.php';
}
