<?php
require_once('Controller/validate_logged_in.php');
require_once('Model/cable.php');
require_once('Model/item.php');
require_once('Controller/save_cable.php');
require_once('Controller/view_cable.php');
$connected_items = array();
$connected_cables = array();
if(isset($cable)) {
    $connected_items = Item::getItemsConnectedToCable($cable->getID());
    $connected_cables = Cable::getCablesConnectedToCable($cable->getID());
}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="vendor/bootstrap-4.1.1-dist/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
        <link rel="stylesheet" href="items.css">
        <title>View Cable</title>
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
                        <li class="nav-item">
                            <a class="nav-link" href="actions.php">Actions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="search.php">Search</a>
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
            <h2>View Cable</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label for="cable_id">Cable ID</label>
                    <input type="text" class="form-control" id="cable_id" aria-describedby="cable_id_help" placeholder="Cable ID" maxlength="4" value="<?php if(isset($prefill_cable_id)) echo $prefill_cable_id; ?>" disabled>
                    <small id="cable_id_help" class="form-text text-muted">Four digit hexadecimal.</small>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <input type="text" class="form-control" id="description" name="description" aria-describedby="description_help" placeholder="Description" value="<?php if(isset($prefill_description)) echo $prefill_description; ?>">
                    <small id="description_help" class="form-text text-muted"><!--Short description of item.--></small>
                </div>
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" placeholder="Notes" rows="5"><?php if(isset($prefill_notes)) echo $prefill_notes; ?></textarea>
                </div>
                <input type="hidden" name="cable_id" value="<?php echo $prefill_cable_id; ?>">
                <button type="submit" class="btn btn-primary" name="submit" value="save-cable">Save</button>
            </form>
            <h3>Connected Items</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($connected_items as $connected_item): ?>
                        <tr>
                            <td><a href="./view_item.php?inventory_id=<?php echo urlencode($connected_item->getInventoryID()); ?>"><?php echo $connected_item->getInventoryID(); ?></a></td>
                            <td><a href="./view_item.php?inventory_id=<?php echo urlencode($connected_item->getInventoryID()); ?>"><?php echo $connected_item->getDescription(); ?></a></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php foreach($connected_cables as $connected_cable): ?>
                        <?php $connected_items_from_connected_cable = Item::getItemsConnectedToCable($connected_cable->getID()); ?>
                        <?php foreach($connected_items_from_connected_cable as $connected_item): ?>
                            <tr>
                                <td><a href="./view_item.php?inventory_id=<?php echo urlencode($connected_item->getInventoryID()); ?>"><?php echo $connected_item->getInventoryID(); ?> </a>(via Cable <a href="./view_cable.php?id=<?php echo urlencode($connected_cable->getID()); ?>"><?php echo $connected_cable->getID(); ?></a>)</td>
                                <td><a href="./view_item.php?inventory_id=<?php echo urlencode($connected_item->getInventoryID()); ?>"><?php echo $connected_item->getDescription(); ?></a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if(count($connected_cables) > 0): ?>
            <h3>Connected Cables</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($connected_cables as $connected_cable): ?>
                    <tr>
                        <td><a href="./view_cable.php?id=<?php echo urlencode($connected_cable->getID()); ?>"><?php echo $connected_cable->getID(); ?></a></td>
                        <td><a href="./view_cable.php?id=<?php echo urlencode($connected_cable->getID()); ?>"><?php echo $connected_cable->getDescription(); ?></a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
        <script src="vendor/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="vendor/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="vendor/bootstrap-4.1.1-dist/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
        <script src="disable_enter.js"></script>
    </body>
</html>