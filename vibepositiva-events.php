<?php
/**
 * Plugin Name: Vibe Positiva - Eventos
 * Description: Um plugin para gerenciar os eventos da Vibe Positiva
 * Version: 1.0
 * Author: Yuri Geiger
 */

 // Evita o acesso direto ao arquivo
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Cria a tabela personalizada no banco de dados
 */
function events_plugin_criar_tabela() {
    global $wpdb;

    // Nome da tabela personalizada
    $nome_tabela = $wpdb->prefix . 'events'; 

    // Comando SQL para criar a tabela
    $sql = "CREATE TABLE $nome_tabela (
        id INT(11) NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        enabled TINYINT(1) DEFAULT 1,
        image VARCHAR(255),
        price DECIMAL(10,2) DEFAULT 0.00,
        path_pag VARCHAR(255),
        PRIMARY KEY (id)
    );";

    // Usando dbDelta() para criar a tabela, não sobrescreve dados existentes
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Registrar a função para ser executada ao ativar o plugin
register_activation_hook(__FILE__, 'events_plugin_criar_tabela');

/**
 * Deleta a tabela personalizada do banco de dados
 */
function events_plugin_deletar_tabela() {
    global $wpdb;

    // Nome da tabela personalizada
    $nome_tabela = $wpdb->prefix . 'events';

    // Comando SQL para deletar a tabela
    $sql = "DROP TABLE IF EXISTS $nome_tabela;";

    // Executa o comando SQL
    $wpdb->query($sql);
}

// Registrar a função para ser executada ao desinstalar o plugin
register_uninstall_hook(__FILE__, 'events_plugin_deletar_tabela');


