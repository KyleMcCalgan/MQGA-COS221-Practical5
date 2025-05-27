document.addEventListener('DOMContentLoaded', function(){
    const apiKey = sessionStorage.getItem('api_key');
    const userType = sessionStorage.getItem('user_type');
    const apiUrl = '../../BACKEND/public/index.php';

    const genTable = document.getElementById('genTable');
    const addGenre = document.getElementById('addGenre');
    const newGenreName = document.getElementById('newGenreName');
    const genreMsg = document.getElementById('genreMsg');

    //message styling
    if (!genreMsg.style.padding){
        genreMsg.style.padding = '10px';
        genreMsg.style.marginTop = '20px';
        genreMsg.style.borderRadius = '5px';
        genreMsg.style.display = 'none';
    }

    //api err.
    if (!apiKey){
        console.error('Unauthorized: API key missing.');
        showMessage('You are not authorized. Please log in.', true);
        if (addGenre) addGenre.style.display = 'none';
        return;
    }

    if (userType !== 'super'){
        if (addGenre){
            addGenre.style.display = 'none';
        }
    }
    if (userType !== 'admin' && userType !== 'super'){
        console.error('Unauthorized: Wrong user type for this page.');
        showMessage('You do not have permission to manage genres.', true);
        if (addGenre) addGenre.style.display = 'none';
        if (genTable) genTable.innerHTML = '<tr><td colspan="2">Access denied.</td></tr>';
        return;
    }

    function showLoading(){
        const overlay = document.getElementById('loading-screen-genres');
        if (overlay) overlay.remove();
        const loadingScreen = document.createElement('div');
        loadingScreen.id = 'loading-screen-genres';
        //styles for loading screen when loading  genres
        loadingScreen.style.cssText = 'position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5); color:white; display:flex; align-items:center; justify-content:center; z-index:10000;';
        //pretty cool thing I found, loading screen is over the entire page. lmk if I should change it to only be an element
        loadingScreen.innerHTML = '<div>Processing your request...</div>';
        document.body.appendChild(loadingScreen);
    }

    function hideLoading(){
        const loadingScreen = document.getElementById('loading-screen-genres');
        
        if (loadingScreen) loadingScreen.remove();
    }


    function showMessage(message, isError){
        genreMsg.textContent = message;
        genreMsg.style.display = 'block';
        if (isError){
            genreMsg.className = 'error';
            genreMsg.style.backgroundColor = 'rgba(255, 0, 0, 0.93)';
            genreMsg.style.color = '#d32f2f';
        } else{
            genreMsg.className = 'success';
            genreMsg.style.backgroundColor = 'rgba(10, 255, 10, 0.76)';
            genreMsg.style.color = '#008000';
        }
        setTimeout(() =>{
            genreMsg.style.display = 'none';
            genreMsg.textContent = '';
        }, 5000);
    }


    async function requestToAPI(req){
        //check loading screen
        showLoading();
        try{
            const response = await fetch(apiUrl,{
                method: 'POST',
                headers:{ 'Content-Type': 'application/json' },
                body: JSON.stringify(req)
            });

            const responseText = await response.text();
            hideLoading();

            let result;
            try{
                result = JSON.parse(responseText);
            }catch (parseError){
                console.error('Invalid JSON response:', responseText);
                showMessage(`Server error or invalid response: ${responseText.substring(0,150)}...`, true);
                return null;
            }

            if  (!response.ok){
                const errorMessage = result.message || result.error || `Request failed with status ${response.status}.`;
                console.error('API HTTP Error:', errorMessage, 'Full Response:', result);
                showMessage(errorMessage, true);
                return null;
            }

            if (result.status === 'success'){
                return result;
            }else{
                const errorMessage = result.message || result.error || 'An unknown API error occurred.';
                console.error('API Application Error:', errorMessage, 'Full Response:', result);
                showMessage(errorMessage, true);
                 return null;
            }
        } catch (error){
            hideLoading();
            console.error('Fetch API Error:', error);
            showMessage('An error occurred while communicating with the server: ' + error.message, true);
            return null;
        }
    }

    async function fetchGenres(){
        const payload ={
            type: 'GetGenre',
            api_key: apiKey
        };
        const result = await requestToAPI(payload);

        if (result && result.data && result.data.genres){
            createTable(result.data.genres);
            if (result.data.message && result.data.genres.length > 0){
            }
        } else if (result && result.data && result.data.genres === null || (result && result.data && result.data.genres.length === 0)){
            createTable([]);
            showMessage(result.data.message || 'No genres found.', false);
        }
    }

    function createTable(genres){
        if (!genTable) return;
        genTable.innerHTML = '';

        if (genres.length === 0){
            const row = genTable.insertRow();
            const cell = row.insertCell();
            cell.colSpan = 2;
            cell.textContent = 'No genres available.';
            cell.style.textAlign = 'center';
            return;
        }

        genres.forEach(genre =>{
            const row = genTable.insertRow();
            row.insertCell().textContent = genre.genre;

            const visibilityCell = row.insertCell();
            const label = document.createElement('label');
            label.className = 'checkcon';

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.checked = genre.searchable;
            checkbox.dataset.genreId = genre.category_id;
            checkbox.dataset.genreName = genre.genre;

            if (userType !== 'super'){
                checkbox.disabled = true;
                label.title = 'Only Super Admins can change visibility.';
            }

            checkbox.addEventListener('change', checkboxToggle);

            const checkmarkDiv = document.createElement('div');
            checkmarkDiv.className = 'checkmark';

            label.appendChild(checkbox);
            label.appendChild(checkmarkDiv);
            visibilityCell.appendChild(label);
        });
    }

    if (addGenre && userType === 'super'){
        addGenre.addEventListener('submit', async function(event){
            event.preventDefault();
            const genreName = newGenreName.value.trim();

            if (!genreName){
                showMessage('Genre name cannot be empty.', true);
                return;
            }
            if (genreName.length > 100){
                showMessage('Genre name cannot exceed 100 characters.', true);
                return;
            }

            const payload ={
                type: 'AddGenre',
                api_key: apiKey,
                genre_name: genreName
            };

            const result = await requestToAPI(payload);

            if (result){
                showMessage(result.data.message || 'Genre added successfully!', false);
                newGenreName.value = '';
                fetchGenres();
            }
        });
    }

    async function checkboxToggle(event){
        const checkbox = event.target;
        if (checkbox.disabled) return;

        const genreId = checkbox.dataset.genreId;
        const genreName = checkbox.dataset.genreName;
        const isSearchable = checkbox.checked;

        const payload ={
            type: 'UpdateGenreVisibility',
            api_key: apiKey,
            genre_id: parseInt(genreId),
            searchable: isSearchable
        };

        const result = await requestToAPI(payload);

        if (result){
            showMessage(result.data.message || `Genre '${genreName}' visibility updated.`, false);

        } else{
            checkbox.checked = !isSearchable;

        }
    }

    if (genTable && (userType === 'admin' || userType === 'super')){
        fetchGenres();
    } else if (!genTable){
        // console.warn('genTable element not found. Not fetching genres.');
    }

});