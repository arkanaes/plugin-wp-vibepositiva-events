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

function vibepositiva_events_add_html() {
    ?>
    <div class="wrap" style="max-width: 1200px; margin: 0 auto; padding-right: 10px;">
        <h1 class="wp-heading-inline">Adicionar Novo Evento</h1>

        <form id="form-criar-evento">
            <?php
            // Usar o campo nonce para segurança
            // settings_fields('vibepositiva_events_options_group');
            ?>

            <table class="form-table">
                <tr>
                    <th scope="row"><label for="evento_name">Nome do Evento</label></th>
                    <td><input type="text" id="evento_name" name="name" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="evento_price">Preço</label></th>
                    <td><input type="number" id="evento_price" name="price" class="regular-text" step="0.01" min="0" required></td>
                </tr>
                <!-- <tr>
                    <th scope="row"><label for="evento_price">Preço</label></th>
                    <td><input type="number" id="evento_price" name="evento_price" class="regular-text" step="0.01" min="0" required></td>
                </tr> -->

                <tr>
                    <th scope="row"><label for="evento_path_pag">Caminho da Página</label></th>
                    <td><input type="text" id="evento_path_pag" name="path" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="evento_image">Imagem</label></th>
                    <td>
                        <input type="text" id="evento_image" name="image" class="regular-text" readonly style="background-color: white;">
                        <input type="button" class="button" value="Selecionar Imagem" id="select_image_button">
                        <div id="image_preview" style="margin-top: 10px;">
                            <img id="image_preview_img" src="" style="max-width: 200px; display: none;">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="evento_description">Descrição</label></th>
                    <td><textarea id="evento_description" name="description" class="large-text" rows="5" required></textarea></td>
                </tr>
            </table>
            <button class="button button-primary criar-evento" type="button">
                <i class="fa-regular fa-floppy-disk"></i>
                Criar Evento
            </button>
        </form>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var mediaFrame;

            // Selecionar imagem da biblioteca de mídia
            $('#select_image_button').on('click', function(e) {
                e.preventDefault();

                if (mediaFrame) {
                    mediaFrame.open();
                    return;
                }

                mediaFrame = wp.media({
                    title: 'Selecione ou Envie uma Imagem',
                    button: {
                        text: 'Usar esta Imagem'
                    },
                    multiple: false
                });

                mediaFrame.on('select', function() {
                    var attachment = mediaFrame.state().get('selection').first().toJSON();
                    $('#evento_image').val(attachment.url);
                    $('#image_preview_img').attr('src', attachment.url).show();
                });

                mediaFrame.open();
            });
        });
    </script>

    <?php
}

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

    wp_enqueue_style(
        'bootstrap-cdn', // Handle para o CSS
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css', // URL do CSS
        array(), // Dependências (não há nenhuma)
        '6.7.2', // Versão do CSS
        'all' // Tipo de mídia
    );

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

// Hook into the admin_head action to run your code in the admin area


function wpdocs_admin_code() {




    // More conditions can be added as needed
}
add_action( 'admin_head', 'wpdocs_admin_code' );


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

function criar_evento_ajax() {
    global $wpdb;

    $nome_tabela = $wpdb->prefix . 'events'; // Nome da tabela

    $dados = $_POST['dados'];
    parse_str($dados, $dados_array); // Converte os dados da string para um array

    //Insere os dados no banco de dados
    $resultado = $wpdb->insert(
        $nome_tabela,
        array(
            'name'        => sanitize_text_field($dados_array['name']),
            'description' => sanitize_textarea_field($dados_array['description']),
            'enabled'     => isset($dados_array['enabled']) ? 1 : 0,
            'image'       => esc_url_raw($dados_array['image']),
            'price'       => floatval($dados_array['price']),
            'path_pag'    => sanitize_text_field($dados_array['path_pag']),
        ),
        array(
            '%s', // Formato do campo 'name'
            '%s', // Formato do campo 'description'
            '%d', // Formato do campo 'enabled'
            '%s', // Formato do campo 'image'
            '%f', // Formato do campo 'price'
            '%s', // Formato do campo 'path_pag'
        )
    );

    //Verifica se a inserção foi bem-sucedida
    if ($resultado) {
        wp_send_json_success(array('message' => 'Evento criado com sucesso!'));
    } else {
        wp_send_json_error(array('message' => 'Erro ao criar o evento.'));
    }
}
add_action( 'wp_ajax_criar_evento', 'criar_evento_ajax' );


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

    // Adicionar submenu 1
    add_submenu_page(
        'vibepositivamenueventos', // Slug do menu principal
        'Lista de Eventos', // Título da página do submenu
        'Lista', // Título do item do submenu
        'manage_options', // Capacidade necessária
        'vibe-positiva-events-lista-de-eventos', // Slug da página do submenu
        'vibepositiva_events_options_page_html' // Função de callback para renderizar o conteúdo
    );
    add_submenu_page(
        'vibepositivamenueventos', // Slug do menu principal
        'Adicionar Evento', // Título da página do submenu
        'Adicionar', // Título do item do submenu
        'manage_options', // Capacidade necessária
        'vibe-positiva-events-lista-de-eventos2', // Slug da página do submenu
        'vibepositiva_events_add_html' // Função de callback para renderizar o conteúdo
    );

    //Remove o Menu Principal dos Sub-menus
    remove_submenu_page('vibepositivamenueventos', 'vibepositivamenueventos');
}

// Adiciona Menu de Opções
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

