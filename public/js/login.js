const hidePassword = (idBtnPassword, idInputPassword) => {
    const eInputPassword = document.getElementById(idInputPassword)
    const eBtnPassword = document.getElementById(idBtnPassword)
    console.dir(eBtnPassword.children[0].classList)
    if (eInputPassword.type === 'password') {
        eInputPassword.type = 'text'
        eBtnPassword.children[0].classList.replace('bx-show', 'bx-hide')
    } else {
        eInputPassword.type = 'password'
        eBtnPassword.children[0].classList.replace('bx-hide', 'bx-show')
    }
}
