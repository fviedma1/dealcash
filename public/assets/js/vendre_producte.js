document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.venta-checkbox');
    const popup = document.getElementById('product-popup');

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function(e) {
            if (e.target.checked) {
                const productId = e.target.getAttribute('data-product-id');
                const productName = e.target.getAttribute('data-product-name');
                showPopup(productId, productName);
            }
        });
    });

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('popup-close')) {
            hidePopup();
        } else if (event.target.classList.contains('popup-si')) {
            const productId = event.target.getAttribute('data-product-id');
            fetchUsers(productId);
        } else if (event.target.classList.contains('popup-no')) {
            const productId = event.target.getAttribute('data-product-id');
            showDniNameFields(productId);
        } else if (event.target.classList.contains('popup-submit')) {
            const productId = event.target.getAttribute('data-product-id');
            submitForm(productId, event);
        }
    });

    function hidePopup() {
        popup.style.display = 'none';
        popup.querySelectorAll('input').forEach(input => input.value = '');
    }

    function showPopup(productId, productName) {
        const popupContent = popup.querySelector('.popup-content');
        popupContent.innerHTML = `
            <button class="popup-close"><i class="fas fa-times"></i></button>
            <h3>${productName}</h3>
            <p>Usuari registrat a la base de dades?</p>
            <button class="popup-si" data-product-id="${productId}">Sí</button>
            <button class="popup-no" data-product-id="${productId}">No</button>
        `;
        popup.style.display = 'flex';
    }

    function fetchUsers(productId) {
        const fetchUrl = '../../app/controllers/fetch_users.php';
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `${fetchUrl}?productId=${productId}`, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                const users = JSON.parse(xhr.responseText);
                showUsersList(productId, users);
            } else {
                console.error('Error fetching users:', xhr.status);
            }
        };
        xhr.send();
    }

    function showUsersList(productId, users) {
        const popupContent = popup.querySelector('.popup-content');
        let userOptions = '';

        users.forEach(user => {
            userOptions += `<li><a href="#" class="user-option" data-user-id="${user.id_usuari}" data-product-id="${productId}">${user.nom_usuari}</a></li>`;
        });

        popupContent.innerHTML = `
            <button class="popup-close"><i class="fas fa-times"></i></button>
            <h4>Selecciona un usuari:</h4>
            <ul>${userOptions}</ul>
        `;

        document.querySelectorAll('.user-option').forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                const userId = e.target.getAttribute('data-user-id');
                showPreuField(productId, userId);
            });
        });
    }

    function showPreuField(productId, userId) {
        const popupContent = popup.querySelector('.popup-content');
        popupContent.innerHTML = `
            <button class="popup-close"><i class="fas fa-times"></i></button>
            <div class="form-group">
                <label for="preu">Preu</label>
                <input type="text" id="preu" name="preu" required>
            </div>
            <button type="button" class="popup-submit" data-product-id="${productId}" data-user-id="${userId}">Enviar</button>
        `;
    }

    function showDniNameFields(productId) {
        const popupContent = popup.querySelector('.popup-content');
        popupContent.innerHTML = `
            <button class="popup-close"><i class="fas fa-times"></i></button>
            <div class="form-group">
                <label for="dni">DNI</label>
                <input type="text" id="dni" name="dni" required>
            </div>
            <div class="form-group">
                <label for="name">Nom</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="preu">Preu</label>
                <input type="text" id="preu" name="preu" required>
            </div>
            <button type="button" class="popup-submit" data-product-id="${productId}">Enviar</button>
        `;
    }

    function submitForm(productId, event) {
        const dni = document.getElementById('dni') ? document.getElementById('dni').value : null;
        const name = document.getElementById('name') ? document.getElementById('name').value : null;
        const preu = document.getElementById('preu').value;

        const formData = new FormData();
        formData.append('productId', productId);
        formData.append('preu', preu);

        if (dni && name) {
            formData.append('dni', dni);
            formData.append('name', name);
        } else {
            const userId = event.target.getAttribute('data-user-id');
            formData.append('userId', userId);
        }

        fetch('../../app/controllers/process_sale.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                hidePopup();
                actualizarProductosVendidos(productId);
            })
            .catch(error => {
                hidePopup();
            });
    }

    function actualizarProductosVendidos(productId) {
        let productosVendidos = document.getElementById('productosVendidos');
        let vendidos = productosVendidos.value ? JSON.parse(productosVendidos.value) : [];
        vendidos.push(productId);
        productosVendidos.value = JSON.stringify(vendidos);
    }

    const finalizarSubhastaForm = document.getElementById('finalizar-subhasta-form');
    if (finalizarSubhastaForm) {
        finalizarSubhastaForm.addEventListener('submit', function(event) {
            const checkboxes = document.querySelectorAll('.venta-checkbox');
            let vendidos = [];

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    vendidos.push(checkbox.getAttribute('data-product-id'));
                }
            });

            let productosVendidosInput = document.getElementById('productosVendidos');
            productosVendidosInput.value = JSON.stringify(vendidos);
        });
    }
});