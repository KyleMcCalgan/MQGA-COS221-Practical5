<?php include "header.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Super Admin - Manage Stores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/genres.css">
    <link rel="stylesheet" type="text/css" href="../CSS/stylingJ.css">
</head>

<body>
    <div class="container">
        <h1 class="mb-4">Super Admin - Manage Stores</h1>

        <!-- Store Selection Dropdown -->
        <div class="mb-4">
            <label for="storeSelector" class="form-label">Select Store:</label>
            <select class="form-select" id="storeSelector" onchange="showStoreDetails()">
                <option value="">Select a Store</option>
                <option value="store1">Book Haven</option>
                <option value="store2">Comic Corner</option>
                <option value="store3">EduBooks Ltd</option>
            </select>
        </div>

        <!-- Store Management Sections (Initially hidden) -->
        <div id="store1" class="store-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Book Haven</h5>
                    <p class="card-text">Manage admins for Book Haven</p>
                    <!-- Managers List -->
                    <ul>
                        <li>Admin 1  <button class="btn btn-danger btn-sm" style="margin-left: 10px;">Remove</button></li>
                        <li>Admin 2 <button class="btn btn-danger btn-sm" style="margin-left: 10px;">Remove</button></li>
                        <li>Admin 3 <button class="btn btn-danger btn-sm" style="margin-left: 10px;">Remove</button></li>
                    </ul>
                    <!-- Button to add admin -->
                    <div class="card">
                        <div class="card-body">
                            <form action="add_man.php" class="d-flex gap-3">
                                <input type="text" name="newman" class="form-control" placeholder="New Manager" required>
                                <button type="submit" class="btn btn-primary">Add Manager</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div id="store2" class="store-card" style="display: none;">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Comic Corner</h5>
                    <p class="card-text">Manage admins for Comic Corner</p>

                    <ul>
                        <li>Admin 1 <button class="btn btn-danger btn-sm">Remove</button></li>
                        <li>Admin 2 <button class="btn btn-danger btn-sm">Remove</button></li>
                    </ul>

                    <button class="btn btn-primary">Add Admin</button>
                </div>
            </div>
        </div>

        <div id="store3" class="store-card" style="display: none;">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">EduBooks Ltd</h5>
                    <p class="card-text">Manage admins for EduBooks Ltd</p>

                    <ul>
                        <li>Admin 1 <button class="btn btn-danger btn-sm">Remove</button></li>
                        <li>Admin 2 <button class="btn btn-danger btn-sm">Remove</button></li>
                        <li>Admin 3 <button class="btn btn-danger btn-sm">Remove</button></li>
                    </ul>

                    <button class="btn btn-primary">Add Admin</button>
                </div>
            </div>
        </div> -->

    </div>

    <!-- <script>
        function showStoreDetails() {

            const storeCards = document.querySelectorAll('.store-card');
            storeCards.forEach(card => {
                card.style.display = 'none';
            });


            const selectedStore = document.getElementById('storeSelector').value;


            if (selectedStore) {
                document.getElementById(selectedStore).style.display = 'block';
            }
        }
    </script> -->
</body>

</html>

<?php include "footer.php"; ?>