function vibepositiva_events_options_page_html() {
    global $wpdb;

    // Nome da tabela (com prefixo dinâmico)
    $nome_tabela = $wpdb->prefix . 'events';

    // Consultando os dados da tabela
    $eventos = $wpdb->get_results( "SELECT * FROM $nome_tabela" );

    echo '<div class="wrap">';
    echo '<h1>Lista de Eventos</h1>';

    if ( empty( $eventos ) ) {
        echo '<p>Nenhum evento encontrado.</p>';
        return;
    }

    // Exibindo os dados em uma tabela HTML
    echo <<<html
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Ativo</th>
                <th>Imagem</th>
                <th>Preço</th>
                <th>URL</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody> 
    html;


    foreach ( $eventos as $evento ) {
        echo '<tr>';
        echo '<td>' . esc_html( $evento->id ) . '</td>';
        echo '<td>' . esc_html( $evento->name ) . '</td>';
        echo '<td>' . esc_html( $evento->description ) . '</td>';
        echo '<td>' . ( $evento->enabled ? 'Sim' : 'Não' ) . '</td>';
        echo '<td><img src="' . esc_url( $evento->image ) . '" alt="' . esc_attr( $evento->name ) . '" width="50" height="50"></td>';
        echo '<td>R$ ' . number_format( $evento->price, 2, ',', '.' ) . '</td>';
        echo '<td>' . esc_html( $evento->path_pag ) . '</td>';
        echo '<td>
        <button class="button button-primary abrir-modal-editar" 
            data-id="' . esc_attr( $evento->id ) . '" 
            data-name="' . esc_attr( $evento->name ) . '" 
            data-description="' . esc_attr( $evento->description ) . '" 
            data-enabled="' . esc_attr( $evento->enabled ) . '" 
            data-image="' . esc_url( $evento->image ) . '" 
            data-price="' . esc_attr( $evento->price ) . '" 
            data-path_pag="' . esc_attr( $evento->path_pag ) . '"
        >Editar</button>
        <button class="excluir-evento" 
        data-id="' . esc_attr( $evento->id ) . '" 
        data-name="' . esc_attr( $evento->name ) . '"
        >Excluir</button>
        </td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';

    echo <<<modal
    <div id="modal-editar-evento" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Editar Evento</h2>
            <form id="form-editar-evento">
                <input type="hidden" id="evento-id" name="id">
                <p>
                    <label for="evento-nome">Nome:</label>
                    <input type="text" id="evento-nome" name="name" class="regular-text">
                </p>
                <p>
                    <label for="evento-descricao">Descrição:</label>
                    <textarea id="evento-descricao" name="description" class="regular-text"></textarea>
                </p>
                <p>
                    <label for="evento-ativo">Ativo:</label>
                    <input type="checkbox" id="evento-ativo" name="enabled">
                </p>
                <p>
                    <label for="evento-imagem">Imagem:</label>
                    <input type="hidden" id="evento-imagem" name="image">
                    <button type="button" id="selecionar-imagem" class="button">Selecionar Imagem</button>
                    <div id="preview-imagem" style="margin-top: 10px;">
                        <img src="" alt="Pré-visualização" style="max-width: 200px; max-height: 200px display: none;">
                    </div>
                </p>
                <p>
                    <label for="evento-preco">Preço:</label>
                    <input type="number" id="evento-preco" name="price" step="0.01" class="regular-text">
                </p>
                <p>
                    <label for="evento-path">Path:</label>
                    <input type="text" id="evento-path" name="path_pag" class="regular-text">
                </p>
                <p>
                    <button type="button" id="salvar-evento" class="button button-primary">Salvar</button>
                    <button type="button" id="fechar-modal" class="button">Fechar</button>
                </p>
            </form>
        </div>
    </div>
    modal;
    
}

function carregar_scripts_admin() {
    wp_enqueue_media(); // Carrega os scripts necessários para a Media Library
    wp_enqueue_script(
        'sweetalert2', // Nome único para o script
        'https://cdn.jsdelivr.net/npm/sweetalert2@11', // URL da CDN
        array(), // Dependências (se precisar, como jQuery)
        null, // Versão (null usa a última versão)
        true // Coloca o script no final do body
    );
    wp_enqueue_script( 'custom-admin-script', plugin_dir_url(__FILE__) . '/js/admin.js', array( 'jquery','sweetalert2'), '1.0', true );
    wp_enqueue_style( 'custom-admin-style', plugin_dir_url(__FILE__) . '/css/admin.css' );
}
add_action( 'admin_enqueue_scripts', 'carregar_scripts_admin' );

function salvar_evento_ajax() {
    global $wpdb;

    $nome_tabela = $wpdb->prefix . 'events';
    $dados = $_POST['dados'];
    parse_str( $dados, $dados_array );

    $wpdb->update(
        $nome_tabela,
        array(
            'name'        => sanitize_text_field( $dados_array['name'] ),
            'description' => sanitize_textarea_field( $dados_array['description'] ),
            'enabled'     => isset( $dados_array['enabled'] ) ? 1 : 0,
            'image'       => esc_url_raw( $dados_array['image'] ),
            'price'       => floatval( $dados_array['price'] ),
            'path_pag'    => sanitize_text_field( $dados_array['path_pag'] ),
        ),
        array( 'id' => intval( $dados_array['id'] ) ),
        array( '%s', '%s', '%d', '%s', '%f', '%s' ),
        array( '%d' )
    );

    wp_send_json_success( array( 'message' => 'Evento atualizado com sucesso!' ) );
}
add_action( 'wp_ajax_salvar_evento', 'salvar_evento_ajax' );


function vibepositiva_events_options_page() {
    add_menu_page(
        'Vibe Positiva - Eventos', // Título da página
        'Eventos', // Título do menu
        'manage_options', // Permissão necessária
        'vibepositivamenueventos', // Slug único
        'vibepositiva_events_options_page_html', // Função de renderização
        plugin_dir_url(__FILE__) . 'images/person-hiking.svg', // Ícone do menu
        20 // Posição no menu
    );
}

add_action( 'admin_menu', 'vibepositiva_events_options_page' );


function excluir_evento_ajax() {
    global $wpdb;

    // Nome da tabela
    $nome_tabela = $wpdb->prefix . 'events';

    // Obter o ID enviado via AJAX
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = intval($_POST['id']); // Sanitizar o ID

        // Deletar o registro do banco de dados
        $deletado = $wpdb->delete(
            $nome_tabela,           // Nome da tabela
            array('id' => $id),     // Condição (WHERE id = $id)
            array('%d')             // Formato do ID
        );

        if ($deletado) {
            // Sucesso ao deletar
            wp_send_json_success(array('message' => 'Evento excluído com sucesso!'));
        } else {
            // Falha ao deletar
            wp_send_json_error(array('message' => 'Não foi possível excluir o evento.'));
        }
    } else {
        // ID não foi enviado
        wp_send_json_error(array('message' => 'ID do evento não foi fornecido.'));
    }
}
add_action( 'wp_ajax_excluir_evento', 'excluir_evento_ajax' );

//  add_menu_page(
//     'Eventos',
//     'Eventos',
//     'manage_options',
//     'events',
//  );

