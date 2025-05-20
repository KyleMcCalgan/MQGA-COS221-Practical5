document.addEventListener('DOMContentLoaded', function(){
    const productsContainer = document.querySelector('.range');
    const apiUrl = '../../BACKEND/public/index.php'; 
    const apiKey = 'apikey1223432432432';

    function showUserMessage(container, message, isError = false){
        container.innerHTML = '';
        const p = document.createElement('p');
        p.textContent = message;
        if (isError){
            p.style.color = 'red';
        }
        container.appendChild(p);
    }

    async function fetchProducts(){
        showUserMessage(productsContainer, 'Loading books...');

        const req ={
            type: "GetAllProducts",
            api_key: apiKey,
        };

        try{
            const response = await fetch(apiUrl,{
                method: 'POST',
                headers:{
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(req)
            });

            if (!response.ok){
                let errorData;
                try{
                    errorData = await response.json();
                }catch (e){
                }
                const DerrorMessage = errorData && errorData.message ? errorData.message : `An HTTP error ${response.status}occurred.`;
                throw new Error(DerrorMessage);
            }

            const result = await response.json();

            if (result.status === "success" && result.data){
                displayProducts(result.data);
            }else{
                const DerrorMessage = result.message || "Could not retrieve products due to an unknown API response.";
                showUserMessage(productsContainer, `Error: ${DerrorMessage}`, true);
                console.error("API Error:", result);
            }

        }catch (error){
            showUserMessage(productsContainer, `Failed to load books: ${error.message}`, true);
            console.error('Error fetching products:', error);
        }
    }

    function displayProducts(products) {
        productsContainer.innerHTML = '';

        if (!products || products.length === 0) {
            showUserMessage(productsContainer, 'No books found.');
            return; 
        }

        const randProducts = [...products].sort(() => 0.5 - Math.random());

        const selectedProducts = randProducts.slice(0, 50);

        if (selectedProducts.length === 0){
            showUserMessage(productsContainer, 'No books to display after selection.');
            return;
        }

        selectedProducts.forEach(product => {
            const card = document.createElement('div');
            card.className = 'card';

            const img = document.createElement('img');
            img.src = product.thumbnail || product.smallThumbnail || '../Images/notfound.png'; 
            img.alt = product.title || 'Book Cover';
            img.className = 'card-image';
            img.onerror = function() {
                this.onerror = null;
                this.src = '../Images/notfound.jpg';
                this.alt = 'Image failed to load';
            };

            const titleElement = document.createElement('h2');
            titleElement.textContent = product.title || 'Untitled';

            const cardContent = document.createElement('div');
            cardContent.className = 'card-content';

            const authorElement = document.createElement('p');
            authorElement.textContent = product.author || 'N/A';

            const ratingElement = document.createElement('p');
            if (product.book_rating) {
                const numericRating = parseFloat(product.book_rating);
                ratingElement.textContent = !isNaN(numericRating) ? `${numericRating.toFixed(2)} ⭐` : 'Invalid Rating ⭐';
            } else {
                ratingElement.textContent = 'Not Rated ⭐';
            }

            card.appendChild(img);
            card.appendChild(titleElement);
            
            cardContent.appendChild(authorElement);
            cardContent.appendChild(ratingElement);

            card.appendChild(cardContent);

            productsContainer.appendChild(card);
        });
    }

    fetchProducts();
});