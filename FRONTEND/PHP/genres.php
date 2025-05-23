<?php include "header.php"; ?>
    <title>Manage Genres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/genres.css">
    <link rel="stylesheet" type="text/css" href="../CSS/stylingJ.css">

    </br></br></br></br></br>
    <div class="container">
        <h1 class="mb-4">Manage Genres</h1>

        <div id="genreMsg" style="padding: 10px; margin-top: 20px; border-radius: 5px; display: none;"></div>

        <div class="card mb-4">
            <div class="card-body">
                <table class="table table-bordered table-hover text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Genre</th>
                            <th>Visible?</th>
                        </tr>
                    </thead>
                    <tbody id="genTable">
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

        <div class="card">
            <div class="card-body">
                <form id="addGenre" class="d-flex gap-3"> <input type="text" id="newGenreName" name="new_genre" class="form-control" placeholder="New Genre" required>
                    <button type="submit" class="btn btn-primary">Add Genre</button>
                </form>
            </div>
        </div>
    </div>

</br>

    <script src="../JS/genres.js"></script>
<?php include "footer.php"; ?>