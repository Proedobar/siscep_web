function confirmarEliminacion(url) {
    Swal.fire({
        title: '¿Está seguro?',
        text: '¿Está seguro de que desea eliminar este elemento?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(url, {})
                .done(function(data) {
                    Swal.fire({
                        title: '¡Eliminado!',
                        text: 'El elemento ha sido eliminado con éxito.',
                        icon: 'success'
                    }).then(() => {
                        $.pjax.reload({container: '#pjax-container'});
                    });
                })
                .fail(function() {
                    Swal.fire({
                        title: 'Error',
                        text: 'Ha ocurrido un error al eliminar el elemento.',
                        icon: 'error'
                    });
                });
        }
    });
    return false;
} 