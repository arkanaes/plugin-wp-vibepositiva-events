<?php

function vibepositiva_events_create_event_html()
{
?>
    <div class="wrap" style="max-width: 1200px; margin: 0 auto; padding-right: 10px;">
        <h1 class="wp-heading-inline">Criar Novo Evento</h1>

        <form id="form-create_event">
            <?php
            // Usar o campo nonce para segurança
            wp_nonce_field( 'create_event_nonce');
            ?>

            <table class="form-table">
                <tr>
                    <th scope="row"><label for="title">Título</label></th>
                    <td><input type="text" id="title" name="title" class="regular-text" required placeholder="Ex.: Rapel Cachoeira do Quincé"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="price">Preço</label></th>
                    <td><input type="number" id="price" name="price" class="regular-text" step="0.01" min="0" required placeholder="150.00"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="event-date">Data do Evento</label></th>
                    <td><input type="date" id="event-date" name="event-date" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="page_path">Caminho da Página</label></th>
                    <td><input type="text" id="page_path" name="page_path" class="regular-text" required placeholder="/rapel-cachoeira-do-quince"></td>
                </tr>
                <tr>
                    <th>    
                        <label for="image">Imagem:</label>
                    </th>
                    <td>
                        <input type="hidden" id="image" name="image">
                        <button type="button" id="select-image" class="button" style="background-color: #2271b1; color: #fff;" value="Selecionar Imagem">Selecionar Imagem</button>
                        <div id="preview-image" style="margin-top: 10px;">
                            <img src="" alt="Pré-visualização" style="width: 100%; max-width:500px; display: none;">
                        </div>
                    </td>                    
                </tr>
                <tr>
                    <th scope="row"><label for="description">Descrição</label></th>
                    <td><textarea id="description" name="description" class="large-text" rows="5" required>Junte-se a nós para uma experiência emocionante de rapel em [Nome do Local], um dos destinos mais incríveis para quem busca adrenalina e contato com a natureza. Prepare-se para descer desafiadoras paredes rochosas, aproveitando as vistas deslumbrantes enquanto sente a emoção de conquistar a altura.</textarea></td>
                </tr>
            </table>
            <button class="button button-primary btn-create-event" type="button">
                <i class="fa-regular fa-floppy-disk"></i>
                Criar Evento
            </button>
        </form>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var mediaFrame;

            $('#select-image').on('click', function(e) {
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
                    $('#image').val(attachment.url);
                    $('#preview-image img').attr('src', attachment.url).show();
                });

                mediaFrame.open();
            });
        });
    </script>

<?php
}
