<?php
require_once('Controller/validate_logged_in.php');
require_once('Model/item.php');
require_once('Model/cable.php');
require_once('Model/location.php');
require_once('Model/transaction.php');
require_once('Controller/save_item.php');
require_once('Controller/view_item.php');
$all_locations = Location::retrieveAllFromDatabase();
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="vendor/bootstrap-4.1.1-dist/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
        <link rel="stylesheet" href="items.css">
        <title>View Item</title>
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
                        <li class="nav-item active">
                            <a class="nav-link" href="items.php">Items <span class="sr-only">(current)</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="locations.php">Locations</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="cables.php">Cables</a>
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
            <h2>View Item</h2>
            <form method="post" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="inventory_id">Inventory ID</label>
                    <input type="text" class="form-control" id="inventory_id" name="inventory_id" aria-describedby="inventory_id_help" placeholder="Inventory ID" maxlength="6" value="<?php if(isset($prefill_inventory_id)) echo $prefill_inventory_id; ?>" disabled>
                    <input type="hidden" name="inventory_id" value="<?php if(isset($prefill_inventory_id)) echo $prefill_inventory_id; ?>">
                    <small id="inventory_id_help" class="form-text text-muted">Six digit hexadecimal.</small>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <input type="text" class="form-control" id="description" name="description" aria-describedby="description_help" placeholder="Description" value="<?php if(isset($prefill_description)) echo $prefill_description; ?>" required>
                    <small id="description_help" class="form-text text-muted"><!--Short description of item.--></small>
                </div>
                <div class="form-group">
                    <label for="model">Model Number</label>
                    <input type="text" class="form-control" id="model" name="model" aria-describedby="model_help" placeholder="Model Number" value="<?php if(isset($prefill_model)) echo $prefill_model; ?>">
                    <small id="model_help" class="form-text text-muted"><!--Model number of item.--></small>
                </div>
                <div class="form-group">
                    <label for="serial">Serial Number</label>
                    <input type="text" class="form-control" id="serial" name="serial" aria-describedby="serial_help" placeholder="Serial Number" value="<?php if(isset($prefill_serial)) echo $prefill_serial; ?>">
                    <small id="serial_help" class="form-text text-muted"><!--Serial number of item.--></small>
                </div>
                <div class="form-group">
                    <label for="mac_address">MAC Address</label>
                    <input type="text" class="form-control" id="mac_address" name="mac_address" aria-describedby="mac_address_help" placeholder="MAC Address" value="<?php if(isset($prefill_mac_address)) echo $prefill_mac_address; ?>">
                    <small id="mac_address_help" class="form-text text-muted"><!--MAC Address of item.--></small>
                </div>
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" placeholder="Notes" rows="5"><?php if(isset($prefill_notes)) echo $prefill_notes; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <select class="form-control" id="location" name="location">
                        <option value=""> </option>
                        <?php foreach($all_locations as $location): ?>
                        <option value="<?php echo $location->getID(); ?>" <?php if(isset($prefill_location) && $prefill_location == $location->getID()) echo ' selected'; ?>><?php echo $location->getName(); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <?php if(isset($connected_cables) && count($connected_cables) > 0): ?>
                <h3>Connected Cables</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Description</th>
                            <th>Connected Items</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($connected_cables as $connected_cable): ?>
                        <tr>
                            <td><a href="./view_cable.php?id=<?php echo urlencode($connected_cable->getID()); ?>"><?php echo $connected_cable->getID(); ?></a></td>
                            <td><a href="./view_cable.php?id=<?php echo urlencode($connected_cable->getID()); ?>"><?php echo $connected_cable->getDescription(); ?></a></td>
                            <td>
                                <?php $connected_items = Item::getItemsConnectedToCable($connected_cable->getID()); ?>
                                <?php foreach($connected_items as $connected_item): ?>
                                <?php if($connected_item->getInventoryID() != $item->getInventoryID()): ?>
                                <a href="./view_item.php?inventory_id=<?php echo urlencode($connected_item->getInventoryID()); ?>"><?php echo $connected_item->getDescription(); ?></a>
                                <br>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>

                <h4>Transactions</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Price</th>
                            <th>Notes</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if( isset($transactions) ): ?>
                        <?php foreach( $transactions as $transaction ): ?>
                        <tr>
                            <td><input type="date" class="form-control" name="transaction-date[<?php echo $transaction->getID(); ?>]" value="<?php echo $transaction->getDate(); ?>"></td>
                            <td><input type="text" class="form-control" name="transaction-location[<?php echo $transaction->getID(); ?>]" value="<?php echo $transaction->getLocation(); ?>"></td>
                            <td>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">$</div>
                                    </div>
                                    <input type="text" class="form-control" name="transaction-price[<?php echo $transaction->getID(); ?>]" value="<?php echo $transaction->getPrice(); ?>">
                                </div>
                            </td>
                            <td><textarea class="form-control" name="transaction-notes[<?php echo $transaction->getID(); ?>]"><?php echo $transaction->getNotes(); ?></textarea></td>
                            <td><input type="checkbox" class="form-check-input" name="transaction-delete[]" value="<?php echo $transaction->getID(); ?>"></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        <tr>
                            <td><input type="date" class="form-control" name="transaction-date-new"></td>
                            <td><input type="text" class="form-control" name="transaction-location-new"></td>
                            <td>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">$</div>
                                    </div>
                                    <input type="text" class="form-control" name="transaction-price-new">
                                </div>
                            </td>
                            <td><textarea class="form-control" name="transaction-notes-new"></textarea></td>
                            <td>&nbsp;</td>
                        </tr>
                    </tbody>
                </table>

                <h4>Files</h4>
                <?php if( isset($files) ): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Filename</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach( $files as $file_id=>$filename ): ?>
                        <tr>
                            <td><a href="<?php echo $filename; ?>" target="_blank"><?php echo basename($filename); ?></a></td>
                            <td><input type="checkbox" class="form-check-input" name="file-delete[]" value="<?php echo $file_id; ?>"></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
                <input type="file" class="form-control" name="file-new">
                <button type="submit" class="btn btn-primary" name="submit" value="save-item">Save</button>
            </form>

        </div>
        <script src="vendor/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="vendor/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="vendor/bootstrap-4.1.1-dist/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
        <script src="disable_enter.js"></script>
    </body>
</html>