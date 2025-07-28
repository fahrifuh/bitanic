const changeStatus = (motorStatus, statusElement) => {
    switch (motorStatus) {
        case 0:
            statusElement.classList.remove("bg-on-status")
            break;
        case 1:
            statusElement.classList.add("bg-on-status")
            break;

        default:
            break;
    }
}
