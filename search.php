<?php
require_once('Controller/validate_logged_in.php');
require_once('Model/cable.php');
require_once('Controller/save_cable.php');
require_once('Controller/view_cable.php');
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="vendor/bootstrap-4.1.1-dist/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
        <link rel="stylesheet" href="items.css">
        <title>Search</title>
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
                        <li class="nav-item">
                            <a class="nav-link" href="actions.php">Actions</a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" href="search.php">Search <span class="sr-only">(current)</span></a>
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
            <h2>Search</h2>
            <form method="post" action="" class="form-inline">
                <div class="form-group">
                    <input type="text" class="form-control" id="search" name="search" placeholder="Item or Cable ID">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" name="submit" value="search">Search</button>
                </div>
            </form>

            <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody id="result">
                        
                    
                        
                    </tbody>
                </table>
        </div>
        <script src="vendor/jquery-3.3.1.min.js" crossorigin="anonymous"></script>
        <script src="vendor/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="vendor/bootstrap-4.1.1-dist/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
        <script>
            $(document).ready(function() {
                var timeoutID = null;
                function findMember(str) {
                    $.ajax({
                      dataType: "json",
                      url: './search_handler.php?q='+str,
                      success: function(result) {
                        var items = result['Items'];
                        var cables = result['Cables'];
                        buildResultTable(items, cables);
                      },
                      error: function() {
                          buildResultTable([], []);
                      }
                    });
                }

                $('#search').keyup(function(e) {
                    clearTimeout(timeoutID);
                    timeoutID = setTimeout(() => findMember(e.target.value), 500);
                });

                function buildResultTable(items, cables) {
                    $('#result').empty();
                    if(items.length > 0) {
                        $('#result').append('<tr><td><b>Items</b></td><td>&nbsp;</td></tr>');
                    }
                    for (var i=0; i<items.length; i++) {
                        $('#result').append('<tr><td>'+items[i][0]+'</td><td>'+items[i][1]+'</td></tr>');
                    }
                    if(cables.length > 0) {
                        $('#result').append('<tr><td><b>Cables</b></td><td>&nbsp;</td></tr>');
                    }
                    for (var i=0; i<cables.length; i++) {
                        $('#result').append('<tr><td>'+cables[i][0]+'</td><td>'+cables[i][1]+'</td></tr>');
                    }
                }
            });
        </script>
    </body>
</html>