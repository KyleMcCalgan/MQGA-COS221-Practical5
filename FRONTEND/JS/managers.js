document.addEventListener('DOMContentLoaded', () => {
    const apiUrl = '../../BACKEND/public/index.php';
    const apiKey = sessionStorage.getItem('api_key');
    const userType = sessionStorage.getItem('user_type');
    const storeSelector = document.getElementById('storeSelector');
    const deleteStoreButton = document.getElementById('delete-store');
    const addAdminButton = document.getElementById('add-admin');
    const addStoreButton = document.getElementById('add-store');
    const mantopMessage = document.getElementById('mantop-message');
    const addAdminModal = document.getElementById('add-admin-modal');
    const addStoreModal = document.getElementById('add-store-modal');
    const deleteStoreModal = document.getElementById('delete-store-modal');
    const deleteAdminModal = document.getElementById('delete-admin-modal');
    const editNameModal = document.getElementById('edit-name-modal');
    const editLogoModal = document.getElementById('edit-logo-modal');
    const editDomainModal = document.getElementById('edit-domain-modal');
    const editTypeModal = document.getElementById('edit-type-modal');
    const closeAddAdminModal = document.getElementById('close-add-admin-modal');
    const closeAddStoreModal = document.getElementById('close-add-store-modal');
    const closeDeleteStoreModal = document.getElementById('close-delete-store-modal');
    const closeDeleteAdminModal = document.getElementById('close-delete-admin-modal');
    const closeEditNameModal = document.getElementById('close-edit-name-modal');
    const closeEditLogoModal = document.getElementById('close-edit-logo-modal');
    const closeEditDomainModal = document.getElementById('close-edit-domain-modal');
    const closeEditTypeModal = document.getElementById('close-edit-type-modal');
    const cancelDeleteStore = document.getElementById('cancel-delete-store');
    const confirmDeleteStore = document.getElementById('confirm-delete-store');
    const cancelDeleteAdmin = document.getElementById('cancel-delete-admin');
    const confirmDeleteAdmin = document.getElementById('confirm-delete-admin');
    const deleteStoreMessage = document.getElementById('delete-store-message');
    const deleteAdminMessage = document.getElementById('delete-admin-message');
    const addAdminForm = document.getElementById('add-admin-form');
    const addStoreForm = document.getElementById('add-store-form');
    const editNameForm = document.getElementById('edit-name-form');
    const editLogoForm = document.getElementById('edit-logo-form');
    const editDomainForm = document.getElementById('edit-domain-form');
    const editTypeForm = document.getElementById('edit-type-form');
    const adminMessage = document.getElementById('admin-message');
    const storeMessage = document.getElementById('store-message');
    const editNameMessage = document.getElementById('edit-name-message');
    const editLogoMessage = document.getElementById('edit-logo-message');
    const editDomainMessage = document.getElementById('edit-domain-message');
    const editTypeMessage = document.getElementById('edit-storetype-message');
    const adminsContainer = document.querySelector('.adminscont');

    let stores = []; 
    let storeIdToDelete = null; 
    let adminIdToDelete = null; 

    function showUserMessage(element, message, isError = false) {
        if (!element) {
            // console.error('Message element is undefined');
            return;
        }
        element.textContent = message;
        element.style.color = isError ? 'red' : 'green';
        element.style.fontSize = '14px';
        element.style.marginBottom = '10px';
        element.style.display = 'block';
        setTimeout(() => {
            element.textContent = '';
            element.style.display = 'none';
        }, 1000); 
    }

    async function fetchStores() {
        const payload = {
            type: 'GetStores',
            api_key: apiKey
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
                throw new Error(`HTTP error ${response.status}`);
            }

            const result = await response.json();
            if (result.status === 'success' && result.data) {
                stores = Array.isArray(result.data) ? result.data : [result.data];
                populateStoreDropdown();
            } else {
                throw new Error(result.message || 'Could not retrieve stores.');
            }
        } catch (error) {
            // console.error('Error fetching stores:', error);
            showUserMessage(mantopMessage, `Error: ${error.message}`, true);
        }
    }

    function populateAdminsContainer(admins) {
        if (!adminsContainer) {
            // console.warn('Admins container not found');
            return;
        }

        adminsContainer.innerHTML = ''; 

        if (!admins || admins.length === 0) {
            const noAdmins = document.createElement('p');
            noAdmins.textContent = 'No admins found for this store.';
            noAdmins.classList.add('no-admins');
            adminsContainer.appendChild(noAdmins);
            return;
        }

        admins.forEach((admin, index) => {
            const storeRow = document.createElement('div');
            storeRow.classList.add('storerow');

            const amount = document.createElement('h3');
            amount.classList.add('amount');
            amount.textContent = index + 1;
            storeRow.appendChild(amount);

            const adName = document.createElement('h3');
            adName.classList.add('adname');
            adName.textContent = admin.name;
            storeRow.appendChild(adName);

            const adEmail = document.createElement('h3');
            adEmail.classList.add('ademail');
            adEmail.textContent = admin.email;
            storeRow.appendChild(adEmail);

            if (userType === 'super') {
                const deleteButton = document.createElement('button');
                deleteButton.classList.add('normalbutton', 'blackbutton', 'adrow', 'delete-admin-btn');
                deleteButton.textContent = 'delete';
                deleteButton.dataset.userId = admin.id;
                deleteButton.addEventListener('click', () => {
                    showDeleteAdminModal(admin.id, admin.name);
                });
                storeRow.appendChild(deleteButton);
            }

            adminsContainer.appendChild(storeRow);
        });
    }

    async function removeStoreAdmin(userId) {
        const payload = {
            type: 'RemoveStoreAdmin',
            api_key: apiKey,
            user_id: parseInt(userId)
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
                showUserMessage(mantopMessage, 'Admin removed successfully.', false);
                await fetchStores();
            } else {
                throw new Error(result.message || 'Could not remove admin.');
            }
        } catch (error) {
            // console.error('Error removing admin:', error);
            showUserMessage(mantopMessage, `Error: ${error.message}`, true);
        }
    }

    function showDeleteAdminModal(userId, adminName) {
        adminIdToDelete = userId;
        deleteAdminMessage.textContent = `Are you sure you want to delete ${adminName}?`;
        deleteAdminModal.style.display = 'flex';
    }

    function hideDeleteAdminModal() {
        deleteAdminModal.style.display = 'none';
        deleteAdminMessage.textContent = '';
        adminIdToDelete = null;
    }

    function populateStoreDropdown() {
        storeSelector.innerHTML = '<option value="">Select a Store</option>';
        if (stores && Array.isArray(stores) && stores.length > 0) {
            stores.forEach(store => {
                const option = document.createElement('option');
                option.value = store.store_id;
                option.textContent = store.name;
                storeSelector.appendChild(option);
            });
            if (userType === 'admin' && stores.length > 0) {
                storeSelector.value = stores[0].store_id;
                const changeEvent = new Event('change');
                storeSelector.dispatchEvent(changeEvent);
            }
        } else {
            // console.warn('No valid stores to populate dropdown');
        }
        updateStoreInfo();
    }

    function updateStoreInfo() {
        const storeId = storeSelector.value;
        const store = stores.find(s => s.store_id == storeId);
        const fields = {
            'display-name': store ? store.name : 'Select a store',
            'display-logo': store ? store.logo : 'N/A',
            'display-domain': store ? store.domain : 'N/A',
            'display-type': store ? store.type : 'N/A'
        };

        Object.keys(fields).forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = fields[id] || 'N/A';
            } else {
                // console.warn(`Element with id ${id} not found`);
            }
        });

        const heading = document.querySelector('.booknameheading');
        if (heading) {
            heading.textContent = store ? store.name : 'Select a Store';
        } else {
            // console.warn('Heading with class booknameheading not found');
        }

        populateAdminsContainer(store ? store.admins : []);
    }

    async function updateStore(storeId, field, value) {
        const payload = {
            type: 'UpdateStore',
            api_key: apiKey,
            store_id: parseInt(storeId)
        };
        payload[field] = value;

        const messageField = field === 'storetype' ? 'type' : field;
        const messageElement = window[`edit-${messageField}-message`];

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
                showUserMessage(messageElement, result.data.message, false);
                await fetchStores();
                setTimeout(window[`hideEdit${messageField.charAt(0).toUpperCase() + messageField.slice(1)}Modal`], 1500);
            } else {
                throw new Error(result.message || `Could not update ${field}.`);
            }
        } catch (error) {
            // console.error(`Error updating ${field}:`, error);
            showUserMessage(messageElement, `Error: ${error.message}`, true);
        }
    }

    async function deleteStore(storeId) {
        const payload = {
            type: 'DeleteStore',
            api_key: apiKey,
            store_id: storeId
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
                showUserMessage(mantopMessage, result.data.message, false);
                await fetchStores();
                storeSelector.value = '';
                updateStoreInfo();
            } else {
                throw new Error(result.message || 'Could not delete store.');
            }
        } catch (error) {
            // console.error('Error deleting store:', error);
            showUserMessage(mantopMessage, `Error: ${error.message}`, true);
        }
    }

    async function addStoreAdmin(email, password, storeId) {
        const payload = {
            type: 'AddStoreAdmin',
            api_key: apiKey,
            email,
            password,
            store_id: parseInt(storeId)
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
                showUserMessage(adminMessage, result.data.message, false);
                setTimeout(hideAddAdminModal, 1500);
                await fetchStores();
            } else {
                throw new Error(result.message || 'Could not add admin.');
            }
        } catch (error) {
            // console.error('Error adding admin:', error);
            showUserMessage(adminMessage, `Error: ${error.message}`, true);
        }
    }

    async function addStore(name) {
        const payload = {
            type: 'AddStore',
            api_key: apiKey,
            name
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
                showUserMessage(storeMessage, result.data.message, false);
                await fetchStores();
                setTimeout(hideAddStoreModal, 1500);
            } else {
                throw new Error(result.message || 'Could not add store.');
            }
        } catch (error) {
            // console.error('Error adding store:', error);
            showUserMessage(storeMessage, `Error: ${error.message}`, true);
        }
    }

    function showAddAdminModal() {
        if (!storeSelector.value) {
            showUserMessage(mantopMessage, 'Please select a store first.', true);
            return;
        }
        addAdminModal.style.display = 'flex';
        adminMessage.textContent = '';
        addAdminForm.reset();
    }

    function hideAddAdminModal() {
        addAdminModal.style.display = 'none';
        addAdminForm.reset();
        adminMessage.textContent = '';
    }

    function showAddStoreModal() {
        addStoreModal.style.display = 'flex';
        storeMessage.textContent = '';
        addStoreForm.reset();
    }

    function hideAddStoreModal() {
        addStoreModal.style.display = 'none';
        addStoreForm.reset();
        storeMessage.textContent = '';
    }

    function showDeleteStoreModal(storeId) {
        const store = stores.find(s => s.store_id == storeId);
        if (!store) {
            showUserMessage(mantopMessage, 'Invalid store selected.', true);
            return;
        }
        storeIdToDelete = storeId;
        deleteStoreMessage.textContent = `Are you sure you want to delete ${store.name}?`;
        deleteStoreModal.style.display = 'flex';
    }

    function hideDeleteStoreModal() {
        deleteStoreModal.style.display = 'none';
        deleteStoreMessage.textContent = '';
        storeIdToDelete = null;
    }

    function showEditNameModal() {
        if (!storeSelector.value) {
            showUserMessage(mantopMessage, 'Please select a store first.', true);
            return;
        }
        editNameModal.style.display = 'flex';
        editNameMessage.textContent = '';
        editNameForm.reset();
    }

    function hideEditNameModal() {
        editNameModal.style.display = 'none';
        editNameForm.reset();
        editNameMessage.textContent = '';
    }

    function showEditLogoModal() {
        if (!storeSelector.value) {
            showUserMessage(mantopMessage, 'Please select a store first.', true);
            return;
        }
        editLogoModal.style.display = 'flex';
        editLogoMessage.textContent = '';
        editLogoForm.reset();
    }

    function hideEditLogoModal() {
        editLogoModal.style.display = 'none';
        editLogoForm.reset();
        editLogoMessage.textContent = '';
    }

    function showEditDomainModal() {
        if (!storeSelector.value) {
            showUserMessage(mantopMessage, 'Please select a store first.', true);
            return;
        }
        editDomainModal.style.display = 'flex';
        editDomainMessage.textContent = '';
        editDomainForm.reset();
    }

    function hideEditDomainModal() {
        editDomainModal.style.display = 'none';
        editDomainForm.reset();
        editDomainMessage.textContent = '';
    }

    function showEditTypeModal() {
        if (!storeSelector.value) {
            showUserMessage(mantopMessage, 'Please select a store first.', true);
            return;
        }
        editTypeModal.style.display = 'flex';
        editTypeMessage.textContent = '';
        editTypeForm.reset();
    }

    function hideEditTypeModal() {
        editTypeModal.style.display = 'none';
        editTypeForm.reset();
        editTypeMessage.textContent = '';
    }

    window.openEditModal = function (field) {
        const modalFunctions = {
            'name': showEditNameModal,
            'logo': showEditLogoModal,
            'domain': showEditDomainModal,
            'type': showEditTypeModal
        };
        if (modalFunctions[field]) {
            modalFunctions[field]();
        }
    };

    function initializePage(userType) {
        if (userType === 'super') {
            const editButtons = document.querySelectorAll('.editBtn');
            editButtons.forEach(button => button.remove());
            fetchStores();
        } else if (userType === 'admin') {
            const mantopContainer = document.querySelector('.mantop');
            if (mantopContainer) {
                mantopContainer.style.display = 'none';
            }
            const deleteButtons = document.querySelectorAll('.deletebtn');
            deleteButtons.forEach(button => {
                button.style.display = 'none';
            });
            fetchStores();
        } else {
            const mantopContainer = document.querySelector('.mantop');
            if (mantopContainer) {
                mantopContainer.style.display = 'none';
            }
        }
    }

    if (userType === 'super' || userType === 'admin') {
        storeSelector.addEventListener('change', updateStoreInfo);

        if (userType === 'super') {
            deleteStoreButton.addEventListener('click', () => {
                const storeId = storeSelector.value;
                if (!storeId) {
                    showUserMessage(mantopMessage, 'Please select a store to delete.', true);
                    return;
                }
                showDeleteStoreModal(storeId);
            });

            addAdminButton.addEventListener('click', showAddAdminModal);

            addStoreButton.addEventListener('click', showAddStoreModal);
        }

        addAdminForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const email = document.getElementById('admin-email').value.trim();
            const password = document.getElementById('admin-password').value.trim();
            const storeId = storeSelector.value;
            if (!email || !password) {
                showUserMessage(adminMessage, 'Please provide both email and password.', true);
                return;
            }
            if (!storeId) {
                showUserMessage(adminMessage, 'Please select a store first.', true);
                return;
            }
            await addStoreAdmin(email, password, storeId);
        });

        addStoreForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const name = document.getElementById('store-name').value.trim();
            if (!name) {
                showUserMessage(storeMessage, 'Please provide a store name.', true);
                return;
            }
            await addStore(name);
        });

        editNameForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const name = document.getElementById('edit-name').value.trim();
            const storeId = storeSelector.value;
            if (!name) {
                showUserMessage(editNameMessage, 'Please provide a store name.', true);
                return;
            }
            if (!storeId) {
                showUserMessage(editNameMessage, 'Please select a store first.', true);
                return;
            }
            await updateStore(storeId, 'name', name);
        });

        editLogoForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const logo = document.getElementById('edit-logo').value.trim();
            const storeId = storeSelector.value;
            if (!logo) {
                showUserMessage(editLogoMessage, 'Please provide a logo URL.', true);
                return;
            }
            if (!storeId) {
                showUserMessage(editLogoMessage, 'Please select a store first.', true);
                return;
            }
            await updateStore(storeId, 'logo', logo);
        });

        editDomainForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const domain = document.getElementById('edit-domain').value.trim();
            const storeId = storeSelector.value;
            if (!domain) {
                showUserMessage(editDomainMessage, 'Please provide a domain.', true);
                return;
            }
            if (!storeId) {
                showUserMessage(editDomainMessage, 'Please select a store first.', true);
                return;
            }
            await updateStore(storeId, 'domain', domain);
        });

        editTypeForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const type = document.getElementById('edit-type').value;
            const storeId = storeSelector.value;
            if (!type) {
                showUserMessage(editTypeMessage, 'Please select a store type.', true);
                return;
            }
            if (!storeId) {
                showUserMessage(editTypeMessage, 'Please select a store first.', true);
                return;
            }
            await updateStore(storeId, 'storetype', type);
        });

        closeAddAdminModal.addEventListener('click', hideAddAdminModal);
        addAdminModal.addEventListener('click', (event) => {
            if (event.target === addAdminModal) {
                hideAddAdminModal();
            }
        });

        closeAddStoreModal.addEventListener('click', hideAddStoreModal);
        addStoreModal.addEventListener('click', (event) => {
            if (event.target === addStoreModal) {
                hideAddStoreModal();
            }
        });

        closeDeleteStoreModal.addEventListener('click', hideDeleteStoreModal);
        cancelDeleteStore.addEventListener('click', hideDeleteStoreModal);
        deleteStoreModal.addEventListener('click', (event) => {
            if (event.target === deleteStoreModal) {
                hideDeleteStoreModal();
            }
        });

        closeDeleteAdminModal.addEventListener('click', hideDeleteAdminModal);
        cancelDeleteAdmin.addEventListener('click', hideDeleteAdminModal);
        deleteAdminModal.addEventListener('click', (event) => {
            if (event.target === deleteAdminModal) {
                hideDeleteAdminModal();
            }
        });

        confirmDeleteAdmin.addEventListener('click', async () => {
            if (adminIdToDelete) {
                await removeStoreAdmin(adminIdToDelete);
                hideDeleteAdminModal();
            }
        });

        closeEditNameModal.addEventListener('click', hideEditNameModal);
        editNameModal.addEventListener('click', (event) => {
            if (event.target === editNameModal) {
                hideEditNameModal();
            }
        });

        closeEditLogoModal.addEventListener('click', hideEditLogoModal);
        editLogoModal.addEventListener('click', (event) => {
            if (event.target === editLogoModal) {
                hideEditLogoModal();
            }
        });

        closeEditDomainModal.addEventListener('click', hideEditDomainModal);
        editDomainModal.addEventListener('click', (event) => {
            if (event.target === editDomainModal) {
                hideEditDomainModal();
            }
        });

        closeEditTypeModal.addEventListener('click', hideEditTypeModal);
        editTypeModal.addEventListener('click', (event) => {
            if (event.target === editTypeModal) {
                hideEditTypeModal();
            }
        });

        confirmDeleteStore.addEventListener('click', async () => {
            if (storeIdToDelete) {
                await deleteStore(storeIdToDelete);
                hideDeleteStoreModal();
            }
        });
    }

    initializePage(userType);
});