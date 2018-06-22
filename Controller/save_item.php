<?php
require_once("Model/item.php");
require_once("Model/transaction.php");
if(isset($_POST['submit']) && $_POST['submit'] == 'save-item') {
    $inventory_id = $_POST['inventory_id'];
    $description = $_POST['description'];
    $model_number = $_POST['model'];
    $serial_number = $_POST['serial'];
    $mac_address = $_POST['mac_address'];
    $notes = $_POST['notes'];
    $location = $_POST['location'];

    if($description == "") {
        trigger_error("Description is required.");
        exit;
    }

    try {
        $item = Item::retrieveFromDatabase($inventory_id);
        $item->setDescription($description);
        $item->setModelNumber($model_number);
        $item->setSerialNumber($serial_number);
        $item->setMacAddress($mac_address);
        $item->setNotes($notes);
        $item->setLocation($location);
        $item->save();

        // Save transactions
        if( isset($_POST['transaction-date-new']) 
            && isset($_POST['transaction-location-new']) 
            && isset($_POST['transaction-price-new'])
            && isset($_POST['transaction-notes-new'])
            && (
                $_POST['transaction-location-new'] != ""
                || $_POST['transaction-price-new'] != ""
                || $_POST['transaction-notes-new'] != ""
            )
        ) {
            Transaction::createTransaction(
                $item->getInventoryID(),
                $_POST['transaction-date-new'],
                $_POST['transaction-location-new'],
                $_POST['transaction-price-new'],
                $_POST['transaction-notes-new']
            );
        }
        if( isset($_POST['transaction-date'])
            && isset($_POST['transaction-location']) 
            && isset($_POST['transaction-price'])
            && isset($_POST['transaction-notes'])
        ) {
            foreach( $_POST['transaction-date'] as $transaction_id => $date_value ) {
                if(! ( isset($_POST['transaction-location'][$transaction_id]) 
                       && isset($_POST['transaction-price'][$transaction_id])
                       && isset($_POST['transaction-notes'][$transaction_id])
                ) ) {
                    // Not all the fields were set, so do not save this entry
                    continue;
                }
                $transaction = Transaction::retrieveFromDatabase($transaction_id);
                $transaction->setDate($date_value);
                $transaction->setLocation($_POST['transaction-location'][$transaction_id]);
                $transaction->setPrice($_POST['transaction-price'][$transaction_id]);
                $transaction->setNotes($_POST['transaction-notes'][$transaction_id]);
                $transaction->save();
            }
        }
        if( isset($_POST['transaction-delete']) ) {
            foreach( $_POST['transaction-delete'] as $transaction_id ) {
                $transaction = Transaction::retrieveFromDatabase($transaction_id);
                $transaction->delete();
            }
        }

        // Handle file upload
        if( isset($_FILES['file-new']) && isset($_FILES['file-new']['tmp_name']) && $_FILES['file-new']['tmp_name'] != '' ) {
            $item->addFile($_FILES['file-new']['tmp_name'], $_FILES['file-new']['name']);
        }
        if( isset($_POST['file-delete']) ) {
            foreach( $_POST['file-delete'] as $file_id ) {
                $item->deleteFile($file_id);
            }
        }

        // Handle maintenance
        if( isset($_POST['maintenance-date-new']) 
            && isset($_POST['maintenance-notes-new'])
            && (
                $_POST['maintenance-notes-new'] != ""
            )
        ) {
            Maintenance::createMaintenance(
                $item->getInventoryID(),
                $_POST['maintenance-date-new'],
                $_POST['maintenance-notes-new']
            );
        }
        if( isset($_POST['maintenance-date'])
            && isset($_POST['maintenance-notes'])
        ) {
            foreach( $_POST['maintenance-date'] as $maintenance_id => $date_value ) {
                if(! ( isset($_POST['maintenance-notes'][$maintenance_id])
                ) ) {
                    // Not all the fields were set, so do not save this entry
                    continue;
                }
                $maintenance = Maintenance::retrieveFromDatabase($maintenance_id);
                $maintenance->setDueDate($date_value);
                $maintenance->setNotes($_POST['maintenance-notes'][$maintenance_id]);
                $maintenance->setCompleted($_POST['maintenance-completed'][$maintenance_id] == '1');
                $maintenance->save();
            }
        }
        if( isset($_POST['maintenance-delete']) ) {
            foreach( $_POST['maintenance-delete'] as $maintenance_id ) {
                $maintenance = Maintenance::retrieveFromDatabase($maintenance_id);
                $maintenance->delete();
            }
        }
        
    } catch(Exception $e) {
        $user_error = "Could not save item.<br>".$e->getMessage();
    }
}