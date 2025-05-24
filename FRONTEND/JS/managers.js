document.addEventListener('DOMContentLoaded', () => {
    const userType = sessionStorage.getItem('user_type');
    console.log(userType);


    function initializePage(userType) {
        if (userType === 'super') {
            const editButtons = document.querySelectorAll('.editBtn');
            editButtons.forEach(button => {
                button.remove();
            });
        }

        if (userType === 'admin') {
            const mantopContainer = document.querySelector('.mantop');
            if (mantopContainer) {
                mantopContainer.style.display = 'none';
            }

            const deleteButtons = document.querySelectorAll('.deletebtn');
            deleteButtons.forEach(button => {
                button.style.display = 'none';
            });
        }
    }



    initializePage(userType);
});