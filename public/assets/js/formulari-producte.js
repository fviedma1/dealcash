"use strict"

const btn = document.querySelector('#button_submit');

btn.addEventListener('click', (e) => {
    e.preventDefault();

    const existingErrors = document.querySelectorAll('.error-message');
    existingErrors.forEach(error => error.remove());

    let hasError = false;

    const nom = document.querySelector('#nom').value;
    const descripcioCurta = document.querySelector('#descripcio-curta').value;
    const imatge = document.querySelector('#imatge').files[0];
    const descripcioLlarga = document.querySelector('#descripcio-llarga').value;
    let preu = document.querySelector('#preu').value;
    const observacions = document.querySelector('#observacions').value;

    if (!nom.trim()) {
        createErrorMessage("El nom no pot estar buit.", '#nom');
        hasError = true;
    } else if (nom.length > 100) {
        createErrorMessage("El nom ha de tenir un màxim de 100 caràcters.", '#nom');
        hasError = true;
    }

    if (!descripcioCurta.trim()) {
        createErrorMessage("La descripció curta no pot estar buida.", '#descripcio-curta');
        hasError = true;
    } else if (descripcioCurta.length > 255) {
        createErrorMessage("La descripció curta ha de tenir un màxim de 255 caràcters.", '#descripcio-curta');
        hasError = true;
    }

    if (!imatge) {
        createErrorMessage("Selecciona una imatge.", '#imatge');
        hasError = true;
    } else {
        const formatImatge = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!formatImatge.includes(imatge.type)) {
            createErrorMessage("El format de la imatge no és vàlid. Els formats permesos són: JPG, PNG, JPEG.", '#imatge');
            hasError = true;
        }
    }

    if (!descripcioLlarga.trim()) {
        createErrorMessage("La descripció llarga no pot estar buida.", '#descripcio-llarga');
        hasError = true;
    } else if (descripcioLlarga.length > 1000) {
        createErrorMessage("La descripció llarga ha de tenir un màxim de 1000 caràcters.", '#descripcio-llarga');
        hasError = true;
    }

    const preuValid = /^([0-9]{1,10}(\.[0-9]{1,2})?)$/.test(preu);
    if (!preuValid) {
        createErrorMessage("El preu ha de ser un número vàlid amb  10 dígits i 2 decimals màxims.", '#preu');
        hasError = true;
    }
    
    if (observacions.length > 255) {
        createErrorMessage("Les observacions han de tenir un màxim de 255 caràcters.", '#observacions');
        hasError = true;
    }

    // Si hay errores, no se envía el formulario
    if (hasError) return;

    // Si todas las validaciones pasan, enviar el formulario
    e.target.closest('form').submit();
});

// Función para crear y mostrar mensajes de error
function createErrorMessage(message, input) {
    const inputField = document.querySelector(input);
    
    // Crear un nuevo elemento para el mensaje de error
    const errorMessage = document.createElement('div');
    errorMessage.className = 'error-message';
    errorMessage.style.color = 'red'; // Estilo para el mensaje de error
    errorMessage.style.textAlign = 'left';
    errorMessage.style.fontSize = '12px'; // Tamaño de la fuente
    errorMessage.style.marginTop = '5px'; // Margen superior

    // Establecer el texto del mensaje
    errorMessage.textContent = message;

    // Insertar el mensaje debajo del campo de entrada
    inputField.parentNode.insertBefore(errorMessage, inputField.nextSibling);
}
