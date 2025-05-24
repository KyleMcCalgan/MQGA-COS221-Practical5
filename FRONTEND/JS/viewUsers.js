document.addEventListener('DOMContentLoaded', () =>{

    const API_ENDPOINT = '../../BACKEND/public/index.php';

    const curApiKey = sessionStorage.getItem('api_key');
    const curUserType = sessionStorage.getItem('user_type');

    const card = document.querySelector('.card');
    const itemHeader = document.querySelector('.item.item-header');
    const lineAfterHeader = itemHeader ? itemHeader.nextElementSibling : null;
    const specErrorMsg = 'no-users-message';

    function showMessage(message, type = 'info'){
        alert(`${type.toUpperCase()}: ${message}`);
        if (type === 'unauthorized'){
        }
    }

    function cleanupCards(){
        if (!card || !lineAfterHeader){
            return;
        }
        const existingNoUsersMessage = card.querySelector(`#${specErrorMsg}`);
        if (existingNoUsersMessage){
            card.removeChild(existingNoUsersMessage);
        }
        const elementsToRemove = [];
        let currentElement = lineAfterHeader.nextElementSibling;
        while (currentElement){
            elementsToRemove.push(currentElement);
            currentElement = currentElement.nextElementSibling;
        }
        elementsToRemove.forEach(el => card.removeChild(el));
    }

    async function getUsers(apiKey){
        const req ={
            type: "GetUsers",
            api_key: apiKey,
            userType: "regular"//only regular
        };

        try{
            const response = await fetch(API_ENDPOINT,{
                method: 'POST',
                headers:{ 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(req)
            });

            if (!response.ok){
                let errorMessage = `Server error: ${response.status}`;
                try{
                    const errorResult = await response.json();
                    errorMessage = errorResult.message || errorMessage;
                }catch (e){
                    //if url changes (please dont)
                }
                showMessage(errorMessage, response.status === 401 || response.status === 403 ? 'unauthorized' : 'error');
                cleanupCards();
                if (response.status === 404) noUsersMsg(errorMessage);
                return;
            }

            const result = await response.json();
            cleanupCards();

            if (result.status === 'success' || result.status === true){
                if (result.data && result.data.length > 0){
                    popUserTable(result.data, apiKey);
                } else{
                    noUsersMsg(result.message || 'No regular users found in DB');
                }
            } else{
                showMessage(result.message || 'Could not get users', 'error');
            }

        } catch (error){
            console.error('Getting users error:', error);
            cleanupCards();
            showMessage(`An error occurred while getting users: ${error.message}.`, 'error');
            noUsersMsg('User data load issue.');
        }
    }

    function noUsersMsg(messageText){
        if (!card || !lineAfterHeader) return;
        cleanupCards();

        const noUsersMessage = document.createElement('p');
        noUsersMessage.id = specErrorMsg;
        noUsersMessage.textContent = messageText;
        noUsersMessage.style.textAlign = 'center';
        noUsersMessage.style.padding = '20px';

        lineAfterHeader.insertAdjacentElement('afterend', noUsersMessage);
    }

    function popUserTable(users, apiKey){
        if (!card || !lineAfterHeader) return;
        
        let currentAnchor = lineAfterHeader;

        users.forEach(user =>{
            const divsBoi = document.createElement('div');
            divsBoi.classList.add('item');
            divsBoi.dataset.userId = user.id; 

            divsBoi.innerHTML = `
                <p>${user.id !== undefined ? user.id : 'N/A'}</p>
                <p>${user.name || 'N/A'}</p>
                <p>${user.surname || 'N/A'}</p>
                <p>${user.email || 'N/A'}</p>
                <button class="delete-user-btn" aria-label="Delete user ${user.name || user.id}">Delete</button>
            `;
            
            currentAnchor.insertAdjacentElement('afterend', divsBoi);
            currentAnchor = divsBoi;

            const hrElement = document.createElement('hr');
            currentAnchor.insertAdjacentElement('afterend', hrElement);
            currentAnchor = hrElement;

            const deleteButton = divsBoi.querySelector('.delete-user-btn');
            deleteButton.addEventListener('click', () => deleteUser(user.id, divsBoi, hrElement, apiKey));
        });
    }

    async function deleteUser(userId, userItemElement, hrElementAssociatedWithItem, apiKey){
        if (!confirm(`Are you sure you want to delete user ID: ${userId}? This action is irreversible`)){
            return;
        }

        const req ={
            type: "RemoveUsers",
            api_key: apiKey,
            id: userId
        };

        try{
            const response = await fetch(API_ENDPOINT,{
                method: 'POST',
                headers:{ 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(req)
            });

            if (!response.ok){
                 let errorMessage = `Server error: ${response.status}`;
                try{
                    const errorResult = await response.json();
                    errorMessage = errorResult.message || errorMessage;
                } catch (e){}
                showMessage(errorMessage, response.status === 403 || response.status === 401 ? 'unauthorized' : 'error');
                return;
            }

            const result = await response.json();

            if (result.status === 'success' || result.status === true){
                showMessage(result.message || `User ID ${userId} deleted successfully.`, 'success');
                
                if (userItemElement && userItemElement.parentNode){
                    userItemElement.parentNode.removeChild(userItemElement);
                }
                if (hrElementAssociatedWithItem && hrElementAssociatedWithItem.parentNode){
                    hrElementAssociatedWithItem.parentNode.removeChild(hrElementAssociatedWithItem);
                }
                
                const remainingItems = card.querySelectorAll('.item:not(.item-header)');
                if (remainingItems.length === 0){
                    noUsersMsg('All users have been removed or no users found.');
                }
            } else{
                showMessage(result.message || `Failed to delete user ID ${userId}.`, 'error');
            }

        } catch (error){
            console.error('Delete User Error:', error);
            showMessage(`An error occurred while deleting user ID: ${userId}: ${error.message}.`, 'error');
        }
    }

    function initUserTable(){
        if (!card || !itemHeader || !lineAfterHeader){
             if (card){ 
                const errorP = document.createElement('p');
                errorP.textContent = "Error: Could not initialize the user list due to a missing page element.";
                errorP.style.color = "red";
                errorP.style.textAlign = "center";
                errorP.style.padding = "20px";
                cleanupCards();
                if(lineAfterHeader) lineAfterHeader.insertAdjacentElement('afterend', errorP);
                else card.appendChild(errorP);
            }
            return;
        }

        if (curUserType === 'super' && curApiKey){
            getUsers(curApiKey);
        } else{
            cleanupCards();
            const authMessage = curApiKey ? "You are not authorized to manage users bruh." : "Please log in as a super administrator to manage users.";
            noUsersMsg(authMessage);
            showMessage(authMessage, 'unauthorized');
        }
    }

    initUserTable();
});