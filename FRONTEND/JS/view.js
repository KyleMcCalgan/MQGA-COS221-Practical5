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

    function showUserMessage(element, message, isError = false) {
        element.textContent = message;
        if (isError) {
            element.style.color = 'red';
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

    function populateBookDetails(product) {
        bookImage.src = product.thumbnail || product.smallThumbnail || '../Images/notfound.png';
        bookImage.alt = product.title || 'Book Cover';
        bookImage.onerror = function () {
            this.onerror = null;
            this.src = '../Images/notfound.png';
            this.alt = 'Image failed to load';
        };

        bookTitle.textContent = product.title || 'Untitled Book';
        bookAuthor.textContent = product.author || 'Author N/A';
        bookPublisher.textContent = `Publisher: ${product.publisher}, ${product.publishedDate}`;
        bookCategories.textContent = product.categories && product.categories.length ? product.categories.join(', ') : 'Categories N/A';
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
        modalCategories.textContent = `Categories: ${product.categories && product.categories.length ? product.categories.join(', ') : 'N/A'}`;
    }

    function showModal() {
        modal.style.display = 'flex';
    }

    function hideModal() {
        modal.style.display = 'none';
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
        } catch (error) {
            // Error message handled in fetchProduct
        }
    }

    detailsButton.addEventListener('click', showModal);
    closeModal.addEventListener('click', hideModal);
    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            hideModal();
        }
    });

    loadProductDetails();
});