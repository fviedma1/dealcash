function filterProducts() {
    // Obtener los valores de la búsqueda y del filtro
    const searchValue = document.getElementById('searchBar').value.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, ""); // Normaliza y elimina acentos
    const priceFilterValue = document.getElementById('priceFilter').value;
    
    const products = document.querySelectorAll('.product');
    
    products.forEach(product => {
        // Filtrar por búsqueda
        const productName = product.querySelector('.titol-tercer').textContent.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        
        // Filtrar por precio
        const price = parseInt(product.getAttribute('data-price'));
        
        let priceMatch = true;
        if (priceFilterValue) {
            const [minPrice, maxPrice] = priceFilterValue.split('-').map(Number);
            priceMatch = price >= minPrice && price <= maxPrice;
        }

        // Mostrar/ocultar producto basado en la búsqueda y el filtro de precio
        if (productName.includes(searchValue) && priceMatch) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
}
