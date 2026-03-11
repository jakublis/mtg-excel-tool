<?php

// Config
$allIdentifiersName = 'allIdentifiers.json';
$cardPricesName = 'AllPricesToday.json';
$csvStartingName = 'cards_start.csv';
$csvWithUuidName = 'cards_with_uuid.csv';
$csvWithPricesName = 'cards_with_uuid_and_prices.csv';

// Load all Identifiers
$json = json_decode(file_get_contents($allIdentifiersName), true);
$data = $json['data'] ?? null;
if(is_null($data)){
    echo 'Data is empty!';
    return;
}

// Simplify data to array [name||number||set => uuid]
$simpleData = [];
foreach($data as $uuid => $row){
    $simpleData[$row['name'] . "||" .$row['number'] . "||" . $row['setCode']] = $uuid;
    unset($data[$uuid]);
}

// Prepare new data from loaded csv starting file to attach uuid
$newData = [];
$row = 1;
$missingCount = 0;
if (($handle = fopen($csvStartingName, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",", '"', "\\")) !== FALSE) {
        if($row == 1){
            $newData[$row-1][] = $data[0];
            $newData[$row-1][] = $data[1];
            $newData[$row-1][] = $data[2];
            $newData[$row-1][] = $data[3];
            $newData[$row-1][] = $data[4];
            $newData[$row-1][] = $data[5];
            $newData[$row-1][] = 'uuid';
            $row++;
            continue;
        }

        $newData[$row-1][] = $data[0]; //Copies
        $newData[$row-1][] = $data[1]; //Name
        $newData[$row-1][] = $data[2]; //Rarity
        $newData[$row-1][] = $data[3]; //Number
        $newData[$row-1][] = $data[4]; //Set
        $newData[$row-1][] = $data[5]; //Foil?

        // attach uuid using name||number||set identifier
        if(isset($simpleData[$data[1] . "||" .$data[3] . "||" . $data[4]])){
            $newData[$row-1][] = $simpleData[$data[1] . "||" .$data[3] . "||" . $data[4]];
        }else{
            $newData[$row-1][] = null;
            $missingCount++;
            printf("missing: " . $data[1] . " || " .$data[3] . " || " . $data[4] . PHP_EOL);
        }

        $row++;
    }
    fclose($handle);
    printf('missing uuids: ' . $missingCount . PHP_EOL);

    // create csv file with uuids
    $fp = fopen($csvWithUuidName, 'w');    
    foreach ($newData as $rows) {
        fputcsv($fp, $rows, ",", '"', "\\");
    }    
    fclose($fp);
}

//load price data
$json = json_decode(file_get_contents($cardPricesName), true);
$data = $json['data'] ?? null;
if(is_null($data)){
    echo 'Data is empty!';
    return;
}

// Create simple data array [uuid => price]
$simpleData = [];
foreach($data as $uuid => $row){
    if(isset($row['paper']['cardmarket']['retail'])){
        $simpleData[$uuid] = $row['paper']['cardmarket']['retail'];
        unset($data[$uuid]);
    }
}

// Prepare new data from loaded csv with uuid file to attach prices
$newData = [];
$row = 1;
$missingCount = 0;
if (($handle = fopen($csvWithUuidName, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",", '"', "\\")) !== FALSE) {
        if($row == 1){
            $newData[$row-1][] = $data[0];
            $newData[$row-1][] = $data[1];
            $newData[$row-1][] = $data[2];
            $newData[$row-1][] = $data[3];
            $newData[$row-1][] = $data[4];
            $newData[$row-1][] = $data[5];
            $newData[$row-1][] = $data[6];
            $newData[$row-1][] = 'Price';
            $row++;
            continue;
        }

        $newData[$row-1][] = $data[0]; //Copies
        $newData[$row-1][] = $data[1]; //Name
        $newData[$row-1][] = $data[2]; //Rarity
        $newData[$row-1][] = $data[3]; //Number
        $newData[$row-1][] = $data[4]; //Set
        $newData[$row-1][] = $data[5]; //Foil?
        $newData[$row-1][] = $data[6]; //uuid

        // attach price by uuid
        if(is_null($data[6])){
            continue;
        }
        if(isset($simpleData[$data[6]])){
            // check for foil
            if($data[5]){
                if(isset($simpleData[$data[6]]['foil'])){
                    $newData[$row-1][] = number_format(reset($simpleData[$data[6]]['foil']),2,","," ");
                }else{
                    $missingCount++;
                    printf("missing: " . $data[1] . " || " .$data[3] . " || " . $data[4] . " || foil" . PHP_EOL);
                }
            }else{
                if(isset($simpleData[$data[6]]['normal'])){
                    $newData[$row-1][] = number_format(reset($simpleData[$data[6]]['normal']),2,","," ");
                }else{
                    $missingCount++;
                    printf("missing: " . $data[1] . " || " .$data[3] . " || " . $data[4] . " || normal" .  PHP_EOL);
                }
            }
        }else{
            $missingCount++;
            printf("missing: " . $data[1] . " || " .$data[3] . " || " . $data[4] . PHP_EOL);
        }

        $row++;
    }
    fclose($handle);
    printf('missing prices: ' . $missingCount . PHP_EOL);

    // create csv file with uuid
    $fp = fopen($csvWithPricesName, 'w');    
    foreach ($newData as $rows) {
        fputcsv($fp, $rows, ",", '"', "\\");
    }    
    fclose($fp);
}