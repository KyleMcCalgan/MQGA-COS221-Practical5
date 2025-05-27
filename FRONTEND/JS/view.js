document.addEventListener('DOMContentLoaded', function () {
    const apiUrl = '../../BACKEND/public/index.php';
    const apiKey = sessionStorage.getItem('api_key');
    const bookImage = document.getElementById('book-image');
    const bookTitle = document.getElementById('book-title');
    const bookAuthor = document.getElementById('book-author');
    const bookPublisher = document.getElementById('book-publisher');
    const bookCategories = document.getElementById('book-categories');
    const bookPageCount = document.getElementById('book-pagecount');
    const bookIsbn13 = document.getElementById('book-isbn13');
    const detailsButton = document.getElementById('details-button');
    const modal = document.getElementById('book-details-modal');
    const closeModal = document.getElementById('close-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalDescription = document.getElementById('modal-description');
    const modalAuthor = document.getElementById('modal-author');
    const modalIsbn13 = document.getElementById('modal-isbn13');
    const modalPublished = document.getElementById('modal-published');
    const modalPublisher = document.getElementById('modal-publisher');
    const modalPageCount = document.getElementById('modal-pagecount');
    const modalMaturity = document.getElementById('modal-maturity');
    const modalLanguage = document.getElementById('modal-language');
    const modalAccessible = document.getElementById('modal-accessible');
    const modalCategories = document.getElementById('modal-categories');
    const rowContainer = document.querySelector('.rowcontainer');
    const reviewButton = document.getElementById('review-button');
    const reviewModal = document.getElementById('review-modal');
    const closeReviewModal = document.getElementById('close-review-modal');
    const reviewForm = document.getElementById('review-form');
    const reviewInput = document.getElementById('review-input');
    const ratingInputs = document.querySelectorAll('input[name="rate"]');
    const reviewMessage = document.getElementById('review-message');
    const statsAvgRating = document.querySelector('.statsavgR');
    const statsNumRatings = document.querySelector('.statsnoR');
    const statsNumReviews = document.querySelector('.statsnoRe');
    const reviewAccordion = document.getElementById('reviewAccordion');
    const sortSelect = document.getElementById('review-sort');
    const prevPageButton = document.getElementById('prev-page');
    const nextPageButton = document.getElementById('next-page');

    let currentPage = 0;
    const reviewsPerPage = 6;
    let allReviews = [];
    let currentSort = 'newest';

    function showUserMessage(element, message, isError = false) {
        element.textContent = message;
        if (isError) {
            element.style.color = 'red';
        } else {
            element.style.color = 'green';
        }
    }

    function getQueryParam(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }

    async function fetchProduct(bookId) {
        const payload = {
            type: 'GetProduct',
            api_key: apiKey,
            book_id: bookId
        };

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload)
            });

            if (!response.ok) {
                let errorData;
                try {
                    errorData = await response.json();
                } catch (e) { }
                const errorMessage = errorData && errorData.message ? errorData.message : `An HTTP error ${response.status} occurred.`;
                throw new Error(errorMessage);
            }

            const result = await response.json();

            if (result.status === 'success' && result.data) {
                return result.data;
            } else {
                const errorMessage = result.message || 'Could not retrieve product details.';
                throw new Error(errorMessage);
            }
        } catch (error) {
            showUserMessage(bookTitle, `Error: ${error.message}`, true);
            console.error('Error fetching product:', error);
            throw error;
        }
    }

    async function fetchReviews(bookId, sort) {
        const payload = {
            type: 'GetBookReviewsRatings',
            api_key: apiKey,
            book_id: bookId,
            sort: sort
        };

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload)
            });

            if (!response.ok) {
                let errorData;
                try {
                    errorData = await response.json();
                } catch (e) { }
                const errorMessage = errorData && errorData.message ? errorData.message : `An HTTP error ${response.status} occurred.`;
                throw new Error(errorMessage);
            }

            const result = await response.json();

            if (result.status === 'success' && result.data) {
                return result.data;
            } else {
                const errorMessage = result.message || 'Could not retrieve reviews and ratings.';
                throw new Error(errorMessage);
            }
        } catch (error) {
            showUserMessage(bookTitle, `Error: ${error.message}`, true);
            console.error('Error fetching reviews:', error);
            throw error;
        }
    }

    async function submitRating(bookId, rating) {
        const payload = {
            type: 'AddUserRating',
            apikey: apiKey,
            book_id: bookId,
            rating: rating
        };

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (result.status === 'success') {
                showUserMessage(reviewMessage, 'Rating submitted successfully!', false);
                return true;
            } else {
                showUserMessage(reviewMessage, `Error: ${result.message || 'Could not submit rating.'}`, true);
                return false;
            }
        } catch (error) {
            showUserMessage(reviewMessage, `Error: ${error.message}`, true);
            console.error('Error submitting rating:', error);
            return false;
        }
    }

    async function submitReview(bookId, review) {
        const payload = {
            type: 'AddUserReview',
            apikey: apiKey,
            book_id: bookId,
            review: review
        };

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (result.status === 'success') {
                showUserMessage(reviewMessage, 'Review submitted successfully!', false);
                return true;
            } else {
                showUserMessage(reviewMessage, `Error: ${result.message || 'Could not submit review.'}`, true);
                return false;
            }
        } catch (error) {
            showUserMessage(reviewMessage, `Error: ${error.message}`, true);
            console.error('Error submitting review:', error);
            return false;
        }
    }

    async function fetchUserRating(bookId) {
        const payload = {
            type: 'GetUserBookRating',
            api_key: apiKey,
            book_id: bookId
        };

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload)
            });

            if (!response.ok) {
                let errorData;
                try {
                    errorData = await response.json();
                } catch (e) { }
                const errorMessage = errorData && errorData.message ? errorData.message : `An HTTP error ${response.status} occurred.`;
                throw new Error(errorMessage);
            }

            const result = await response.json();

            if (result.status === 'success' && result.data) {
                const rating = result.data.rating;
                const yourRating = document.getElementById('your-rating');
                const yourRatingStats = document.querySelector('.your-ratingstats');
                if (rating !== null) {
                    yourRating.textContent = `Your rating: ${rating} ⭐`;
                    yourRatingStats.textContent = `Your rating: ${rating} ⭐`;
                } else {
                    yourRating.textContent = 'Your rating: N/A';
                    yourRatingStats.textContent = 'Your rating: N/A';
                }
            } else {
                throw new Error(result.message || 'Could not retrieve user rating.');
            }
        } catch (error) {
            console.error('Error fetching user rating:', error);
            const yourRating = document.getElementById('your-rating');
            const yourRatingStats = document.querySelector('.your-ratingstats');
            yourRating.textContent = 'N/A';
            yourRatingStats.textContent = 'Your rating: N/A';
        }
    }

    function populateBookDetails(product) {
        bookImage.src = product.thumbnail || product.smallThumbnail || '../Images/notfound.png';
        bookImage.alt = product.title;
        bookImage.onerror = function () {
            this.onerror = null;
            this.src = '../Images/notfound.png';
            this.alt = 'Image failed to load';
        };

        bookTitle.textContent = product.title;
        bookAuthor.textContent = product.author || 'Author unknown';
        bookPublisher.textContent = `Publisher: ${product.publisher}, ${product.publishedDate}`;
        bookCategories.textContent = product.categories && product.categories.length ? 'Genres: ' + product.categories.join(', ') : 'Categories N/A';
        bookPageCount.textContent = `Page count: ${product.pageCount}`;
        bookIsbn13.textContent = `ISBN-13: ${product.isbn13}`;
    }

    function populateModalDetails(product) {
        modalTitle.textContent = product.title || 'Untitled Book';
        modalDescription.textContent = product.description || 'No description available.';
        modalAuthor.textContent = `Author: ${product.author}`;
        modalIsbn13.textContent = `ISBN-13: ${product.isbn13}`;
        modalPublished.textContent = `Published Date: ${product.publishedDate}`;
        modalPublisher.textContent = `Publisher: ${product.publisher}`;
        modalPageCount.textContent = `Page Count: ${product.pageCount}`;
        modalMaturity.textContent = `Maturity Rating: ${product.maturityRating}`;
        modalLanguage.textContent = `Language: ${product.language}`;
        modalAccessible.textContent = `Accessible In: ${product.accessibleIn}`;
        modalCategories.textContent = `${product.categories && product.categories.length ? 'Genres: ' + product.categories.join(', ') : 'N/A'}`;
    }

    function populateStoreRows(stores) {
        // rowContainer.innerHTML = '';
        if (!stores || stores.length === 0) {
            const noStoresMessage = document.createElement('div');
            noStoresMessage.className = 'storerow';
            noStoresMessage.innerHTML = `
                <h2 class="store-name">No stores available</h2>
                <h2 class="store-rating">-</h2>
                <h2 class="store-price">-</h2>
            `;
            rowContainer.appendChild(noStoresMessage);
            return;
        }

        const sortedStores = [...stores].sort((a, b) => {
            const priceA = a.price && !isNaN(parseFloat(a.price)) ? parseFloat(a.price) : Infinity;
            const priceB = b.price && !isNaN(parseFloat(b.price)) ? parseFloat(b.price) : Infinity;
            return priceA - priceB;
        });

        const validStores = sortedStores.filter(store => store.price && !isNaN(parseFloat(store.price)));
        const cheapestStore = validStores.length > 0 ? validStores[0] : null;

        sortedStores.forEach(store => {
            const storeRow = document.createElement('div');
            storeRow.className = `storerow${cheapestStore && store.price === cheapestStore.price ? ' cheapest' : ''}`;
            storeRow.innerHTML = `
                <div class="store-name rowinfo">
                    <a href="${store.domain || '#'}" target="_blank">
                        <img src="${store.logo || '../Images/notfound.png'}" alt="${store.name || 'Store'} Logo" class="store-logo">
                    </a>
                    <h4>${store.name || 'Unknown Store'}</h4>
                </div>
                <h4 class="store-rating rowinfo">${store.rating ? store.rating + ' ⭐' : 'N/A'}</h4>
                <h4 class="store-price rowinfo">${store.price ? 'R' + store.price : 'N/A'}</h4>
            `;
            rowContainer.appendChild(storeRow);
        });
    }

    function populateReviewStats(stats) {
        statsAvgRating.textContent = `Average rating: ${stats.average_rating ? stats.average_rating + '⭐' : 'N/A'}`;
        statsNumRatings.textContent = `Number of ratings: ${stats.number_of_ratings || 0}`;
        statsNumReviews.textContent = `Number of reviews: ${stats.number_of_reviews || 0}`;
    }

    function populateReviews(reviews, page) {
        reviewAccordion.innerHTML = '';
        if (!reviews || reviews.length === 0) {
            const noReviewsMessage = document.createElement('p');
            noReviewsMessage.textContent = 'No reviews available.';
            noReviewsMessage.style.textAlign = 'center';
            reviewAccordion.appendChild(noReviewsMessage);
            prevPageButton.disabled = true;
            nextPageButton.disabled = true;
            return;
        }

        function decodeHtmlEntities(text) {
            if (!text) return text;
            try {
                let decoded = text
                    .replace(/&#39;/g, "'")
                    .replace(/&quot;/g, '"')
                    .replace(/&lt;/g, '<')
                    .replace(/&gt;/g, '>')
                    .replace(/&amp;/g, '&');
                const div = document.createElement('div');
                div.innerHTML = decoded;
                decoded = div.textContent;
                // console.log(`Original: ${text}, Decoded: ${decoded}`);
                return decoded;
            } catch (error) {
                // console.error('Error decoding HTML entities:', error, 'Text:', text);
                return text;
            }
        }

        const start = page * reviewsPerPage;
        const end = start + reviewsPerPage;
        const paginatedReviews = reviews.slice(start, end);

        paginatedReviews.forEach((review, index) => {
            const accordionItem = document.createElement('div');
            accordionItem.className = 'accordion-item';

            const header = document.createElement('h2');
            header.className = 'accordion-header';
            header.id = `heading${review.review_id}`;

            const button = document.createElement('button');
            button.className = `accordion-button${index === 0 ? '' : ' collapsed'}`;
            button.type = 'button';
            button.setAttribute('data-bs-toggle', 'collapse');
            button.setAttribute('data-bs-target', `#collapse${review.review_id}`);
            button.setAttribute('aria-expanded', index === 0 ? 'true' : 'false');
            button.setAttribute('aria-controls', `collapse${review.review_id}`);
            button.textContent = `${review.user_name}: ${review.rating ? review.rating + ' ⭐' : 'No rating'}`;

            header.appendChild(button);

            const collapseDiv = document.createElement('div');
            collapseDiv.id = `collapse${review.review_id}`;
            collapseDiv.className = `accordion-collapse collapse${index === 0 ? ' show' : ''}`;
            collapseDiv.setAttribute('aria-labelledby', `heading${review.review_id}`);
            collapseDiv.setAttribute('data-bs-parent', '#reviewAccordion');

            const accordionBody = document.createElement('div');
            accordionBody.className = 'accordion-body';
            accordionBody.textContent = decodeHtmlEntities(review.review) || 'No review text provided.';

            collapseDiv.appendChild(accordionBody);

            accordionItem.appendChild(header);
            accordionItem.appendChild(collapseDiv);

            reviewAccordion.appendChild(accordionItem);
        });

        prevPageButton.disabled = page === 0;
        nextPageButton.disabled = end >= reviews.length;
    }

    function showModal() {
        modal.style.display = 'flex';
    }

    function hideModal() {
        modal.style.display = 'none';
    }

    function showReviewModal() {
        reviewModal.style.display = 'flex';
        reviewMessage.textContent = 'If you have an existing rating or review for this book, it will be replaced.';
    }

    function hideReviewModal() {
        reviewModal.style.display = 'none';
        reviewForm.reset();
        reviewMessage.textContent = '';
    }

    async function loadReviews(bookId, sort, page = 0) {
        try {
            const reviewData = await fetchReviews(bookId, sort);
            allReviews = reviewData.reviews || [];
            populateReviews(allReviews, page);
            populateReviewStats(reviewData.stats);
        } catch (error) { }
    }

    async function loadProductDetails() {
        const bookId = getQueryParam('id');
        if (!bookId) {
            showUserMessage(bookTitle, 'Error: No book ID provided.', true);
            return;
        }

        try {
            const product = await fetchProduct(bookId);
            populateBookDetails(product);
            populateModalDetails(product);
            populateStoreRows(product.stores);
            await fetchUserRating(bookId);
            await loadReviews(bookId, currentSort, currentPage);
        } catch (error) { }
    }

    detailsButton.addEventListener('click', showModal);
    closeModal.addEventListener('click', hideModal);
    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            hideModal();
        }
    });

    reviewButton.addEventListener('click', showReviewModal);
    closeReviewModal.addEventListener('click', hideReviewModal);
    reviewModal.addEventListener('click', function (event) {
        if (event.target === reviewModal) {
            hideReviewModal();
        }
    });

    reviewForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        const bookId = getQueryParam('id');
        if (!bookId) {
            showUserMessage(reviewMessage, 'Error: No book ID provided.', true);
            return;
        }

        const reviewText = reviewInput.value.trim();
        const selectedRating = Array.from(ratingInputs).find(input => input.checked)?.value;

        let success = true;

        if (selectedRating) {
            const ratingSuccess = await submitRating(bookId, selectedRating);
            success = success && ratingSuccess;
        }

        if (reviewText) {
            const reviewSuccess = await submitReview(bookId, reviewText);
            success = success && reviewSuccess;
        }

        if (success && (selectedRating || reviewText)) {
            hideReviewModal();
            currentPage = 0;
            await loadReviews(bookId, currentSort, currentPage);
        } else if (!selectedRating && !reviewText) {
            showUserMessage(reviewMessage, 'Error: Please provide a rating or review.', true);
        }
    });

    sortSelect.addEventListener('change', async function () {
        currentSort = sortSelect.value;
        currentPage = 0;
        const bookId = getQueryParam('id');
        if (bookId) {
            await loadReviews(bookId, currentSort, currentPage);
        }
    });

    prevPageButton.addEventListener('click', function () {
        if (currentPage > 0) {
            currentPage--;
            populateReviews(allReviews, currentPage);
        }
    });

    nextPageButton.addEventListener('click', function () {
        if ((currentPage + 1) * reviewsPerPage < allReviews.length) {
            currentPage++;
            populateReviews(allReviews, currentPage);
        }
    });

    const userType = sessionStorage.getItem('user_type');
    if (userType !== 'regular' && reviewButton) reviewButton.remove();
    loadProductDetails();
});