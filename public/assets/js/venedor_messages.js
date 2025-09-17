document.addEventListener('DOMContentLoaded', function () {
    // Función para cargar mensajes del servidor sin recargar la página
    function cargarMensajes() {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_messages.php', true);  // Este archivo PHP devolverá los mensajes en formato JSON

        xhr.onload = function () {
            if (this.status === 200) {
                const messages = JSON.parse(this.responseText);
                const messageList = document.querySelector('.message-section ul');
                messageList.innerHTML = '';  // Limpia la lista de mensajes

                if (messages.length > 0) {
                    messages.forEach(message => {
                        const li = document.createElement('li');
                        li.className = `producte-${message.estado}`;  // Agrega la clase en función del estado
                        li.innerHTML = `
                            <strong>Producte:</strong> ${message.product_name}<br>
                            <strong>Estat:</strong> ${message.estado}<br>
                            <strong>Missatge:</strong> ${message.mensaje}<br>
                        `;
                        messageList.appendChild(li);
                    });
                } else {
                    messageList.innerHTML = '<p>No tens missatges.</p>';
                }
            }
        };

        xhr.onerror = function () {
            console.error('Error de conexión con el servidor');
        };

        xhr.send();
    }

    // Llamar a la función para cargar los mensajes al cargar la página
    cargarMensajes();

    // Opcional: Actualizar mensajes cada cierto tiempo (por ejemplo, cada 30 segundos)
    setInterval(cargarMensajes, 30000);  // 30000 ms = 30 segundos
});
