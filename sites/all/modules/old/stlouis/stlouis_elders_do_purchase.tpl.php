<?php

  global $game, $phone_id, $purchasing_luck;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

// mail('joseph@cheek.com', 'testing 5', 'got here!');

  $purchasing_luck = TRUE;
  $game_user = $fetch_user();

  if (($_SERVER['REMOTE_ADDR'] == '66.211.170.66') ||
    ($_SERVER['REMOTE_ADDR'] == '173.0.81.1') ||
    ($_SERVER['REMOTE_ADDR'] == '173.0.81.33') ||
    ($_SERVER['REMOTE_ADDR'] == '173.0.82.126') || // <-- paypal sandbox
    (strpos($_SERVER['HTTP_USER_AGENT'], 'com.cheek.stlouis') !== FALSE) ||
    (strpos($_SERVER['HTTP_USER_AGENT'], 'com.cheek.celestialglory')
      !== FALSE)) {


    if (arg(4) == 'withAppleReceipt') { // iOS receipt data attached

      $receipt_data = $_POST['receiptdata'];
      $receipt_json = json_encode(array('receipt-data' => $receipt_data));
      $appleURL = 'https://buy.itunes.apple.com/verifyReceipt';

// call apple URL to check receipt
      $params = array('http' => array(
        'method' => 'POST',
        'content' => $receipt_json,
      ));

      $ctx = stream_context_create($params);
      $fp = fopen($appleURL, 'rb', false, $ctx);

      if (!$fp)
        mail('joseph@cheek.com', 'unable to verify Apple receipt',
          'could not fopen() ' . $appleURL . '.');

      $response_json = stream_get_contents($fp);

      if ($response_json === FALSE)
        mail('joseph@cheek.com', 'unable to verify Apple receipt',
          'could not read data from ' . $appleURL . 'due to error
' . $php_errormsg . '.');

      $response = json_decode($response_json);
ob_start();
var_dump($response);
$response_dump = ob_get_contents();
ob_end_clean();

      mail('joseph@cheek.com', 'iOS receipt check response', 
        'receipt_data is ' . $receipt_data . 
        'response is: ' . $response_dump . '
response_json is: ' . $response_json . '
response status is: ' . $response->status);

      if ($response->status !== 0) { // uhoh!  receipt not validated!
        echo 'NO';
        exit;
      }

      if (substr($response->receipt->bid, 0, 10) !== 'com.cheek.') { 
// uhoh!  hack
// FIXME -- debit karma
        echo 'NO';
        exit;
      }

    } // check iOS receipt data

    $luck = 10;
    
    if (arg(3) == '30') $luck = 30; // paypal
    
    if (arg(3) == '35') $luck = 35; // paypal
    if (arg(3) == 'luck_35') $luck = 35; // google
    if (arg(3) == 'buy_luck_35') $luck = 35; // blackberry
    if (arg(3) == 'com.cheek.stlouis.luck.35') $luck = 35; // apple
    if (arg(3) == 'com.cheek.celestialglory.luck.35') $luck = 35; // apple
    if (arg(3) == 'com.cheek.celestial_glory.luck.35') $luck = 35; // apple
    
    if (arg(3) == 'buy_luck_120') $luck = 120; // blackberry
    
    if (arg(3) == '130') $luck = 130; // paypal
    
    if (arg(3) == '150') $luck = 150; // paypal
    if (arg(3) == 'luck.150') $luck = 150; // google
    if (arg(3) == 'com.cheek.stlouis.luck.150') $luck = 150; // apple
    if (arg(3) == 'com.cheek.celestialglory.luck.150') $luck = 150; // apple
    if (arg(3) == 'com.cheek.celestial_glory.luck.150') $luck = 150; // apple
    
    if (arg(3) == '320') $luck = 320; // paypal
    if (arg(3) == 'luck.320') $luck = 320; // google

// stop iOS luck hacking
    if (arg(4) == 'abc123') $luck = 0;

// mail('joseph@cheek.com', 'testing 30', "luck is $luck");
    
    $sql = 'update users set luck = luck + %d
      where id = %d;';
    $result = db_query($sql, $luck, $game_user->id);
  
    $sql = 'insert into purchases (fkey_users_id, purchase)
      values (%d, "%s");';
    $msg = 'User ' . $game_user->username . ' purchased ' . $luck .
      ' Luck (currently ' . $game_user->luck . ') at URL ' .
            $_SERVER['REQUEST_URI'] . ' (IP Address ' . $_SERVER['REMOTE_ADDR']
            . ')'; 
    $result = db_query($sql, $game_user->id, $msg);
    
    mail('joseph@cheek.com', $game . ' Luck purchase', $msg);

  }
//  drupal_goto($game . '/elders/' . $phone_id);

  echo 'YES';
  
  exit;
