document.addEventListener('DOMContentLoaded', function () {

    let btns = document.querySelectorAll(".button");
    let span = document.querySelector('.span-quantity');
    //const ou let obligatoire sinon prend le dernier
    for (const btn of btns) {

        btn.addEventListener("click", (e) => {
            //annule l'envoi du formulaire en php
            e.preventDefault();
            let id = btn.dataset.id;
            const input = btn.parentNode.querySelector('input');
            fetch('panier/ajout/' + id + '/?quantity=' + input.value)
                .then(response => {
                        return response.text()
                    }
                )
                .then(function () {
                    //changer contenu du bouton
                    btn.textContent = "Modifier";
                    //changer en appellant route la qty du panier
                    fetch('/qtyPanier')
                        .then(response => {
                                return response.text()
                            }
                        )
                        .then(function (quantity) {
                                span.innerHTML = quantity;
                            }
                        )
                })
        })
    }


})
