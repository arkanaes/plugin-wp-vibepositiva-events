jQuery(document).ready(function ($) {
    $('.open-modal-edit').on('click', function () {
        // Preencher os dados do evento no modal
        $('#event-id').val($(this).data('id'));
        $('#event-title').val($(this).data('title'));
        $('#event-description').val($(this).data('description'));
        $('#event-enabled').prop('checked', $(this).data('enabled') == 1);
        $('#event-event_date').val($(this).data('event_date'));
        $('#event-image').val($(this).data('image'));
        $('#event-price').val($(this).data('price'));
        $('#event-page_path').val($(this).data('page_path'));

        // Atualizar a pré-visualização da imagem
        const imageUrl = $(this).data('image');
        if (imageUrl) {
            $('#preview-image img').attr('src', imageUrl).show();
        } else {
            $('#preview-image img').hide();
        }

        // Mostrar o modal
        $('#modal-edit-event').fadeIn();
    });

    $('.delete-event').on('click', function () {
        
        const eventoId = $(this).data('id');
        // Swal.fire popup de confirmação
        Swal.fire({
            text: "Você deseja realmente deletar o Evento (" + $(this).data('title') + ")?",
            icon: "warning",
            iconColor: "#dc3545",
            showCancelButton: true,
            cancelButtonColor: "#adb5bd",
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#dc3545",
            confirmButtonText: "Sim, deletar!",
        }).then((result) => {
            if (result.isConfirmed) {
                // Fazer requisição AJAX para excluir o evento
                $.post(ajaxurl, {
                    action: 'delete_event',
                    id: eventoId,
                }, function (resposta) {
                    if (resposta.success) {
                        // Swal.fire popup de confirmação
                        Swal.fire({
                            toast: true,
                            position: 'bottom-end',
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true,
                            title: "Deletado com sucesso!",
                            icon: "success"
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        alert('Erro ao excluir o evento: ' + resposta.data.message);
                    }
                }, 'json');
            }
        });
    });

    $('#close-modal').on('click', function () {
        $('#modal-edit-event').fadeOut();
    });

    $('#update-event').on('click', function () {
        const data = $('#form-edit-event').serialize();

        // Fazer requisição AJAX para atualizar o evento
        $.post(ajaxurl, {
            action: 'update_event',
            data: data,
        }, function (resposta) {
    
            // Swal.fire popup de confirmação
            Swal.fire({
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
                title: resposta.data.message,
                icon: resposta.success ? "success" : "error"
            }).then(() => {
                if (resposta.success) {
                    location.reload();
                }
            });


        }, 'json');
    });

    let frame; // Referência para o Media Frame

    // Abrir a Media Library ao clicar no botão
    $('#select-image').on('click', function (e) {
        e.preventDefault();

        // Se o frame já foi criado, reabra-o
        if (frame) {
            frame.open();
            return;
        }

        // Crie o Media Frame
        frame = wp.media({
            title: 'Selecione ou Envie uma Imagem',
            button: {
                text: 'Usar esta Imagem',
            },
            multiple: false, // Permite selecionar apenas uma imagem
        });

        // Quando uma imagem é selecionada
        frame.on('select', function () {
            const attachment = frame.state().get('selection').first().toJSON();

            // Atualizar o campo oculto e a pré-visualização
            $('#event-image').val(attachment.url);
            $('#preview-image img').attr('src', attachment.url).show();
        });

        // Abrir o frame
        frame.open();
    });
});






