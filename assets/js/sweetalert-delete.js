const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'default-btn m-2',
        cancelButton: 'default-btn m-2'
    },
    buttonsStyling: false
})

$('.delete-sweetalert').on('click',function (e) {
    const id = $(this).data('id');
    const url = $(this).data('url');
    const self = $(this);
    e.preventDefault();
    swalWithBootstrapButtons.fire({
        title: 'Are you sure you want to delete it ?',
        text: "This action is irreversible!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Confirm !',
        cancelButtonText: 'Cancel !'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                success: function (data) {
                    if(data == 1){
                        self.parents('#view'+ id).remove();
                        Swal.fire(
                                'Deleted!',
                                'The element has been deleted.',
                                'success'
                        )
                    }
                }
            });
        }
    })
})
