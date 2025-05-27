document.addEventListener('DOMContentLoaded', function(){
    const summaryContainer = document.getElementById('website-summary-container');
    const apiPath = '../../BACKEND/public/index.php'; 

    if (!summaryContainer){
        return;
    }

    async function getSummary(){
        try{
            const req ={ type: 'WebsiteSummary' }; 
            const response = await fetch(apiPath,{
                method: 'POST',
                headers:{
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(req)
            });

            if (!response.ok){
                let errorInfo = `API request failed with status ${response.status}`;
                try{
                    const errorData = await response.json();
                    if (errorData && errorData.message){
                        errorInfo += `: ${errorData.message}`;
                    } else if (errorData && errorData.error){
                        errorInfo += `: ${errorData.error}`;
                    }
                }catch(e){
                }
                throw new Error(errorInfo);
            }

            const res = await response.json();

            if (res.status === "success" && res.data){ 
                displaySummary(res.data);
            }else  {
                const errorMessage = res.message || res.error || 'Failed to load summary data due to API response format.';
                displayError(errorMessage);
                console.error('API Error:', errorMessage, res);
            }

        }catch (error){
            displayError('Could not retrieve website summary. Please check your connection or try again later.');
            console.error('Fetch or Processing Error:', error);
        }
    }

    function displaySummary(stats){
        summaryContainer.innerHTML = ''; 

        const statsShown = [
           { value: stats.total_books, textAfterValue: "📚 Books Cataloged" },
           { value: stats.total_stores, textAfterValue: "🛒 Stores" }, 
           { value: stats.total_regular_users, textAfterValue: "👥 Community Members" },
           { value: stats.average_price ? `R${stats.average_price}` : 'N/A', textAfterValue: "💰 Average Book Price" },
           { value: (stats.cheapest_price && stats.most_expensive_price) ? `R${stats.cheapest_price} - R${stats.most_expensive_price}` : 'N/A', textAfterValue: "↕️ Price Range" },
           { value: stats.total_reviews, textAfterValue: "💬 Total Book Reviews" },
           { value: stats.average_rating ? `${stats.average_rating}/5` : 'N/A', textAfterValue: "⭐ Average Book Rating" }, 
           { value: stats.total_categories, textAfterValue: "🏷️ Available Categories" }
        ];

        const cardsContainer = document.createElement('div');
        cardsContainer.className = 'stats-cards-container';

        statsShown.forEach(metric =>{
            if  (typeof metric.value  !== 'undefined' && metric.value !== null){
                const card = document.createElement('div');
                card.className = 'stat-card';

                const valueDiv = document.createElement('div');
                valueDiv.className = 'stat-card-value';
                valueDiv.textContent = metric.value;
                
                const labelDiv = document.createElement('div');
                labelDiv.className = 'stat-card-label';
                labelDiv.textContent = metric.textAfterValue;

                card.appendChild(valueDiv);
                card.appendChild(labelDiv);
                cardsContainer.appendChild(card);
            }
        });
        summaryContainer.appendChild(cardsContainer);

    }
    //check error
    function displayError(message){
        summaryContainer.innerHTML = `<p class="error-message">${message}</p>`;
    }

    getSummary();
});