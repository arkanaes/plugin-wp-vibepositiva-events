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
        wp_send_json_error(array('message' => $wpdb->last_error ?: 'Erro ao criar o evento.'));
    }
}

/**
 * Função de callback para atualizar um evento
 */
function update_event_ajax() {
    global $wpdb;

    $nome_tabela = $wpdb->prefix . 'events';

    $data = $_POST['data'];

    parse_str( $data, $data_array );

    $resultado = $wpdb->update(
        $nome_tabela,
        array(
            'title'       => sanitize_text_field( $data_array['title'] ),
            'description' => sanitize_textarea_field( $data_array['description'] ),
            'enabled'     => isset( $data_array['enabled'] ) ? 1 : 0,
            'event_date'  => sanitize_text_field($data_array['event_date']),
            'image'       => esc_url_raw( $data_array['image'] ),
            'price'       => floatval( $data_array['price'] ),
            'page_path'    => sanitize_text_field( $data_array['page_path'] ),
        ),
        array( 'id' => intval( $data_array['id'] ) ),
        array( '%s', '%s', '%d', '%s', '%s', '%f', '%s' ),
        array( '%d' )
    );

    //Verifica se a inserção foi bem-sucedida
    if ($resultado) {
        wp_send_json_success( array( 'message' => 'Evento atualizado com sucesso!' ) );
    } else {
        wp_send_json_error(array('message' => $wpdb->last_error ?: 'Não foi possível atualizar o evento.'));
    }
}

function delete_event_ajax() {
    global $wpdb;

    // Nome da tabela
    $nome_tabela = $wpdb->prefix . 'events';

    // Obter o ID enviado via AJAX
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = intval($_POST['id']); // Sanitizar o ID

        // Deletar o registro do banco de dados
        $excluded = $wpdb->delete(
            $nome_tabela,           // Nome da tabela
            array('id' => $id),     // Condição (WHERE id = $id)
            array('%d')             // Formato do ID
        );

        if ($excluded) {
            // Sucesso ao deletar
            wp_send_json_success(array('message' => 'Evento excluído com sucesso!'));
        } else {
            // Falha ao deletar
            wp_send_json_error(array('message' => $wpdb->last_error ?: 'Não foi possível excluir o evento.'));
        }
    } else {
        // ID não foi enviado
        wp_send_json_error(array('message' => 'ID do evento não foi fornecido.'));
    }
}
