<?php
require_once('Controller/validate_logged_in.php');
require_once('Model/cable.php');
require_once('Controller/create_cable.php');


?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="vendor/bootstrap-4.1.1-dist/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
        <link rel="stylesheet" href="items.css">
        <title>Add Cable</title>
    </head>
    <body>
        <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
            <div class="container">
                <a class="navbar-brand" href="#">Pollack Inventory</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="items.php">Items</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="locations.php">Locations</a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" href="cables.php">Cables <span class="sr-only">(current)</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container">
            <?php if(isset($user_error) && $user_error != ''): ?>
            <div class="alert alert-danger">
                <p><?php echo $user_error; ?></p>
            </div>
            <?php endif; ?>
            <h2>Add Cable</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label for="cable_id">Cable ID</label>
                    <input type="text" class="form-control" id="cable_id" name="cable_id" aria-describedby="cable_id_help" placeholder="Cable ID" maxlength="4" value="<?php if(isset($prefill_cable_id)) echo $prefill_cable_id; ?>">
                    <small id="cable_id_help" class="form-text text-muted">Four digit hexidecimal. Leave blank to generate automatically.</small>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <input type="text" class="form-control" id="description" name="description" aria-describedby="description_help" placeholder="Description" value="<?php if(isset($prefill_description)) echo $prefill_description; ?>" required>
                    <small id="description_help" class="form-text text-muted"><!--Short description of item.--></small>
                </div>
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" placeholder="Notes" rows="5"><?php if(isset($prefill_notes)) echo $prefill_notes; ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary" name="submit" value="new-cable">Save</button>
            </form>
        </div>
        <script src="vendor/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="vendor/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="vendor/bootstrap-4.1.1-dist/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
    </body>
</html>