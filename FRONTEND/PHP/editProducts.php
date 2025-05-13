<?php include "header.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Books</title>
    <link rel="stylesheet" href="../CSS/editProducts.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../CSS/stylingJ.css">
</head>

<body>
</br></br></br>
    <div class="container mt-5">
        <h2 class="mb-4">Manage Books</h2>

        <div class="mb-4">
            <select id="companySelect" class="form-select w-auto">
                <option selected disabled>Select a company</option>
                <option value="1">Book Haven</option>
                <option value="2">PageTurners</option>
                <option value="3">ReadMore Inc.</option>
            </select>
        </div>

        <table class="table table-bordered table-hover text-center">
            <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Language</th>
                    <th>Category</th>
                    <th>Rating</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>The Great Book</td>
                    <td>Jane Doe</td>
                    <td>English</td>
                    <td>Fiction</td>
                    <td>4.3</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#editBookModal">edit</button>
                        <button class="btn btn-sm btn-outline-danger"> Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="update_book.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editBookModalLabel">Edit Book</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="The Great Book" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Author</label>
                            <input type="text" name="author" class="form-control" value="Jane Doe" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Publisher</label>
                            <input type="text" name="publisher" class="form-control" value="Penguin">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Published Date</label>
                            <input type="date" name="publishedDate" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Categories</label>
                            <div class="dropdown-checklist">
                                <label><input type="checkbox" name="categories[]" value="Fiction" checked> Fiction</label>
                                <label><input type="checkbox" name="categories[]" value="Non-Fiction"> Non-Fiction</label>
                                <label><input type="checkbox" name="categories[]" value="Science"> Science</label>
                                <label><input type="checkbox" name="categories[]" value="Fantasy"> Fantasy</label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Page Count</label>
                            <input type="number" name="pageCount" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Maturity Rating</label>
                            <select name="maturityRating" class="form-select">
                                <option value="Everyone">Everyone</option>
                                <option value="Mature">Mature</option>
                                <option value="Teen">Teen</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Language</label>
                            <input type="text" name="language" class="form-control" value="English">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Image Link</label>
                            <input type="text" name="imageLink" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Accessible In</label>
                            <input type="text" name="accessibleIn" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Ratings Count</label>
                            <input type="number" name="ratingsCount" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">ISBN-13</label>
                            <input type="text" name="isbn13" class="form-control">
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

    <!-- Bootstrap JS (required for modal) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php include "footer.php"; ?>