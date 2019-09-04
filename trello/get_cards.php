<?

$key = "2ecc021a785866a9f027a14fd849a64d";
$token = "af08f69820b3ec1eb3942fa859f949300dd15b72681d235983446a89ce8bbb7c";
$boardID = "THUvte8Q";

$request = "https://api.trello.com/1/boards/$boardID/members?key=$key&token=$token";

$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, $request); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
$output = curl_exec($ch); 
curl_close($ch);      

$users = json_decode($output, true);
//print_r($users);

$userArr = ['55793c298cf2763e9412a9ba', '5d22bcd268801436f33aab72', '5d240c5861a8013810222dd5'];

foreach ($userArr as $userID ) {
    //$userID = $valueUser['id'];
    $request = "https://api.trello.com/1/members/$userID/cards?filter=visible&key=$key&token=$token";
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $request); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $output = curl_exec($ch); 
    curl_close($ch);
    //echo $output;
    $cards = json_decode($output, true);

    print_r($cards);
    
    echo "<b>".$valueUser['fullName']."</b><br>";
    foreach ($cards as $keyCard => $valueCard) {
        echo $valueCard['name']."<br>";
    }
    echo "<br>";
}
