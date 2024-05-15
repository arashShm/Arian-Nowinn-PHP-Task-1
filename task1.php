<?php

function getCityData($city)
{

    //Here we define allowed city and check them
    $allowed_cities = ['Zanjan', 'Tehran', 'Ardabil', 'Isfahan', 'Qazvin'];
    if (!in_array($city, $allowed_cities)) {
        throw new Exception('Invalid city');
    }

    //accessing the api and receiving data 
    $url = 'https://my.arian.co.ir/bpmsback/api/1.0/arian/arian/exercise/product-prices';
    $headers = array(
        'Referer:https://my.arian.co.ir/'
    );

    // Make GET request to API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);



    // Calculate average price
    $totalPrice = 0;
    $count = 0;
    foreach ($data as $item) {
            $totalPrice += $item['price'];
            $count++;
            
    }
    $averagePrice = $totalPrice / $count;

    // Calculate total price and find the cheapest shop
    $cheapestShop = '';
    $cheapestPrice = PHP_INT_MAX;
    foreach ($data as $item) {
        if ($item['city'] == $city) {
            $item['shipping'] = 0 ;
            $buyingTotalPrice = $item['price'] + $item['shipping'];
            // var_dump($item['shipping'] );
            // die;
            if ($buyingTotalPrice < $cheapestPrice) {
                $cheapestShop = $item['store'];
                $cheapestPrice = $buyingTotalPrice;
            }
        }
    }


    // Save calculation results to a file
    $result = "City: $city\nAverage Price: $averagePrice\nCheapest Shop: $cheapestShop\nTotal Price: $cheapestPrice\n\n";
    file_put_contents('calculation_results.txt', $result, FILE_APPEND);


    //Returning the data in view
    return [
        'average_price' => $averagePrice,
        'cheapest_shop' => $cheapestShop,
        'total_price' => $cheapestPrice
    ];
}

// Example usage
$city = 'Zanjan';
$result = getCityData($city);
print_r($result);
