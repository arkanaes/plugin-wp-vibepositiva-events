<?php

/**
 * Função de callback para criar um evento
 */
function create_event_ajax()
{
    // Verifica o nonce para segurança
    if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'create_event_nonce' ) ) {

        wp_send_json_error(array('message' => 'Erro de segurança: nonce inválido.'));
        return;
    }

    global $wpdb;

    $nome_tabela = $wpdb->prefix . 'events'; // Nome da tabela

    $dados = $_POST['dados'];
    parse_str($dados, $dados_array); // Converte os dados da string para um array

    //Insere os dados no banco de dados
    $resultado = $wpdb->insert(
        $nome_tabela,
        array(
            'title'        => sanitize_text_field($dados_array['title']),
            'description' => sanitize_textarea_field($dados_array['description']),
            'enabled'     => 0,
            'image'       => esc_url_raw($dados_array['image']),
            'price'       => floatval($dados_array['price']),
            'page_path'    => sanitize_text_field($dados_array['page_path']),
            'event_date' => sanitize_text_field($dados_array['event-date']),
        ),
        array(
            '%s', // Formato do campo 'name'
            '%s', // Formato do campo 'description'
            '%d', // Formato do campo 'enabled'
            '%s', // Formato do campo 'image'
            '%f', // Formato do campo 'price'
            '%s', // Formato do campo 'path_pag'
            '%s', // Formato do campo 'event_date'
        )
    );

    //Verifica se a inserção foi bem-sucedida
    if ($resultado) {
        wp_send_json_success(array('message' => 'Evento criado com sucesso!'));
    } else {
        wp_send_json_error(array('message' => 'Erro ao criar o evento.'));
    }
}
