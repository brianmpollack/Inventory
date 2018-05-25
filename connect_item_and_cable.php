<?php
require_once('Controller/validate_logged_in.php');
require_once('Controller/connect_item_and_cable.php');


?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="vendor/bootstrap-4.1.1-dist/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
        <link rel="stylesheet" href="items.css">
        <title>Connect Cable</title>
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
                        <li class="nav-item">
                            <a class="nav-link" href="cables.php">Cables</a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" href="actions.php">Actions <span class="sr-only">(current)</span></a>
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
            <h2>Connect Item and Cable</h2>
            <form method="post" action="" class="form-inline">
                <div class="form-group">
                    <input type="text" class="form-control mr-sm-2" id="item_id" name="item_id" aria-describedby="item_id_help" placeholder="Item ID" maxlength="6" value="<?php if(isset($prefill_item_id)) echo $prefill_item_id; ?>" required>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control mr-sm-2" id="cable_id" name="cable_id" aria-describedby="cable_id_help" placeholder="Cable ID" maxlength="4" value="<?php if(isset($prefill_cable_id)) echo $prefill_cable_id; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary" name="submit" value="save-item-cable">Save</button>
            </form>
            <div class="row">
                <div class="col-md-6" id="item"></div>
                <div class="col-md-6" id="cable"></div>
            </div>
        </div>
        <script src="vendor/jquery-3.3.1.min.js" crossorigin="anonymous"></script>
        <script src="vendor/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="vendor/bootstrap-4.1.1-dist/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
        <script>
            $(document).ready(function() {
                $("#item_id").focus();
                var itemTimeoutID = null;
                var cableTimeoutID = null;
                function findMemberItem(str) {
                    if(str.length == 6) {
                        $.ajax({
                            dataType: "json",
                            url: './lookup_item_handler.php?id='+str,
                            success: function(result) {
                                $('#item').append('<h3>Item</h3>');
                                $('#item').append('<h5>ID: '+result['item']['inventory_id']+'</h5>');
                                $('#item').append('<p>Description: '+result['item']['description']+'</p>');
                                $('#item').append('<p>Model Number: '+result['item']['model_number']+'</p>');
                                $('#item').append('<p>Serial Number: '+result['item']['serial_number']+'</p>');
                                $('#item').append('<p>MAC Address: '+result['item']['mac_address']+'</p>');
                                $('#item').append('<p>Notes: '+result['item']['notes']+'</p>');
                            },
                            error: function() {
                                $('#item').empty();
                            }
                        });
                    } else {
                        $('#item').empty();
                    }
                }

                function findMemberCable(str) {
                    if(str.length == 4) {
                        $.ajax({
                            dataType: "json",
                            url: './lookup_cable_handler.php?id='+str,
                            success: function(result) {
                                $('#cable').append('<h3>Cable</h3>');
                                $('#cable').append('<h5>ID: '+result['cable']['id']+'</h5>');
                                $('#cable').append('<p>Description: '+result['cable']['description']+'</p>');
                                $('#cable').append('<p>Notes: '+result['cable']['notes']+'</p>');
                            },
                            error: function() {
                                $('#cable').empty();
                            }
                        });
                    } else {
                        $('#cable').empty();
                    }
                }

                $('#item_id').keyup(function(e) {
                    clearTimeout(itemTimeoutID);
                    itemTimeoutID = setTimeout(() => findMemberItem(e.target.value), 500);
                });
                $('#cable_id').keyup(function(e) {
                    clearTimeout(cableTimeoutID);
                    cableTimeoutID = setTimeout(() => findMemberCable(e.target.value), 500);
                });
            });
        </script>
    </body>
</html>