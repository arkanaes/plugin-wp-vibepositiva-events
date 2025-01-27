jQuery(document).ready(function ($) {
    $('.btn-create-event').on('click', function () {
        const dados = $('#form-create_event').serialize();
        console.log(dados)
        /**
         * Swal.fire popup de confirmação
         */
        Swal.fire({
            text: "Você deseja criar esse Evento?",
            icon: "warning",
            iconColor: "#087990",
            showCancelButton: true,
            cancelButtonColor: "#adb5bd",
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#087990",
            confirmButtonText: "Sim",
        }).then((result) => {
            if (result.isConfirmed) {
                // Fazer requisição AJAX para salvar os dados
                $.post(ajaxurl, {
                    action: 'create_event',
                    dados: dados,
                }, function (response) {
                    // Swal.fire popup de confirmação
                    Swal.fire({
                        toast: true,
                        position: 'bottom-end',
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true,
                        title: response.data.message,
                        icon: response.success ? "success" : "error"
                    }).then(() => {
                        if (response.success) {
                            location.reload();
                        }
                    });

                }, 'json');
            }
        });
    });
});



