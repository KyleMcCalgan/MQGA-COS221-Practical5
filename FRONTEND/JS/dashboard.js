document.addEventListener('DOMContentLoaded', function(){
    const apiUrl = '../../BACKEND/public/index.php';
    const apiKey = '9e180a9e28783275354998ff5ecdae7ff85be073f357adbd4e7da49b95e92107';

    const productContainers = document.querySelectorAll('.range');
    const featuredBooksContainer = productContainers.length > 0 ? productContainers[0] : null;
    const highestRatedBooksContainer = productContainers.length > 1 ? productContainers[1] : null;

    function showUserMessage(container, message, isError = false){
        if (!container) return;
        container.innerHTML = '';
        const p = document.createElement('p');
        p.textContent = message;
        if (isError){
            p.style.color = 'red'; 
        }
        container.appendChild(p);
    }

    async function fetchApiData(payload){
        try{
            const response = await fetch(apiUrl,{
                method: 'POST',
                headers:{
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload)
            });

            if (!response.ok){
                let errorData;
                try{
                    errorData = await response.json();
                } catch (e){
                }
                const DerrorMessage = errorData && errorData.message ? errorData.message : `An HTTP error ${response.status} occurred.`;
                throw new Error(DerrorMessage);
            }

            const result = await response.json();

            if (result.status === "success" && result.data){
                return result.data;
            } else{
                const DerrorMessage = result.message || "Could not retrieve products due to an unknown API response.";
                throw new Error(DerrorMessage);
            }
        } catch (error){
            console.error(`Error fetching ${payload.type}:`, error.message);
            throw error;
        }
    }

    function createProductCard(product, index, includeBadge){
        const card = document.createElement('div');
        card.className = 'card';

        const link = document.createElement('a');
        link.href = `view.php?id=${encodeURIComponent(product.id || '')}`;

        const img = document.createElement('img');
        img.src = product.thumbnail || product.smallThumbnail || '../Images/notfound.png';
        img.alt = product.title || 'Book image';
        img.className = 'card-image';
        img.onerror = function(){
            this.onerror = null;
            this.src = '../Images/notfound.png';
            this.alt = 'Image failed to load';
        };

        if (includeBadge && typeof index === 'number'){
            const imageWrapper = document.createElement('div');
            imageWrapper.className = 'image-wrapper-for-badge';
            const badge = document.createElement('div');
            badge.className = 'image-number-badge';
            badge.textContent = index + 1;
            imageWrapper.appendChild(img);
            imageWrapper.appendChild(badge);
            link.appendChild(imageWrapper);
        } else{
            link.appendChild(img);
        }

        const titleElement = document.createElement('h2');
        titleElement.textContent = product.title || 'Untitled Book';
        link.appendChild(titleElement);
        card.appendChild(link);

        const cardContent = document.createElement('div');
        cardContent.className = 'card-content';

        const authorElement = document.createElement('p');
        authorElement.textContent = product.author || 'Author N/A';
        cardContent.appendChild(authorElement);

        const ratingElement = document.createElement('p');
        if (product.book_rating){
            const numericRating = parseFloat(product.book_rating);
            if (!isNaN(numericRating)){
                ratingElement.textContent = `${numericRating.toFixed(1)} ⭐`;
            } else{
                ratingElement.textContent = 'Rating N/A ⭐';
            }
        } else{
            ratingElement.textContent = 'Not Rated ⭐';
        }
        cardContent.appendChild(ratingElement);
        card.appendChild(cardContent);
        return card;
    }

    function displayProducts(container, products, includeBadge = false){
        if (!container) return;
        container.innerHTML = ''; 

        if (!products || products.length === 0){
            showUserMessage(container, 'No books found.');
            return;
        }

        products.forEach((product, index) =>{
            const card = createProductCard(product, index, includeBadge);
            container.appendChild(card);
        });
    }

    async function loadFeaturedBooks(){
        if (!featuredBooksContainer){
            console.warn('Featured books container not found.');
            return;
        }
        showUserMessage(featuredBooksContainer, 'Loading featured books...');
        try{
            const productsFromServer = await fetchApiData({ type: "GetFeaturedProducts", api_key: apiKey });

            const clientShuffledFeaturedProducts = [...productsFromServer].sort(() => 0.5 - Math.random());

            displayProducts(featuredBooksContainer, clientShuffledFeaturedProducts, false);
        } catch (error){
            showUserMessage(featuredBooksContainer, `Error loading featured books: ${error.message}`, true);
        }
    }

    async function loadHighestRatedBooks(){
        if (!highestRatedBooksContainer){
            console.warn('Highest rated books container not found.');
            return;
        }
        showUserMessage(highestRatedBooksContainer, 'Loading highest rated books...');
        try{
            const products = await fetchApiData({ type: "GetHighestRatedProducts", api_key: apiKey });
            displayProducts(highestRatedBooksContainer, products, true);
        } catch (error){
            showUserMessage(highestRatedBooksContainer, `Error loading highest rated books: ${error.message}`, true);
        }
    }

    loadFeaturedBooks();
    loadHighestRatedBooks();
});