// // Registra o custom post type "Atividades"
// function atividades_plugin_register_post_type() {
//     $labels = array(
//         'name'                  => 'Atividades',
//         'singular_name'         => 'Atividade',
//         'menu_name'             => 'Atividades',
//         'name_admin_bar'        => 'Atividade',
//         'add_new'               => 'Adicionar Nova',
//         'add_new_item'          => 'Adicionar Nova Atividade',
//         'new_item'              => 'Nova Atividade',
//         'edit_item'             => 'Editar Atividade',
//         'view_item'             => 'Ver Atividade',
//         'all_items'             => 'Todas as Atividades',
//         'search_items'          => 'Procurar Atividades',
//         'not_found'             => 'Nenhuma atividade encontrada.',
//         'not_found_in_trash'    => 'Nenhuma atividade encontrada na lixeira.',
//         'featured_image'        => 'Imagem destacada',
//         'set_featured_image'    => 'Definir imagem destacada',
//         'remove_featured_image' => 'Remover imagem destacada',
//         'use_featured_image'    => 'Usar como imagem destacada',
//     );

//     $args = array(
//         'labels'             => $labels,
//         'public'             => true,
//         'show_ui'            => true,
//         'show_in_menu'       => true,
//         'show_in_admin_bar'  => true,
//         'menu_position'      => 5,
//         'supports'           => array( 'title', 'editor', 'thumbnail' ),
//         'has_archive'        => true,
//         'rewrite'            => array( 'slug' => 'atividades' ),
//     );

//     register_post_type( 'atividade', $args );
// }
// add_action( 'init', 'atividades_plugin_register_post_type' );

// // Adiciona o campo "Preço" ao custom post type "Atividades"
// function atividades_plugin_add_meta_boxes() {
//     add_meta_box(
//         'atividade_preco',
//         'Preço da Atividade',
//         'atividades_plugin_preco_callback',
//         'atividade',
//         'side',
//         'default'
//     );
// }
// add_action( 'add_meta_boxes', 'atividades_plugin_add_meta_boxes' );

// // Função para renderizar o campo de preço
// function atividades_plugin_preco_callback( $post ) {
//     $preco = get_post_meta( $post->ID, '_atividade_preco', true );
//     echo '<input type="text" name="atividade_preco" value="' . esc_attr( $preco ) . '" />';
// }

// // Salva o campo "Preço"
// function atividades_plugin_save_post( $post_id ) {
//     if ( ! isset( $_POST['atividade_preco'] ) ) {
//         return;
//     }
//     $preco = sanitize_text_field( $_POST['atividade_preco'] );
//     update_post_meta( $post_id, '_atividade_preco', $preco );
// }
// add_action( 'save_post', 'atividades_plugin_save_post' );

// // Adiciona um campo "Ativo/Inativo" com checkbox
// function atividades_plugin_add_active_field() {
//     add_meta_box(
//         'atividade_ativo',
//         'Atividade Ativa?',
//         'atividades_plugin_active_callback',
//         'atividade',
//         'side',
//         'default'
//     );
// }
// add_action( 'add_meta_boxes', 'atividades_plugin_add_active_field' );

// function atividades_plugin_active_callback( $post ) {
//     $ativo = get_post_meta( $post->ID, '_atividade_ativo', true );
//     echo '<input type="checkbox" name="atividade_ativo" ' . checked( $ativo, 'on', false ) . ' /> Ativa';
// }

// function atividades_plugin_save_active_field( $post_id ) {
//     if ( isset( $_POST['atividade_ativo'] ) ) {
//         update_post_meta( $post_id, '_atividade_ativo', 'on' );
//     } else {
//         delete_post_meta( $post_id, '_atividade_ativo' );
//     }
// }
// add_action( 'save_post', 'atividades_plugin_save_active_field' );

// // Exibe as atividades na página inicial
// function atividades_plugin_display_atividades() {
//     $args = array(
//         'post_type'      => 'atividade',
//         'posts_per_page' => -1,
//         'meta_key'       => '_atividade_ativo',
//         'meta_value'     => 'on',
//     );

//     $atividades = new WP_Query( $args );

//     if ( $atividades->have_posts() ) {
//         echo '<ul>';
//         while ( $atividades->have_posts() ) {
//             $atividades->the_post();
//             $preco = get_post_meta( get_the_ID(), '_atividade_preco', true );
//             $path = get_permalink();
//             $imagem = get_the_post_thumbnail_url();

//             echo '<li>';
//             if ( $imagem ) {
//                 echo '<img src="' . esc_url( $imagem ) . '" alt="' . get_the_title() . '" />';
//             }
//             echo '<h3>' . get_the_title() . '</h3>';
//             echo '<p>' . get_the_excerpt() . '</p>';
//             echo '<p>Preço: ' . esc_html( $preco ) . '</p>';
//             echo '<a href="' . esc_url( $path ) . '">Ver mais</a>';
//             echo '</li>';
//         }
//         echo '</ul>';
//     }

//     wp_reset_postdata();
// }
// add_action( 'wp_footer', 'atividades_plugin_display_atividades' );
