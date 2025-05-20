document.addEventListener('DOMContentLoaded', function(){
    const apiUrl = '../../BACKEND/public/index.php';
    const apiKey = 'apikey1223432432432';

    const productContainers = document.querySelectorAll('.range');
    const featuredBooksContainer = productContainers[0];
    const highestRatedBooksContainer = productContainers[1];

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
                }catch (e){
                }
                const errorMessage = errorData && errorData.message ? errorData.message : `HTTP error ${response.status}`;
                throw new Error(errorMessage);
            }
            const result = await response.json();
            if (result.status === "success" && result.data){
                return result.data;
            }else{
                const errorMessage = result.message || "Could not retrieve products.";
                throw new Error(errorMessage);
            }
        }catch (error){
            console.error(`Error fetching ${payload.type}:`, error);
            throw error;
        }
    }

    function createProductCard(product, index, includeBadge){
        const card = document.createElement('div');
        card.className = 'card';

        const link = document.createElement('a');
        link.href = `view.php?id=${product.id}`;

        const img = document.createElement('img');
        img.src = product.thumbnail || product.smallThumbnail || 'https://placehold.co/200x300/EFEFEF/AAAAAA?text=No+Image';
        img.alt = product.title || 'Book image';
        img.className = 'card-image';
        img.onerror = function(){
            this.onerror=null;
            this.src='https://placehold.co/200x300/EFEFEF/AAAAAA?text=Image+Error';
        };

        if (includeBadge){
            const imageWrapper = document.createElement('div');
            imageWrapper.className = 'image-wrapper-for-badge';
            const badge = document.createElement('div');
            badge.className = 'image-number-badge';
            badge.textContent = index + 1;
            imageWrapper.appendChild(img);
            imageWrapper.appendChild(badge);
            link.appendChild(imageWrapper);
        }else{
            link.appendChild(img);
        }

        const title = document.createElement('h2');
        title.textContent = product.title || 'Untitled Book';
        link.appendChild(title);
        card.appendChild(link);

        const cardContent = document.createElement('div');
        cardContent.className = 'card-content';

        const author = document.createElement('p');
        author.textContent = product.author || 'Author N/A';
        cardContent.appendChild(author);

        const rating = document.createElement('p');
        if (product.book_rating){
            const numericRating = parseFloat(product.book_rating);
            if (!isNaN(numericRating)){
                rating.textContent = `${numericRating.toFixed(1)}⭐`;
            }else{
                rating.textContent = 'Rating N/A ⭐';
            }
        }else{
            rating.textContent = 'Not Rated ⭐';
        }
        cardContent.appendChild(rating);
        card.appendChild(cardContent);
        return card;
    }

    function displayProducts(container, products, includeBadge = false){
        container.innerHTML = '';
        if (!products || products.length === 0){
            container.innerHTML = '<p>No books found.</p>';
            return;
        }
        products.forEach((product, index) =>{
            const card = createProductCard(product, index, includeBadge);
            container.appendChild(card);
        });
    }

    async function loadFeaturedBooks(){
        if (!featuredBooksContainer) return;
        featuredBooksContainer.innerHTML = '<p>Loading featured books...</p>';
        try{
            const products = await fetchApiData({ type: "GetFeaturedProducts", api_key: apiKey });
            displayProducts(featuredBooksContainer, products, false);
        }catch (error){
            featuredBooksContainer.innerHTML = `<p>Error loading featured books: ${error.message}</p>`;
        }
    }

    async function loadHighestRatedBooks(){
        if (!highestRatedBooksContainer) return;
        highestRatedBooksContainer.innerHTML = '<p>Loading highest rated books...</p>';
        try{
            const products = await fetchApiData({ type: "GetHighestRatedProducts", api_key: apiKey });
            displayProducts(highestRatedBooksContainer, products, true);
        }catch (error){
            highestRatedBooksContainer.innerHTML = `<p>Error loading highest rated books: ${error.message}</p>`;
        }
    }

    loadFeaturedBooks();
    loadHighestRatedBooks();
});