document.addEventListener('DOMContentLoaded', function() {
    const productsContainer = document.querySelector('.range');
    const apiUrl = '../../BACKEND/public/index.php';
    const apiKey = 'apikey1223432432432';

    async function fetchProducts() {
        productsContainer.innerHTML = '<p>Loading books...</p>';

        const requestPayload = {
            type: "GetAllProducts",
            api_key: apiKey,
        };

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(requestPayload)
            });

            if (!response.ok) {
                let errorData;
                try {
                    errorData = await response.json();
                } catch (e) {
                }
                const errorMessage = errorData && errorData.message ? errorData.message : `HTTP error ${response.status}`;
                throw new Error(errorMessage);
            }

            const result = await response.json();

            if (result.status === "success" && result.data) {
                displayProducts(result.data);
            } else {
                const errorMessage = result.message || "Could not retrieve products.";
                productsContainer.innerHTML = `<p>Error: ${errorMessage}</p>`;
                console.error("API Error:", result);
            }

        } catch (error) {
            productsContainer.innerHTML = `<p>Failed to load books. ${error.message}</p>`;
            console.error('Error fetching products:', error);
        }
    }

    function displayProducts(products) {
        productsContainer.innerHTML = '';

        if (products.length === 0) {
            productsContainer.innerHTML = '<p>No books found.</p>';
            return;
        }

        products.forEach(product => {
            const card = document.createElement('div');
            card.className = 'card';

            const img = document.createElement('img');
            img.src = product.thumbnail || product.smallThumbnail || '../Images/default_book.png';
            img.alt = product.title;
            img.className = 'card-image';

            const title = document.createElement('h2');
            title.textContent = product.title;

            const cardContent = document.createElement('div');
            cardContent.className = 'card-content';

            const author = document.createElement('p');
            author.textContent = product.author || 'N/A';

            const rating = document.createElement('p');
            if (product.book_rating) {
                const numericRating = parseFloat(product.book_rating);
                rating.textContent = `${numericRating.toFixed(2)} ⭐`;
            } else {
                rating.textContent = 'Not Rated ⭐';
            }

            card.appendChild(img);
            card.appendChild(title);
            
            cardContent.appendChild(author);
            cardContent.appendChild(rating);

            card.appendChild(cardContent);

            productsContainer.appendChild(card);
        });
    }

    fetchProducts();

    //Future: Add event listeners for search, sort, category filters
    // e.g., document.getElementById('searchButton').addEventListener('click', () => {
    //     const searchTerm = document.getElementById('searchInput').value;
    //     fetchProducts({ title: searchTerm }); // You'll need to modify fetchProducts to accept params
    // });
});