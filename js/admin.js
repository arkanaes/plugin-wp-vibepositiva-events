jQuery(document).ready(function ($) {
    $('.abrir-modal-editar').on('click', function () {
        // Preencher os dados do evento no modal
        $('#evento-id').val($(this).data('id'));
        $('#evento-nome').val($(this).data('name'));
        $('#evento-descricao').val($(this).data('description'));
        $('#evento-ativo').prop('checked', $(this).data('enabled') == 1);
        $('#evento-imagem').val($(this).data('image'));
        $('#evento-preco').val($(this).data('price'));
        $('#evento-path').val($(this).data('path_pag'));
    
        // Atualizar a pré-visualização da imagem
        const imageUrl = $(this).data('image');
        if (imageUrl) {
            $('#preview-imagem img').attr('src', imageUrl).show();
        } else {
            $('#preview-imagem img').hide();
        }
    
        // Mostrar o modal
        $('#modal-editar-evento').fadeIn();
    });

    $('.excluir-evento').on('click', function () {
        const eventoId = $(this).data('id');
        Swal.fire({
            text: "Você deseja realmente deletar o Evento ("+$(this).data('name')+")?",
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
                    action: 'excluir_evento',
                    id: eventoId,
                }, function (resposta) {
                    if (resposta.success) {
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

    $('#fechar-modal').on('click', function () {
        $('#modal-editar-evento').fadeOut();
    });

    $('#salvar-evento').on('click', function () {
        const dados = $('#form-editar-evento').serialize();

        // Fazer requisição AJAX para salvar os dados
        $.post(ajaxurl, {
            action: 'salvar_evento',
            dados: dados,
        }, function (resposta) {
            alert(resposta.data.message);
            if (resposta.success) {
                location.reload();
            }
        }, 'json');
    });

    let frame; // Referência para o Media Frame

    // Abrir a Media Library ao clicar no botão
    $('#selecionar-imagem').on('click', function (e) {
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
            $('#evento-imagem').val(attachment.url);
            $('#preview-imagem img').attr('src', attachment.url).show();
        });

        // Abrir o frame
        frame.open();
    });
});






