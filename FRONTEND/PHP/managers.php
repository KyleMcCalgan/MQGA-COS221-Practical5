<!DOCTYPE html>
<html lang="en">

<head>
    <title>Managment</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" type="text/css" href="../CSS/stylingJ.css">
    <link rel="stylesheet" type="text/css" href="../CSS/managers.css">

    <script src="../JS/managers.js"></script>
</head>

<?php include "header.php"; ?>

<body>

    <div class="container">
        <div class="mantop">
            <h1 class="mainheader">Stores managment</h1>
            <div class="topinnercont">
                <div class="left">
                    <h2>Specific Store</h2>
                    <div class="leftselect">
                        <select class="form-select" id="storeSelector">
                            <option value="">Select a Store</option>
                            <option value="store1">Book Haven</option>
                            <option value="store2">Comic Corner</option>
                            <option value="store3">EduBooks Ltd</option>
                        </select>
                    </div>

                    <div class="mantopfunc">
                        <button class="blackbutton normalbutton" id="delete-store">Delete store</button>
                        <button class="blackbutton normalbutton" id="add-admin">Add admin</button>
                    </div>

                    <p id="mantop-message" style="display: none;"></p>
                </div>

                <div class="gen">
                    <h2 class="subheader">General</h2>
                    <button class="blackbutton normalbutton" id="add-store">Add store</button>
                </div>
            </div>
        </div>

        <div class="modal" id="add-admin-modal">
            <div class="modal-content">
                <span class="close-button" id="close-add-admin-modal">×</span>
                <h2>Add Store Admin</h2>
                <p id="admin-message"></p>
                <form id="add-admin-form">
                    <label for="admin-email">Email:</label>
                    <input type="email" id="admin-email" class="form-control" required>
                    <label for="admin-password">Password:</label>
                    <input type="password" id="admin-password" class="form-control" required>
                    <div class="centerbutton">
                        <button type="submit" class="blackbutton normalbutton">Submit</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal" id="add-store-modal">
            <div class="modal-content">
                <span class="close-button" id="close-add-store-modal">×</span>
                <h2>Add Store</h2>
                <p id="store-message"></p>
                <form id="add-store-form">
                    <label for="store-name">Store Name:</label>
                    <input type="text" id="store-name" class="form-control" required>
                    <div class="centerbutton">
                        <button type="submit" class="blackbutton normalbutton">Submit</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal" id="delete-store-modal">
            <div class="modal-content">
                <span class="close-button" id="close-delete-store-modal">×</span>
                <h2>Confirm Delete Store</h2>
                <p id="delete-store-message"></p>
                <div class="centerbutton">
                    <button class="blackbutton normalbutton" id="cancel-delete-store">Cancel</button>
                    <button class="blackbutton normalbutton" id="confirm-delete-store">Delete</button>
                </div>
            </div>
        </div>


        <div class="genman">
            <h1 class="booknameheading">Book Haven</h1>
            <h2>Store information</h2>


            <div class="card-body">
                <div class="form-group">
                    <label for="display-name">Name</label>
                    <div class="display-field">
                        <span id="display-name">Loading...</span>
                        <button type="button" class="editBtn" onclick="openEditModal('name')">
                            <svg viewBox="0 0 512 512">
                                <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="display-logo">Logo</label>
                    <div class="display-field">
                        <span id="display-logo">Loading...</span>
                        <button type="button" class="editBtn" onclick="openEditModal('logo')">
                            <svg viewBox="0 0 512 512">
                                <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="display-domain">Domain</label>
                    <div class="display-field">
                        <span id="display-domain">Loading...</span>
                        <button type="button" class="editBtn" onclick="openEditModal('domain')">
                            <svg viewBox="0 0 512 512">
                                <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="display-type">Type</label>
                    <div class="display-field">
                        <span id="display-type">Loading...</span>
                        <button type="button" class="editBtn" onclick="openEditModal('type')">
                            <svg viewBox="0 0 512 512">
                                <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c-6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal" id="edit-name-modal">
                <div class="modal-content">
                    <span class="close-button" id="close-edit-name-modal">×</span>
                    <h2>Edit Store Name</h2>
                    <p id="edit-name-message"></p>
                    <form id="edit-name-form">
                        <label for="edit-name">Name:</label>
                        <input type="text" id="edit-name" class="form-control" required>
                        <div class="centerbutton">
                            <button type="submit" class="blackbutton normalbutton">Submit</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal" id="edit-logo-modal">
                <div class="modal-content">
                    <span class="close-button" id="close-edit-logo-modal">×</span>
                    <h2>Edit Store Logo</h2>
                    <p id="edit-logo-message"></p>
                    <form id="edit-logo-form">
                        <label for="edit-logo">Logo URL:</label>
                        <input type="url" id="edit-logo" class="form-control" required>
                        <div class="centerbutton">
                            <button type="submit" class="blackbutton normalbutton">Submit</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal" id="edit-domain-modal">
                <div class="modal-content">
                    <span class="close-button" id="close-edit-domain-modal">×</span>
                    <h2>Edit Store Domain</h2>
                    <p id="edit-domain-message"></p>
                    <form id="edit-domain-form">
                        <label for="edit-domain">Domain:</label>
                        <input type="text" id="edit-domain" class="form-control" required>
                        <div class="centerbutton">
                            <button type="submit" class="blackbutton normalbutton">Submit</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal" id="edit-type-modal">
                <div class="modal-content">
                    <span class="close-button" id="close-edit-type-modal">×</span>
                    <h2>Edit Store Type</h2>
                    <p id="edit-storetype-message"></p>
                    <form id="edit-type-form">
                        <label for="edit-type">Type:</label>
                        <select id="edit-type" class="form-control" required>
                            <option value="online">Online</option>
                            <option value="physical">Physical</option>
                        </select>
                        <div class="centerbutton">
                            <button type="submit" class="blackbutton normalbutton">Submit</button>
                        </div>
                    </form>
                </div>
            </div>

            <h2>Admins</h2>
            <div class="adminscont">
                <div class="storerow">
                    <h3 class="amount">1</h3>
                    <h3 class="adname">Name</h3>
                    <h3 class="ademail">Email</h3>
                    <button class="normalbutton blackbutton adrow deletebtn">delete</button>
                </div>

            </div>

        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?php include "footer.php"; ?>

</html>