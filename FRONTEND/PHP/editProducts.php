<?php include "header.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/editProducts.css">
    <link rel="stylesheet" href="../CSS/stylingJ.css">
</head>

<body>
    </br></br></br>
    <div class="container mt-5">
        <h2 class="mb-4">Manage Books</h2>

        <div class="mb-4 d-flex">
            <div class="me-3">
                <select id="bookViewSelect" class="form-select w-auto">
                    <option value="current" selected>Current Books</option>
                    <option value="new">New Books</option>
                </select>
            </div>
            <div>
                <select id="companySelect" class="form-select w-auto">
                    <option selected disabled>Loading...</option>
                </select>
            </div>
        </div>

        <table class="table table-bordered table-hover text-center">
            <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Rating</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5">Loading products...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editBookForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editBookModalLabel">Edit Book / Store Info</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body row g-3">
                        <div class="col-md-6">
                            <label for="modal_title" class="form-label">Title</label>
                            <input type="text" name="title" id="modal_title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="modal_author" class="form-label">Author</label>
                            <input type="text" name="author" id="modal_author" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="modal_publisher" class="form-label">Publisher</label>
                            <input type="text" name="publisher" id="modal_publisher" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="modal_publishedDate" class="form-label">Published Date</label>
                            <input type="date" name="publishedDate" id="modal_publishedDate" class="form-control">
                        </div>
                        <div class="col-12">
                            <label for="modal_description" class="form-label">Description</label>
                            <textarea name="description" id="modal_description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="modal_pageCount" class="form-label">Page Count</label>
                            <input type="number" name="pageCount" id="modal_pageCount" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="modal_maturityRating" class="form-label">Maturity Rating</label>
                            <select name="maturityRating" id="modal_maturityRating" class="form-select">
                                <option value="NOT_MATURE">Not Mature</option>
                                <option value="MATURE">Mature</option>
                                <option value="EVERYONE">Everyone</option>
                                <option value="TEEN">Teen</option>
                                <option value="ADULT">Adult</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="modal_language" class="form-label">Language</label>
                            <input type="text" name="language" id="modal_language" class="form-control">
                        </div>
                        <div class="col-12">
                            <label for="modal_imageLink" class="form-label">Image Link (Thumbnail)</label>
                            <input type="text" name="imageLink" id="modal_imageLink" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="modal_accessibleIn" class="form-label">Accessible In</label>
                            <input type="text" name="accessibleIn" id="modal_accessibleIn" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="modal_ratingsCount" class="form-label">Ratings Count (Overall)</label>
                            <input type="number" name="ratingsCount" id="modal_ratingsCount" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="modal_isbn13" class="form-label">ISBN-13</label>
                            <input type="text" name="isbn13" id="modal_isbn13" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Categories</label>
                            <div class="dropdown-checklist">
                                <p>Loading categories...</p>
                            </div>
                        </div>

                        <div class="col-md-6 admin-specific-field" style="display:none;">
                            <label for="modal_store_price" class="form-label">Your Store's Price</label>
                            <input type="number" step="0.01" name="store_price" id="modal_store_price" class="form-control">
                        </div>
                        <div class="col-md-6 admin-specific-field" style="display:none;">
                            <label for= "modal_store_rating" class="form-label">Your Store's Rating (0.0-5.0)</label>
                                <input type="number" step="0.1" min="0" max="5" name="store_rating" id="modal_store_rating" class="form-control">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addBookForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addBookModalLabel">Add Book to Store</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="add_store_price" class="form-label">Store Price</label>
                            <input type="number" step="0.01" min="0" name="store_price" id="add_store_price" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="add_store_rating" class="form-label">Store Rating (0.0-5.0)</label>
                            <input type="number" step="0.1" min="0" max="5" name="store_avaliacao" id="add_store_rating" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add to Store</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../JS/editProducts.js"></script>
</body>

</html>
<?php include "footer.php"; ?>