const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'default-btn m-2',
        cancelButton: 'default-btn m-2'
    },
    buttonsStyling: false
})

$('.btn-confirm-sweetalert').on('click', function (e) {
    const id = $(this).data('id');
    const url = $(this).data('url');
    const title = $(this).data('title');
    const text = $(this).data('text');
    const icon = $(this).data('icon');
    const self = $(this);
    e.preventDefault();
    swalWithBootstrapButtons.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonText: 'Confirm !',
        cancelButtonText: 'Cancel !'
    }).then((result) => {
        if (result.isConfirmed) {
            location.href = url;
        }
    })
})
