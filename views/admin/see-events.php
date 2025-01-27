<?php ?>

<div style="padding-right: 10px;">
    <h1><?php esc_html(get_admin_page_title()) ?></h1>
    <form method="post">
        <!-- Garante que possa ser retornado a página atual. -->
        <input type="hidden" name="page" value="<?php $_REQUEST['page'] ?>" />

        <?php
        //Botão de pesquisa
        $vibe_positiva_events_list_table->search_box('Buscar Evento', 'search');
        // Inclui a marcação da visualização.
        $vibe_positiva_events_list_table->display();
        ?>

    </form>
</div>

<!-- Modal -->
<div id="modal-edit-event" class="modal" style="display: none;">
    <div class="modal-content ">
        <h2>Editar Evento</h2>
        <form id="form-edit-event">
            <input type="hidden" id="event-id" name="id">
            <p>
                <label for="title">Título:</label>
                <input type="text" id="event-title" name="title" class="regular-text">
            </p>
            <p>
                <label for="enabled">Ativo:</label>
                <input type="checkbox" id="event-enabled" name="enabled">
            </p>
            <p>
                <label for="event_date">Data:</label>
                <input type="date" id="event-event_date" name="event_date">
            </p>
            <p>
                <label for="image">Imagem:</label>
                <input type="hidden" id="event-image" name="image">
                <button type="button" id="select-image" class="button">Selecionar Imagem</button>
            <div id="preview-image" style="margin-top: 10px;">
                <img src="" alt="Pré-visualização" style="max-width: 200px; max-height: 200px display: none;">
            </div>
            </p>
            <p>
                <label for="price">Preço:</label>
                <input type="number" id="event-price" name="price" step="0.01" class="regular-text">
            </p>
            <p>
                <label for="page_path" title="É o caminho na URL que leva para uma página.">URI da Página:</label>
                <input type="text" id="event-page_path" name="page_path" class="regular-text">
            </p>
            <p>
                <label for="description">Descrição:</label>
                <textarea id="event-description" name="description" class="regular-text"></textarea>
            </p>
            <p>
                <button type="button" id="update-event" class="button button-primary">Salvar</button>
                <button type="button" id="close-modal" class="button">Fechar</button>
            </p>
        </form>
    </div>
</div>