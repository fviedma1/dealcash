function filterProducts() {
    // Obtener los valores de la búsqueda y del filtro
    const estatFilterValue = document.getElementById('estatFilter').value;
    
    const products = document.querySelectorAll('.product');
    
    products.forEach(product => {
        // Filtrar por búsqueda
        const productName = product.querySelector('.titol-tercer').textContent.toLowerCase().normalize("NFD");
        
        // Filtrar por precio
        const price = parseInt(product.getAttribute('data-price'));
        let priceMatch = true;        
        if (estatFilterValue === 'validat') {
            priceMatch =  price > 200; 
        } else  if (estatFilterValue === 'rebutjat') {
            priceMatch =  price > 200; 
        } else  if (estatFilterValue === 'assignat') {
            priceMatch =  price > 200; 
        } else  if (estatFilterValue === 'pendent') {
            priceMatch =  price > 200; 
        }

        // Mostrar/ocultar producto basado en la búsqueda y el filtro de precio
        if (productName.includes(searchValue) && priceMatch) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
}
