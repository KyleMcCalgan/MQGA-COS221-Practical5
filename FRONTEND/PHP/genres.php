<?php include "header.php"; ?>
    <title>Manage Genres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/genres.css">
    <link rel="stylesheet" type="text/css" href="../CSS/stylingJ.css">

    </br></br></br></br></br>
    <div class="container">
        <h1 class="mb-4">Manage Genres</h1>

        <!-- Table of Genres -->
        <div class="card mb-4">
            <div class="card-body">
                <table class="table table-bordered table-hover text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Genre</th>
                            <th>Visible?</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Fantasy</td>
                            <td>
                                <label class="checkcon">
                                    <input checked="checked" type="checkbox">
                                    <div class="checkmark"></div>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td>Children's books</td>
                            <td>
                                <label class="checkcon">
                                    <input checked="checked" type="checkbox">
                                    <div class="checkmark"></div>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td>Science</td>
                            <td>
                                <label class="checkcon">
                                    <input checked="checked" type="checkbox">
                                    <div class="checkmark"></div>
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add New Genre -->
        <div class="card">
            <div class="card-body">
                <form action="add_genre.php" method="POST" class="d-flex gap-3">
                    <input type="text" name="new_genre" class="form-control" placeholder="New Genre" required>
                    <button type="submit" class="btn btn-primary">Add Genre</button>
                </form>
            </div>
        </div>
    </div>

</body>
<?php include "footer.php"; ?>

</html>