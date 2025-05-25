document.addEventListener('DOMContentLoaded', function (){
    const rangeContainer = document.querySelector('.range');
    const searchBar = document.querySelector('.search-bar'); 
    const sortNameElement = document.getElementById('sort-name');
    const sortAuthElement = document.getElementById('sort-author');
    const sortRatingElement = document.getElementById('sort-rating');
    const genreFilterElement = document.getElementById('filter-genre');

    const apiUrl = '../../BACKEND/public/index.php';
    const apiKey = sessionStorage.getItem('api_key');

    let allProductsArr = []; 
    let currSearchInput = '';
    let currSortName = 'default';
    let currSortAuth = 'default';
    let currSortRating = 'default';
    let currGenre = 'default';
    
    let prevSearchInput = '';
    let prevGenre = 'default';
    let prevSortName = 'default';

    function showUserMessage(container, message, isError = false){
        if (!container) return;
        container.innerHTML = '';
        const p = document.createElement('p');
        p.textContent = message;
        p.style.textAlign = 'center'; 
        if (isError){
            p.style.color = 'red';
        }
        container.appendChild(p);
    }

    async function getGenres(){
        if (!apiKey){
            console.warn('API key not found, cannot fetch genres.');
            genreFilterElement.disabled = true;
            genreFilterElement.innerHTML = '<option value="default">Genre filter unavailable</option>';
            return;
        }
        try{
            const response = await fetch(apiUrl,{
                method: 'POST',
                headers:{ 'Content-Type': 'application/json' },
                body: JSON.stringify({ type: 'GetGenre', api_key: apiKey })
            });
            if (!response.ok) throw new Error(`API error for GetGenre: ${response.status}`);
            const result = await response.json();
            if (result.status === 'success' && result.data && result.data.genres){
                popGenreFilter(result.data.genres);
            } else{
                throw new Error(result.message || 'Failed to parse genres.');
            }
        } catch (error){
            console.error('Error fetching genres:', error);
            genreFilterElement.innerHTML = '<option value="default">Error loading genres</option>';
            genreFilterElement.disabled = true;
        }
    }

    function popGenreFilter(genres){
        genreFilterElement.innerHTML = '<option value="default">All Genres</option>'; 
        genres.forEach(genre =>{
            if (genre.searchable){ 
                const option = document.createElement('option');
                option.value = genre.genre; 
                option.textContent = genre.genre;
                genreFilterElement.appendChild(option);
            }
        });
    }

    async function getProducts(){
        if (!apiKey){
            showUserMessage(rangeContainer, 'Login required to browse products.', true);
            allProductsArr = [];
            return []; 
        }
        showUserMessage(rangeContainer, 'Loading books...');

        const payload ={
            type: "GetAllProducts",
            api_key: apiKey,
            sort: "false" 
        };

        if (currSearchInput){
            payload.title = currSearchInput;
        }
        if (currGenre !== 'default'){
            payload.category = currGenre;
        }
        if (currSortName === 'name_asc'){
            payload.sort = "true";
        }

        try{
            const response = await fetch(apiUrl,{
                method: 'POST',
                headers:{ 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            if (!response.ok){
                let errorData;
                try{ errorData = await response.json(); } catch (e){}
                const DerrorMessage = errorData && errorData.message ? errorData.message : `HTTP error ${response.status} occurred.`;
                throw new Error(DerrorMessage);
            }
            const result = await response.json();
            if (result.status === "success" && result.data){
                allProductsArr = result.data; 
                return result.data;
            } else{
                const DerrorMessage = result.message || "Could not retrieve products due to an unknown API response.";
                throw new Error(DerrorMessage);
            }
        } catch (error){
            showUserMessage(rangeContainer, `Failed to load books: ${error.message}`, true);
            console.error('Error fetching products:', error);
            allProductsArr = []; 
            return []; 
        }
    }

    function applyClientSideSorts(productsToSort){
        let sortedProducts = [...productsToSort];

        if (currSortName === 'name_asc'){ 
             sortedProducts.sort((a, b) => (a.title || '').localeCompare(b.title || ''));
        } else if (currSortName === 'name_desc'){
            sortedProducts.sort((a, b) => (b.title || '').localeCompare(a.title || ''));
        }

        if (currSortAuth === 'author_asc'){
            sortedProducts.sort((a, b) => (a.author || '').localeCompare(b.author || ''));
        } else if (currSortAuth === 'author_desc'){
            sortedProducts.sort((a, b) => (b.author || '').localeCompare(a.author || ''));
        }

        if (currSortRating !== 'default'){
            sortedProducts.sort((a, b) =>{
                const ratingA = parseFloat(a.book_rating);
                const ratingB = parseFloat(b.book_rating);
                const valA = isNaN(ratingA) ? (currSortRating === 'rating_asc' ? Infinity : -Infinity) : ratingA;
                const valB = isNaN(ratingB) ? (currSortRating === 'rating_asc' ? Infinity : -Infinity) : ratingB;
                
                if (currSortRating === 'rating_asc'){
                    return valA - valB;
                } else{
                    return valB - valA;
                }
            });
        }
        return sortedProducts;
    }

    function displayProducts(){
        let productsToConsider = [...allProductsArr];

        //random products
        const randomSubset = [...productsToConsider].sort(() => 0.5 - Math.random()).slice(0, 50);

        let sortedSubsetToDisplay = applyClientSideSorts(randomSubset);

        renderProductCards(sortedSubsetToDisplay);
    }

    function renderProductCards(productsToDisplay){ 
        rangeContainer.innerHTML = ''; 

        if (!productsToDisplay || productsToDisplay.length === 0){
            if (allProductsArr.length === 0 && !currSearchInput && currGenre === 'default' && currSortName === 'default' && currSortAuth === 'default' && currSortRating === 'default'){
                 showUserMessage(rangeContainer, 'No books available at the moment.');
            } else{
                 showUserMessage(rangeContainer, 'No books match your current filters and sort criteria.');
            }
            return;
        }

        productsToDisplay.forEach(product =>{
            const card = document.createElement('div');
            card.className = 'card';

            const link = document.createElement('a');
            link.href = `view.php?id=${encodeURIComponent(product.id || '')}`;

            const img = document.createElement('img');
            img.src = product.thumbnail || product.smallThumbnail || '../Images/notfound.png'; 
            img.alt = product.title || 'Book Cover';
            img.className = 'card-image';
            img.onerror = function (){
                this.onerror = null;
                this.src = '../Images/notfound.jpg'; 
                this.alt = 'Image failed to load';
            };

            const titleElement = document.createElement('h2');
            titleElement.textContent = product.title || 'Untitled';

            link.appendChild(img);
            link.appendChild(titleElement);
            card.appendChild(link);

            const cardContent = document.createElement('div');
            cardContent.className = 'card-content';

            const authorElement = document.createElement('p');
            authorElement.textContent = product.author || 'N/A';

            const ratingElement = document.createElement('p');
            if (product.book_rating){
                const numericRating = parseFloat(product.book_rating);
                ratingElement.textContent = !isNaN(numericRating) ? `${numericRating.toFixed(2)} ⭐` : 'Invalid Rating ⭐';
            } else{
                ratingElement.textContent = 'Not Rated ⭐';
            }

            cardContent.appendChild(authorElement);
            cardContent.appendChild(ratingElement);
            card.appendChild(cardContent);
            rangeContainer.appendChild(card);
        });
    }

    //very cool, basically creates a delay so that the products dont have to update on every change, but rather after a few ms
    function delayTimer(func, delay){
        let timeout;
        return function(...args){
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }
    
    async function handleChange(){
        const newSearchTerm = searchBar ? searchBar.value.trim() : '';
        const newGenre = genreFilterElement.value;
        const newSortName = sortNameElement.value;
        const newSortAuthor = sortAuthElement.value;
        const newSortRating = sortRatingElement.value;

        const searchChange = newSearchTerm !== prevSearchInput;
        const genreChange = newGenre !== prevGenre;
        const sortChange = (newSortName === 'name_asc' && prevSortName !== 'name_asc') || (newSortName !== 'name_asc' && prevSortName === 'name_asc');

        currSearchInput = newSearchTerm;
        currGenre = newGenre;
        currSortName = newSortName;
        currSortAuth = newSortAuthor;
        currSortRating = newSortRating;

        if (searchChange || genreChange || sortChange){
            await getProducts(); 
        }

        displayProducts(); 

        prevSearchInput = newSearchTerm;
        prevGenre = newGenre;
        prevSortName = (newSortName === 'name_asc') ? 'name_asc' : 'default';
    }
    
    const fancyFunction = delayTimer(handleChange, 400);

    async function pageRefresh(){
        if (!apiKey){
            showUserMessage(rangeContainer, 'Please log in to view products.', true);
            if(searchBar) searchBar.disabled = true;
            if(sortNameElement) sortNameElement.disabled = true;
            if(sortAuthElement) sortAuthElement.disabled = true;
            if(sortRatingElement) sortRatingElement.disabled = true;
            if(genreFilterElement) genreFilterElement.disabled = true;
            return;
        }

        if(searchBar) prevSearchInput = searchBar.value.trim(); else prevSearchInput = '';
        if(genreFilterElement) prevGenre = genreFilterElement.value; else prevGenre = 'default';
        if(sortNameElement) prevSortName = (sortNameElement.value === 'name_asc') ? 'name_asc' : 'default'; else prevSortName = 'default';
        
        currSearchInput = prevSearchInput;
        currGenre = prevGenre;
        if(sortNameElement) currSortName = sortNameElement.value; else currSortName = 'default';
        if(sortAuthElement) currSortAuth = sortAuthElement.value; else currSortAuth = 'default';
        if(sortRatingElement) currSortRating = sortRatingElement.value; else currSortRating = 'default';
        
        await getGenres(); 
        await getProducts(); 
        displayProducts();  

        if (searchBar){
            searchBar.addEventListener('input', fancyFunction);
        }
        if(sortNameElement) sortNameElement.addEventListener('change', handleChange);
        if(sortAuthElement) sortAuthElement.addEventListener('change', handleChange);
        if(sortRatingElement) sortRatingElement.addEventListener('change', handleChange);
        if(genreFilterElement) genreFilterElement.addEventListener('change', handleChange);
    }

    pageRefresh();
});