document.addEventListener('DOMContentLoaded', function () {
    const displayArea = document.getElementById('store-stats-display-area');
    const apiPath = '../../BACKEND/public/index.php';

    if (!displayArea) {
        // console.error('Store stats display area (store-stats-display-area) not found on the page.');
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
            const textarea = document.createElement('textarea');
            textarea.innerHTML = decoded;
            decoded = textarea.value;
            return decoded;
        } catch (error) {
            // console.error('Error decoding HTML entities:', error, 'Text:', text);
            return text;
        }
    }

    async function getStoreStats() {
        try {
            const req = { type: 'WebsiteSummary' };
            const response = await fetch(apiPath, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(req)
            });

            if (!response.ok) {
                let errorInfo = `API request failed: ${response.status}`;
                try {
                    const errorDataForMsg = await response.json();
                    if (errorDataForMsg && errorDataForMsg.message) {
                        errorInfo += `: ${errorDataForMsg.message}`;
                    } else if (errorDataForMsg && errorDataForMsg.error) {
                        errorInfo += `: ${errorDataForMsg.error}`;
                    }
                } catch (e) {}
                throw new Error(errorInfo);
            }

            const res = await response.json();

            if (res.status === "success" && res.data && res.data.store_specific_averages) {
                displayStoreStats(res.data.store_specific_averages);
            } else {
                const errorMsg = res.message || res.error || 'Failed to load store statistics data.';
                displayError(errorMsg + (res.data && !res.data.store_specific_averages ? ' (Store specific averages missing in response)' : ''));
                console.error('API Error or missing data:', errorMsg, res);
            }

        } catch (error) {
            displayError('Could not retrieve store statistics. Please check your connection or try again later.');
            console.error('Fetch or Processing Error:', error);
        }
    }

    function displayStoreStats(storeAverages) {
        displayArea.innerHTML = '';

        if (!storeAverages || storeAverages.length === 0) {
            displayArea.innerHTML = '<p>No store-specific statistics available at the moment.</p>';
            return;
        }

        const storeDetailsSection = document.createElement('div');
        storeDetailsSection.className = 'store-details-section';

        const storeListContainer = document.createElement('div');
        storeListContainer.className = 'store-list-container';

        storeAverages.forEach(store => {
            const storeItemDiv = document.createElement('div');
            storeItemDiv.className = 'store-item-card';

            const storeNameP = document.createElement('h3');
            storeNameP.className = 'store-item-name';
            storeNameP.textContent = decodeHtmlEntities(store.store_name) || 'Unknown Store';

            const storeAvgPriceP = document.createElement('p');
            storeAvgPriceP.className = 'store-item-avg-price';
            storeAvgPriceP.innerHTML = `<strong>Avg. Price:</strong> ${store.average_store_price ? 'R' + parseFloat(store.average_store_price).toFixed(2) : 'N/A'}`;

            const storeAvgRatingP = document.createElement('p');
            storeAvgRatingP.className = 'store-item-avg-rating';
            storeAvgRatingP.innerHTML = `<strong>Avg. Rating:</strong> ${store.average_store_rating ? parseFloat(store.average_store_rating).toFixed(1) + '/5 ⭐' : 'N/A'}`;

            storeItemDiv.appendChild(storeNameP);
            storeItemDiv.appendChild(storeAvgPriceP);
            storeItemDiv.appendChild(storeAvgRatingP);
            storeListContainer.appendChild(storeItemDiv);
        });

        storeDetailsSection.appendChild(storeListContainer);
        displayArea.appendChild(storeDetailsSection);
    }

    function displayError(message) {
        if (displayArea) {
            displayArea.innerHTML = `<p class="error-message">${message}</p>`;
        }
    }

    getStoreStats();
});