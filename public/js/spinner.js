const showSpinner = () => {
    document.querySelector('body').insertAdjacentHTML('afterbegin', `
    <div class="position-fixed d-flex justify-content-center align-items-center bg-info p-2 text-dark align-middle" style="z-index: 2000; width: 100%; height: 100%;--bs-bg-opacity: .5;" id="custom-spinner">
        <div class="spinner-border text-dark" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>`)

    return true;
}

const deleteSpinner = () => {
    const customSprinner = document.getElementById('custom-spinner')

    if (customSprinner) {
        customSprinner.remove()
    }

    return true;
}
