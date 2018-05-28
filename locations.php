<?php
require_once('Controller/validate_logged_in.php');
require_once('Model/location.php');

$all_locations = Location::retrieveAllFromDatabase();
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="vendor/bootstrap-4.1.1-dist/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="vendor/DataTables/datatables.min.css"/>
        <link rel="stylesheet" href="items.css">
        <title>List Locations</title>
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
                        <li class="nav-item active">
                            <a class="nav-link" href="locations.php">Locations <span class="sr-only">(current)</span></a>
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
            <h2>Locations</h2>
            <a href="./add_location.php" class="btn btn-info mb-2">New Location</a> 
            <form method="post" action="">
                <table class="table" id="dataTable">
                    <thead>
                        <tr>
                            <th>Location Name</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($all_locations as $location): ?>
                        <tr>
                            <td>
                                <a href="./view_location.php?id=<?php echo urlencode($location->getID()); ?>"><?php echo $location->getName(); ?></a>
                            </td>
                        </tr>
                    
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </form>
        </div>
        <script src="vendor/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="vendor/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="vendor/bootstrap-4.1.1-dist/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
        <script type="text/javascript" src="vendor/DataTables/datatables.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#dataTable').DataTable();
            } );
        </script>
        <script src="disable_enter.js"></script>
    </body>
</html>