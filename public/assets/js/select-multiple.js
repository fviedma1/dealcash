document.addEventListener('DOMContentLoaded', function() {
    const selectElement = document.getElementById('productes');

    selectElement.addEventListener('mousedown', function(e) {
        e.preventDefault(); // Evita el comportamiento predeterminado de selección
        const option = e.target;

        if (option.tagName === 'OPTION') {
            option.selected = !option.selected; // Alterna la selección
        }
    });
});
