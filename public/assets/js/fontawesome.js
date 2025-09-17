// Crear un elemento <script>
let fontAwesomeScript = document.createElement('script');

// Asignar el enlace de tu kit de Font Awesome
fontAwesomeScript.src = "https://kit.fontawesome.com/ae2976ddfe.js";

// Añadir el atributo 'crossorigin'
fontAwesomeScript.setAttribute('crossorigin', 'anonymous');

// Añadir el script al <head> del documento
document.head.appendChild(fontAwesomeScript);
