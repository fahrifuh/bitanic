const yourRequest = async (url, settings) => {
    try {
        const response = await fetch(url, settings)

        if (response.ok) {
            const data = await response.json()
            return [data, null]
        }

        const error = await response.json()
        return [null, error]
    } catch (error) {
        return [null, error]
    }
}

async function handleDeleteRows(url, token, name, redirect = null) {
    const result = await Swal.fire({
        text: "Menghapus data tidak dapat dibatalkan, dan semua data yang berhubungan akan hilang",
        icon: "warning",
        showCancelButton: true,
        buttonsStyling: false,
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Tidak, batalkan",
        customClass: {
            confirmButton: "btn fw-bold btn-danger",
            cancelButton: "btn fw-bold btn-active-light-primary"
        }
    })

    if (result.value) {
        showSpinner()
        // Simulate delete request -- for demo purpose only
        const settings = {
            method: 'DELETE',
            headers: {
                'x-csrf-token': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        }

        const [res, error] = await yourRequest(url, settings)

        if (error) {
            deleteSpinner()

            if ("messages" in error) {
                let errorMessage = ''

                // myModal.toggle()

                let element = ``
                for (const key in error.messages) {
                    if (Object.hasOwnProperty.call(error.messages, key)) {
                        error.messages[key].forEach(message => {
                            element += `<li>${message}</li>`;
                        });
                    }
                }

                errorMessage = `<ul>${element}</ul>`

                Swal.fire({
                    html: errorMessage,
                    icon: "error",
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
            }

            return false
        }

        Swal.fire({
            text: "Kamu berhasil menghapus data " + name + "!",
            icon: "success",
            showConfirmButton: false,
            allowOutsideClick: false
        })

        window.location.reload();
    }

}
