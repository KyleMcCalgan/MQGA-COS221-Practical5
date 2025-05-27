document.addEventListener('DOMContentLoaded', function () {
    const apiKey = sessionStorage.getItem('api_key');
    const userType = sessionStorage.getItem('user_type');
    const apiUrl = '../../BACKEND/public/index.php';

    const companySelect = document.getElementById('companySelect');
    const bookViewSelect = document.getElementById('bookViewSelect');
    const prodTable = document.querySelector('.table tbody');
    const tableHeaders = document.querySelector('.table thead tr');
    const editBookModalElement = document.getElementById('editBookModal');
    const editBookModalInstance = new bootstrap.Modal(editBookModalElement);
    const editForm = editBookModalElement.querySelector('form');
    const addBookModalElement = document.getElementById('addBookModal');
    const addBookModalInstance = new bootstrap.Modal(addBookModalElement);
    const addBookForm = addBookModalElement.querySelector('form');
    const searchBar = document.querySelector('.search-bar');
    const catsChecklist = editBookModalElement.querySelector('.dropdown-checklist');

    let currBookDataMod = null;
    let allStores = [];
    let allGenres = [];
    let adminStoreID = null;
    let adminStoreName = null;
    let currentView = 'current';

    if (!apiKey || (userType !== 'admin' && userType !== 'super')) {
        showMessage('Access Denied. Please log in. Redirecting...', true, true);
        setTimeout(() => { window.location.href = 'login.php'; }, 2000);
        return;
    }

    if (prodTable) prodTable.innerHTML = '';

    fetchAndSetAllGenres();

    if (userType === 'super') {
        if (companySelect) {
            companySelect.innerHTML = '<option value="">Loading stores...</option>';
            companySelect.disabled = false;
            companySelect.addEventListener('change', handleSuperAdminStoreSelection);
        }
        if (bookViewSelect) {
            bookViewSelect.disabled = true;
            bookViewSelect.style.display = 'none';
            bookViewSelect.addEventListener('change', handleBookViewChange);
        }
        fetchStoresForSuperAdmin();
    } else if (userType === 'admin') {
        if (companySelect) {
            companySelect.innerHTML = '<option selected disabled>Loading your store...</option>';
            companySelect.disabled = true;
        }
        if (bookViewSelect) {
            bookViewSelect.addEventListener('change', handleBookViewChange);
        }
        initializeAdminViewAndFetchProducts();
    }

    if (searchBar) {
        searchBar.addEventListener('input', handleSearch);
    }

    if (editForm) {
        editForm.addEventListener('submit', handleModalFormSubmit);
    }

    if (addBookForm) {
        addBookForm.addEventListener('submit', handleAddBookFormSubmit);
    }

    async function initializeAdminViewAndFetchProducts() {
        showLoading();
        try {
            const storesResponse = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ type: 'GetStores', api_key: apiKey })
            });
            const storesResult = await storesResponse.json();

            if (storesResult.status === 'success' && storesResult.data) {
                if (typeof storesResult.data === 'object' && storesResult.data !== null && !Array.isArray(storesResult.data) && storesResult.data.name && storesResult.data.store_id) {
                    adminStoreID = storesResult.data.store_id;
                    adminStoreName = storesResult.data.name;
                    if (companySelect && companySelect.disabled) {
                        companySelect.options[0].textContent = decodeHtmlEntities(adminStoreName) || "Your Store";
                    }
                    fetchProductsForAdmin(adminStoreName, searchBar ? searchBar.value.trim() : '');
                } else {
                    hideLoading();
                    showMessage('Could not identify your store information from GetStores response.', true, true);
                }
            } else {
                hideLoading();
                showMessage(storesResult.message || 'Failed to get your store details. Cannot fetch products.', true, true);
            }
        } catch (error) {
            hideLoading();
            showMessage('Error fetching your store details: ' + error.message, true, true);
        }
    }

    async function fetchAndSetAllGenres() {
        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ type: 'GetGenre', api_key: apiKey })
            });
            const result = await response.json();
            if (result.status === 'success' && result.data && result.data.genres) {
                allGenres = result.data.genres;
                populateCategoryChecklist(allGenres);
            } else {
                populateCategoryChecklist([]);
            }
        } catch (error) {
            populateCategoryChecklist([]);
        }
    }
    async function fetchStoresForSuperAdmin() {
        showLoading();
        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ type: 'GetStores', api_key: apiKey })
            });
            const result = await response.json();
            if (result.status === 'success' && result.data) {
                allStores = Array.isArray(result.data) ? result.data : [result.data];
                if (companySelect) {
                    companySelect.innerHTML = '<option value="">All Companies</option>';
                    allStores.forEach(store => {
                        const option = document.createElement('option');
                        option.value = store.store_id;
                        option.textContent = decodeHtmlEntities(store.name);
                        option.dataset.storeName = store.name;
                        companySelect.appendChild(option);
                    });
                }
                fetchProductsForSuperAdmin("", searchBar ? searchBar.value : '');
            } else {
                hideLoading();
                showMessage(result.message || 'Failed to load stores.', true, true);
            }
        } catch (error) {
            hideLoading();
            showMessage('Error fetching stores: ' + error.message, true, true);
        }
    }
    function populateCategoryChecklist(genres) {
        if (!catsChecklist) return;
        catsChecklist.innerHTML = '';
        if (genres.length === 0) {
            catsChecklist.innerHTML = '<p>No genres available.</p>';
            return;
        }
        genres.forEach(genre => {
            const label = document.createElement('label');
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.name = 'categories[]';
            checkbox.value = genre.genre;
            label.appendChild(checkbox);
            label.appendChild(document.createTextNode(` ${genre.genre}`));
            catsChecklist.appendChild(label);
        });
    }



    function handleBookViewChange() {
        currentView = bookViewSelect.value;
        if (userType === 'admin') {
            if (currentView === 'current') {
                companySelect.disabled = true;
                if (adminStoreName) {
                    fetchProductsForAdmin(adminStoreName, searchBar ? searchBar.value.trim() : '');
                }
            } else if (currentView === 'new') {
                companySelect.disabled = true;
                if (adminStoreID) {
                    fetchMissingBooksForAdmin(adminStoreID, searchBar ? searchBar.value.trim() : '');
                } else {
                    showMessage('Store ID not available. Cannot fetch missing books.', true, true);
                }
            }
        } else if (userType === 'super') {
            handleSuperAdminStoreSelection();
        }
    }

    function handleSuperAdminStoreSelection() {
        const selectedOption = companySelect.options[companySelect.selectedIndex];
        const storeId = selectedOption.value;
        const storeName = selectedOption.dataset.storeName;
        if (currentView === 'current') {
            fetchProductsForSuperAdmin(storeName, searchBar ? searchBar.value : '', storeId);
        } else if (currentView === 'new') {
            if (storeId) {
                fetchMissingBooksForAdmin(storeId, searchBar ? searchBar.value : '');
            } else {
                showMessage('Please select a store to view missing books.', true, true);
            }
        }
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


    function handleSearch() {
        const searchTerm = searchBar.value.trim();
        if (userType === 'super') {
            const selectedOption = companySelect.options[companySelect.selectedIndex];
            const storeId = selectedOption.value;
            const storeName = selectedOption.dataset.storeName;
            if (currentView === 'current') {
                fetchProductsForSuperAdmin(storeName, searchTerm, storeId);
            } else if (currentView === 'new') {
                if (storeId) {
                    fetchMissingBooksForAdmin(storeId, searchTerm);
                } else {
                    showMessage('Please select a store to search missing books.', true, true);
                }
            }
        } else {
            if (currentView === 'current' && adminStoreName) {
                fetchProductsForAdmin(adminStoreName, searchTerm);
            } else if (currentView === 'new' && adminStoreID) {
                fetchMissingBooksForAdmin(adminStoreID, searchTerm);
            } else {
                showMessage("Your store information is not yet available. Please wait or reload.", true, true);
            }
        }
    }


    async function fetchProductsForSuperAdmin(storeNameToUse, searchTerm = '', storeIdContext = null) {
        showLoading();
        let payload = { type: '', api_key: apiKey };
        if (searchTerm) payload.title = searchTerm;

        if (storeNameToUse && storeNameToUse) {
            payload.type = 'GetStoreProducts';
            payload.store_name = storeNameToUse;
        } else {
            payload.type = 'GetAllProducts';
        }

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await response.json();
            hideLoading();

            let isErrorState = false;
            let messageToShow = result.message || '';

            if (result.status !== 'success') {
                isErrorState = true;
                messageToShow = messageToShow || 'Failed to fetch products.';
            } else if (!result.data || result.data.length === 0) {
                messageToShow = messageToShow || (payload.type === 'GetStoreProducts' ? 'No products found for this store.' : 'No products found.');
            }
            if (result.message && result.message.toLowerCase().includes('api key is required')) {
                isErrorState = true;
                messageToShow = result.message;
            }

            if (isErrorState) {
                renderProductsTable([], payload.type === 'GetAllProducts', storeIdContext, storeNameToUse);
                showMessage(messageToShow, true, true);
            } else if (result.data) {
                renderProductsTable(result.data, payload.type === 'GetAllProducts', storeIdContext, storeNameToUse);
                if (result.data.length === 0) {
                    showMessage(messageToShow, false, true);
                }
            } else {
                renderProductsTable([], payload.type === 'GetAllProducts', storeIdContext, storeNameToUse);
                showMessage('An unexpected issue occurred while fetching products.', true, true);
            }
        } catch (error) {
            hideLoading();
            renderProductsTable([], payload.type === 'GetAllProducts', storeIdContext, storeNameToUse);
            showMessage('Error fetching products: ' + error.message, true, true);
        }
    }



    async function fetchMissingBooksForAdmin(storeId, searchTerm = '') {
        if (!storeId) {
            showMessage('Store ID is required to fetch missing books.', true, true);
            return;
        }
        showLoading();
        const payload = {
            type: 'GetStoreMissingBooks',
            api_key: apiKey,
            store_id: storeId
        };
        if (searchTerm) payload.title = searchTerm;

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await response.json();
            hideLoading();

            let isErrorState = false;
            let messageToShow = result.message || '';

            if (result.status !== 'success') {
                isErrorState = true;
                messageToShow = messageToShow || 'Failed to fetch missing books.';
            } else if (!result.data || result.data.length === 0) {
                messageToShow = messageToShow || 'No missing books found for this store.';
            }

            if (isErrorState) {
                renderMissingBooksTable([], storeId);
                showMessage(messageToShow, true, true);
            } else if (result.data) {
                renderMissingBooksTable(result.data, storeId);
                if (result.data.length === 0) {
                    showMessage(messageToShow, false, true);
                }
            } else {
                renderMissingBooksTable([], storeId);
                showMessage('An unexpected issue occurred while fetching missing books.', true, true);
            }
        } catch (error) {
            hideLoading();
            renderMissingBooksTable([], storeId);
            showMessage('Error fetching missing books: ' + error.message, true, true);
        }
    }


    function renderMissingBooksTable(books, storeId) {
        if (!prodTable) return;
        prodTable.innerHTML = '';

        const expectedColumnCount = 5;

        if (!books || books.length === 0) {
            const colCount = tableHeaders ? tableHeaders.cells.length : expectedColumnCount;
            prodTable.innerHTML = `<tr><td colspan="${colCount}">No missing books found.</td></tr>`;
            return;
        }

        books.forEach(book => {
            const row = prodTable.insertRow();

            row.insertCell().textContent = decodeHtmlEntities(book.title) || 'N/A';
            row.insertCell().textContent = decodeHtmlEntities(book.author) || 'N/A';
            row.insertCell().textContent = 'N/A';
            row.insertCell().textContent = 'N/A';

            const actionsCell = row.insertCell();
            actionsCell.style.whiteSpace = 'nowrap';

            const addButton = document.createElement('button');
            addButton.className = 'btn btn-sm btn-outline-success';
            addButton.textContent = 'Add to My Store';
            addButton.type = 'button';
            addButton.addEventListener('click', () => handleAddClick(book.id, storeId));
            actionsCell.appendChild(addButton);
        });
    }

    function renderProductsTable(products, isAllCompaniesView, currentStoreId, currentStoreNameToDisplay) {
        if (!prodTable) return;
        prodTable.innerHTML = '';

        const expectedColumnCount = 5;

        if (!products || products.length === 0) {
            const colCount = tableHeaders ? tableHeaders.cells.length : expectedColumnCount;
            prodTable.innerHTML = `<tr><td colspan="${colCount}">No products found.</td></tr>`;
            return;
        }

        products.forEach(book => {
            const row = prodTable.insertRow();

            row.insertCell().textContent = decodeHtmlEntities(book.title) || 'N/A';
            row.insertCell().textContent = decodeHtmlEntities(book.author) || 'N/A';

            let ratingDisplay = 'N/A';

            let numericRating;
            if (isAllCompaniesView && book.book_rating !== null && book.book_rating !== undefined) {
                numericRating = parseFloat(book.book_rating);
                if (!isNaN(numericRating)) {
                    ratingDisplay = numericRating.toFixed(2);
                }
            } else if (!isAllCompaniesView && book.rating !== null && book.rating !== undefined) {
                numericRating = parseFloat(book.rating);
                if (!isNaN(numericRating)) {
                    ratingDisplay = numericRating.toFixed(2);
                }

            }

            row.insertCell().textContent = decodeHtmlEntities(ratingDisplay);

            let priceDisplay = 'N/A';

            let numericPrice;
            if (!isAllCompaniesView && book.price !== null && book.price !== undefined) {
                numericPrice = parseFloat(book.price);
                if (!isNaN(numericPrice)) {
                    priceDisplay = numericPrice.toFixed(2);
                }

            }
            row.insertCell().textContent = decodeHtmlEntities(priceDisplay);

            const actionsCell = row.insertCell();
            actionsCell.style.whiteSpace = 'nowrap';

            const editButton = document.createElement('button');
            editButton.className = 'btn btn-sm btn-outline-primary me-2';
            editButton.textContent = 'Edit';
            editButton.type = 'button';
            let storeContextIdForEdit = userType === 'admin' ? adminStoreID : currentStoreId;
            editButton.addEventListener('click', () => handleEditClick(book.id, storeContextIdForEdit, book.price, book.rating));
            actionsCell.appendChild(editButton);

            const deleteButton = document.createElement('button');
            deleteButton.className = 'btn btn-sm btn-outline-danger';
            if (userType === 'admin') {
                deleteButton.textContent = 'Delete Price/Rating';
            } else {
                deleteButton.textContent = 'Delete';
            }
            deleteButton.type = 'button';
            let storeContextIdForDelete = userType === 'admin' ? adminStoreID : currentStoreId;
            let storeContextNameForDelete = userType === 'admin' ? adminStoreName : currentStoreNameToDisplay;
            deleteButton.addEventListener('click', () => handleDeleteClick(book.id, storeContextIdForDelete, storeContextNameForDelete, isAllCompaniesView));
            actionsCell.appendChild(deleteButton);
        });
    }


    async function handleAddBookFormSubmit(event) {
        event.preventDefault();
        if (!currBookDataMod || !currBookDataMod.id || !currBookDataMod.adminContextStoreId) {
            showMessage('No book or store selected for adding.', true, false, 'modal');
            return;
        }

        const formData = new FormData(addBookForm);
        const price = document.getElementById('add_store_price').value;
        const rating = document.getElementById('add_store_rating').value;
        if (isNaN(price) || price < 0) {
            showMessage('Price must be a valid number greater than or equal to 0.', true, false, 'modal');
            return;
        }
        if (isNaN(rating) || rating < 0 || rating > 5) {
            showMessage('Rating must be a valid number between 0.0 and 5.0.', true, false, 'modal');
            return;
        }

        const payload = {
            type: 'AddInfoForStore',
            api_key: apiKey,
            book_id: currBookDataMod.id.toString(),
            store_id: currBookDataMod.adminContextStoreId.toString(),
            Price: price,
            Rating: rating
        };

        showLoading('modal');

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await response.json();
            hideLoading('modal');

            if (result.status === 'success') {
                showMessage(result.data || 'Book added to store successfully!', false, false, 'modal');
                addBookModalInstance.hide();
                if (userType === 'admin') {
                    fetchMissingBooksForAdmin(adminStoreID, searchBar ? searchBar.value.trim() : '');
                } else if (userType === 'super') {
                    const selectedOption = companySelect.options[companySelect.selectedIndex];
                    fetchMissingBooksForAdmin(selectedOption.value, searchBar ? searchBar.value.trim() : '');
                }
            } else {
                showMessage(result.message || 'Failed to add book to store.', true, false, 'modal');
            }
        } catch (error) {
            hideLoading('modal');
            showMessage(`Error adding book to store: ${error.message}`, true, false, 'modal');
        }
    }

    async function handleEditClick(bookId, storeContextId, storeContextPrice, storeContextRating) {
        showLoading();
        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ type: 'GetProduct', api_key: apiKey, book_id: bookId.toString() })
            });
            const result = await response.json();
            hideLoading();

            if (result.status === 'success' && result.data) {
                currBookDataMod = result.data;
                currBookDataMod.adminContextStoreId = userType === 'admin' ? adminStoreID : storeContextId;

                if (userType === 'admin' && adminStoreID && currBookDataMod.stores) {
                    const adminStoreDetails = currBookDataMod.stores.find(s => s.id && s.id.toString() === adminStoreID.toString());
                    if (adminStoreDetails) {
                        currBookDataMod.adminContextStorePrice = adminStoreDetails.price;
                        currBookDataMod.adminContextStoreRating = adminStoreDetails.rating;
                    } else {
                        currBookDataMod.adminContextStorePrice = storeContextPrice;
                        currBookDataMod.adminContextStoreRating = storeContextRating;
                    }
                } else {
                    currBookDataMod.adminContextStorePrice = storeContextPrice;
                    currBookDataMod.adminContextStoreRating = storeContextRating;
                }
                populateAndConfigureModal(currBookDataMod);
                editBookModalInstance.show();
            } else {
                showMessage(result.message || 'Failed to fetch book details.', true, true);
            }
        } catch (error) {
            hideLoading();
            showMessage('Error fetching book details: ' + error.message, true, true);
        }
    }

    function handleAddClick(bookId, storeId) {
        currBookDataMod = { id: bookId, adminContextStoreId: storeId };
        addBookModalInstance.show();
    }



    function populateAndConfigureModal(book) {
        editForm.querySelector('[name="title"]').value = book.title || '';
        editForm.querySelector('[name="author"]').value = book.author || '';
        editForm.querySelector('[name="publisher"]').value = book.publisher || '';
        editForm.querySelector('[name="publishedDate"]').value = book.publishedDate || '';
        editForm.querySelector('[name="description"]').value = book.description || '';
        editForm.querySelector('[name="pageCount"]').value = book.pageCount || '';
        editForm.querySelector('[name="maturityRating"]').value = book.maturityRating || 'NOT_MATURE';
        editForm.querySelector('[name="language"]').value = book.language || '';
        editForm.querySelector('[name="imageLink"]').value = book.thumbnail || book.smallThumbnail || '';
        editForm.querySelector('[name="accessibleIn"]').value = book.accessibleIn || '';
        editForm.querySelector('[name="ratingsCount"]').value = book.ratingsCount !== undefined ? book.ratingsCount : '';
        editForm.querySelector('[name="isbn13"]').value = book.isbn13 || '';

        const categoryCheckboxesInModal = catsChecklist.querySelectorAll('input[type="checkbox"]');
        categoryCheckboxesInModal.forEach(checkbox => {
            checkbox.checked = book.categories && book.categories.includes(checkbox.value);
        });

        const masterFields = ['title', 'author', 'publisher', 'publishedDate', 'description', 'pageCount', 'maturityRating', 'language', 'imageLink', 'accessibleIn', 'ratingsCount', 'isbn13'];
        const adminSpecificFieldElements = editBookModalElement.querySelectorAll('.admin-specific-field');
        const storePriceField = editForm.querySelector('[name="store_price"]');
        const storeRatingField = editForm.querySelector('[name="store_rating"]');

        if (userType === 'super') {
            masterFields.forEach(fieldName => {
                const field = editForm.querySelector(`[name="${fieldName}"]`);
                if (field) field.disabled = false;
            });
            categoryCheckboxesInModal.forEach(cb => cb.disabled = false);
            adminSpecificFieldElements.forEach(el => el.style.display = 'none');
            if (storePriceField) storePriceField.value = '';
            if (storeRatingField) storeRatingField.value = '';
        } else if (userType === 'admin') {
            masterFields.forEach(fieldName => {
                const field = editForm.querySelector(`[name="${fieldName}"]`);
                if (field) field.disabled = true;
            });
            categoryCheckboxesInModal.forEach(cb => cb.disabled = true);
            adminSpecificFieldElements.forEach(el => el.style.display = 'block');
            if (storePriceField) storePriceField.value = book.adminContextStorePrice !== undefined ? book.adminContextStorePrice : '';
            if (storeRatingField) storeRatingField.value = book.adminContextStoreRating !== undefined ? book.adminContextStoreRating : '';
        }
    }

    async function handleModalFormSubmit(event) {
        event.preventDefault();
        if (!currBookDataMod || !currBookDataMod.id) {
            showMessage('No book selected for update or book ID is missing.', true, false, 'modal');
            return;
        }

        const formData = new FormData(editForm);
        let payload = { api_key: apiKey };

        showLoading('modal');

        if (userType === 'super') {
            payload.type = 'UpdateProductSuper';
            const bookUpdateData = { id: currBookDataMod.id };
            ['tempID', 'title', 'description', 'isbn13', 'publishedDate', 'publisher', 'author', 'pageCount', 'maturityRating', 'language', 'smallThumbnail', 'thumbnail', 'accessibleIn', 'ratingsCount']
                .forEach(key => {
                    if (key === 'smallThumbnail' || key === 'thumbnail') {
                        bookUpdateData[key] = formData.get('imageLink') || (key === 'thumbnail' ? currBookDataMod.thumbnail : currBookDataMod.smallThumbnail);
                    } else if (formData.has(key) || editForm.querySelector(`[name="${key}"]`)) {
                        const value = formData.get(key);
                        if (key === 'pageCount' || key === 'ratingsCount') {
                            bookUpdateData[key] = (value !== null && value !== '') ? parseInt(value) : null;
                        } else {
                            bookUpdateData[key] = value;
                        }
                    }
                });

            const selectedCategories = [];
            if (catsChecklist) {
                const categoryCheckboxes = catsChecklist.querySelectorAll('input[type="checkbox"]:checked');
                categoryCheckboxes.forEach(checkbox => {
                    const genreObj = allGenres.find(g => g.genre === checkbox.value);
                    if (genreObj && genreObj.category_id) {
                        selectedCategories.push(genreObj.category_id);
                    }
                });
            }
            bookUpdateData.categories = selectedCategories;
            payload.Book = bookUpdateData;

        } else if (userType === 'admin') {
            payload.type = 'AddInfoForStore';
            payload.book_id = currBookDataMod.id;
            payload.store_id = adminStoreID;
            payload.Price = parseFloat(formData.get('store_price'));
            payload.Rating = parseFloat(formData.get('store_rating'));
            if (!adminStoreID) {
                hideLoading('modal');
                showMessage('Your store ID is not identified. Cannot update price/rating.', true, false, 'modal');
                return;
            }
            if (isNaN(payload.Price) || payload.Price < 0) {
                hideLoading('modal');
                showMessage('Price must be a valid number greater than or equal to 0.', true, false, 'modal');
                return;
            }
            if (isNaN(payload.Rating) || payload.Rating < 0 || payload.Rating > 5) {
                hideLoading('modal');
                showMessage('Rating must be a valid number between 0.0 and 5.0.', true, false, 'modal');
                return;
            }
        } else {
            hideLoading('modal');
            showMessage('Invalid user type for this action.', true, false, 'modal');
            return;
        }

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await response.json();
            hideLoading('modal');

            if (result.status === 'success') {
                showMessage((userType === 'super' ? (result.data || 'Book updated successfully!') : (result.data || 'Store information updated successfully!')), false, false, 'modal');
                editBookModalInstance.hide();
                if (userType === 'super') {
                    const selectedOption = companySelect.options[companySelect.selectedIndex];
                    fetchProductsForSuperAdmin(selectedOption.dataset.storeName, searchBar ? searchBar.value : '', selectedOption.value);
                } else {
                    fetchProductsForAdmin(adminStoreName, searchBar ? searchBar.value : '');
                }
            } else {
                showMessage(result.message || 'Failed to update.', true, false, 'modal');
            }
        } catch (error) {
            hideLoading('modal');
            showMessage(`Error during update: ${error.message}`, true, false, 'modal');
        }
    }



    function showLoading(context = 'page') {
        let container = document.body;
        if (context === 'modal' && editBookModalElement) {
            container = editBookModalElement.querySelector('.modal-content') || editBookModalElement;
        }
        const existingOverlay = container.querySelector('.loading-overlay-custom');
        if (existingOverlay) existingOverlay.remove();

        const loadingDiv = document.createElement('div');
        const currentPosition = window.getComputedStyle(container).position;
        if (currentPosition === 'static') {
            container.style.position = 'relative';
        }
        container.appendChild(loadingDiv);
    }
    async function handleDeleteClick(bookId, storeId, storeName, isAllCompaniesView) {
        let confirmMessage = '';
        let payload = { api_key: apiKey, book_id: bookId };

        if (userType === 'super' && isAllCompaniesView) {
            confirmMessage = `Are you sure you want to PERMANENTLY DELETE this book (ID: ${bookId}) from the ENTIRE SYSTEM? This action cannot be undone.`;
            payload.type = 'DeleteProduct';
        } else if (storeId) {
            let actualStoreName = storeName;
            if (userType === 'admin' && adminStoreName && adminStoreID && adminStoreID.toString() === storeId.toString()) {
                actualStoreName = adminStoreName;
            }
            actualStoreName = actualStoreName || (allStores.find(s => s.store_id && s.store_id.toString() === storeId.toString())?.name) || `Store ID ${storeId}`;
            const decodedActualStoreName = actualStoreName;

            if (userType === 'admin') {
                confirmMessage = `Are you sure you want to delete the price and rating for this book (ID: ${bookId}) from your store: ${decodedActualStoreName}?`;
            } else {
                confirmMessage = `Are you sure you want to remove this book (ID: ${bookId}) from the store listing: ${decodedActualStoreName}?`;
            }
            payload.type = 'DeleteStoreProducts';
            payload.store_id = storeId;
        } else {
            showMessage('Cannot determine deletion context. Store ID missing for store-specific deletion.', true, true);
            return;
        }

        if (!confirm(confirmMessage)) return;

        showLoading();
        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await response.json();
            hideLoading();

            if (result.status === 'success') {
                showMessage(result.data.message || 'Deletion successful!', false, true);
                if (userType === 'super') {
                    const selectedOption = companySelect.options[companySelect.selectedIndex];
                    fetchProductsForSuperAdmin(selectedOption.dataset.storeName, searchBar ? searchBar.value : '', selectedOption.value);
                } else {
                    fetchProductsForAdmin(adminStoreName, searchBar ? searchBar.value : '');
                }
            } else {
                showMessage(result.message || 'Failed to delete.', true, true);
            }
        } catch (error) {
            hideLoading();
            showMessage(`Error during deletion: ${error.message}`, true, true);
        }
    }

    async function fetchProductsForAdmin(storeNameToUse, searchTerm = '') {
        if (!document.body.querySelector('.loading-overlay-custom') && !storeNameToUse && userType === 'admin') {
            if (!adminStoreName) {
                showMessage("Your store details are not available. Cannot fetch products.", true, true);
                hideLoading();
                return;
            }
            storeNameToUse = adminStoreName;
        }
        if (!document.body.querySelector('.loading-overlay-custom')) {
            showLoading();
        }

        const payload = {
            type: 'GetStoreProducts',
            api_key: apiKey,
            store_name: storeNameToUse
        };
        if (searchTerm) payload.title = searchTerm;

        if (!storeNameToUse && userType === 'admin') {
            hideLoading();
            showMessage("Admin store name is required to fetch products.", true, true);
            renderProductsTable([], false, adminStoreID, null);
            return;
        }

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await response.json();
            hideLoading();

            let isErrorState = false;
            let messageToShow = result.message || '';

            if (result.status !== 'success') {
                isErrorState = true;
                messageToShow = messageToShow || 'Failed to fetch your store products.';
            } else if (!result.data) {
                isErrorState = true;
                messageToShow = messageToShow || 'Received success status but no product data.';
            } else if (result.data.length === 0) {
                messageToShow = messageToShow || 'No products found for your store.';
            }
            if (result.message && result.message.toLowerCase().includes('api key is required')) {
                isErrorState = true;
                messageToShow = result.message;
            }

            if (isErrorState) {
                renderProductsTable([], false, adminStoreID, storeNameToUse);
                showMessage(messageToShow, true, true);
            } else if (result.data) {
                renderProductsTable(result.data, false, adminStoreID, storeNameToUse);
                if (result.data.length === 0) {
                    showMessage(messageToShow, false, true);
                }
            } else {
                renderProductsTable([], false, adminStoreID, storeNameToUse);
                showMessage('An unexpected issue occurred while fetching your store products.', true, true);
            }
        } catch (error) {
            hideLoading();
            renderProductsTable([], false, adminStoreID, storeNameToUse);
            showMessage('Error fetching your store products: ' + error.message, true, true);
        }
    }
    
    function hideLoading(context = 'page') {
        let container = document.body;
        if (context === 'modal' && editBookModalElement) {
            container = editBookModalElement.querySelector('.modal-content') || editBookModalElement;
        }
        const loadingOverlay = container.querySelector('.loading-overlay-custom');
        if (loadingOverlay) {
            loadingOverlay.remove();
        }
    }

    function showMessage(message, isError, isPageLevelAlert = true, context = 'page') {
        const messageToDisplay = (typeof message === 'string') ? message : ((message && message.message) ? message.message : 'An unknown error occurred.');

        if (isError || (messageToDisplay && typeof messageToDisplay === 'string' && messageToDisplay.toLowerCase().includes('success'))) {
            if (isPageLevelAlert) {
                alert((isError ? 'Error: ' : '') + messageToDisplay);
            }
        }
        if (context === 'modal') {
            let modalElement = editBookModalElement;
            if (addBookModalElement && addBookModalElement.classList.contains('show')) {
                modalElement = addBookModalElement;
            }
            const modalFooter = modalElement.querySelector('.modal-footer');
            let msgElem = modalFooter.querySelector('.modal-message-custom');
            if (!msgElem) {
                msgElem = document.createElement('div');
                msgElem.className = 'modal-message-custom w-100 mt-2 text-center';
                if (modalFooter.firstChild) modalFooter.insertBefore(msgElem, modalFooter.firstChild);
                else modalFooter.appendChild(msgElem);
            }
            msgElem.textContent = decodeHtmlEntities(messageToDisplay);
            msgElem.style.color = 'white';
            msgElem.style.backgroundColor = isError ? 'rgba(220, 53, 69, 0.9)' : 'rgba(25, 135, 84, 0.9)';
            msgElem.style.padding = '10px';
            msgElem.style.borderRadius = '5px';
            msgElem.style.marginBottom = '10px';

            setTimeout(() => { if (msgElem) msgElem.remove(); }, 5000);
        } else if (!isError && !(messageToDisplay && typeof messageToDisplay === 'string' && messageToDisplay.toLowerCase().includes('success')) && isPageLevelAlert) {
        }
    }

    const style = document.createElement('style');
    style.textContent = `
        .loading-overlay-custom{
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(255, 255, 255, 0.85); display: flex;
            flex-direction: column; justify-content: center; align-items: center;
            z-index: 1065;
        }
        .loading-overlay-custom .spinner{
            border: 5px solid #f3f3f3; border-top: 5px solid #3498db;
            border-radius: 50%; width: 50px; height: 50px; animation: spin 1s linear infinite;
        }
        .loading-overlay-custom p{ margin-top: 15px; font-size: 1.1rem; color: #333; }
        @keyframes spin{ 0%{ transform: rotate(0deg); } 100%{ transform: rotate(360deg); } }
    `;
    document.head.appendChild(style);
});