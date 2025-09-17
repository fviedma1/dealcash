function normalizeText(text) {
    return text.toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/[^a-z0-9\s]/g, "");
}

function filterProducts() {
    const searchValue = normalizeText(document.getElementById('searchBar').value);
    const priceFilterValue = document.getElementById('priceFilter').value;
    const products = document.querySelectorAll('.product');
    let matchCount = 0;

    products.forEach(product => {
        const productName = normalizeText(product.querySelector('.titol-tercer').textContent);
        const price = parseInt(product.getAttribute('data-price'));
        let priceMatch = true;

        if (priceFilterValue) {
            if (priceFilterValue === '+500') {
                priceMatch = price > 500;
            } else {
                const [minPrice, maxPrice] = priceFilterValue.split('-').map(Number);
                priceMatch = price >= minPrice && price <= maxPrice;
            }
        }

        if (productName.includes(searchValue) && priceMatch) {
            product.style.display = 'block';
            matchCount++;
        } else {
            product.style.display = 'none';
        }
    });

    if (matchCount === 0) {
        console.log("No se encontraron productos, ocultando todos.");
        document.querySelector('.products-section').style.display = 'none';
    } else {
        document.querySelector('.products-section').style.display = 'block';
    }
}

document.getElementById('searchBar').addEventListener('input', filterProducts);
document.getElementById('priceFilter').addEventListener('change', filterProducts);