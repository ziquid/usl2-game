<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game = check_plain(arg(0));
  
//  $game_user = $fetch_user();
  
 /* if (($_SERVER['REMOTE_ADDR'] == '66.211.170.66') ||
    (strpos($_SERVER['HTTP_USER_AGENT'], 'com.cheek.stlouis') !== FALSE) ||
    (strpos($_SERVER['HTTP_USER_AGENT'], 'com.cheek.celestial_glory')
      !== FALSE)) {
  	
  	$luck = 10;
  	
  	if (arg(3) == '30') $luck = 30;
  	
  	if (arg(3) == 'buy_luck_35') $luck = 35;
  	if (arg(3) == 'com.cheek.stlouis.luck.35') $luck = 35;
  	if (arg(3) == 'com.cheek.celestial_glory.luck.35') $luck = 35;
  	
  	if (arg(3) == 'buy_luck_120') $luck = 120;
  	
  	if (arg(3) == '130') $luck = 130;
  	
  	if (arg(3) == 'com.cheek.stlouis.luck.150') $luck = 150;
  	if (arg(3) == 'com.cheek.celestial_glory.luck.150') $luck = 150;
  	
	  $sql = 'update users set luck = luck + %d
	    where id = %d;';
	  $result = db_query($sql, $luck, $game_user->id);
	
	  $sql = 'insert into purchases (fkey_users_id, purchase)
	  	values (%d, "%s");';*/
//	  $msg = 'User ' . $game_user->username . ' purchased ' . $luck .
//	    ' Luck (currently ' . $game_user->luck . ') at URL ' . $_SERVER['REQUEST_URI']; 
//	  $result = db_query($sql, $game_user->id, $msg);
	  
    $msg = print_r($_POST, true);
	  mail('joseph@cheek.com', $game . ' Google Checkout Luck purchase', $msg);

//  }
//  drupal_goto($game . '/elders/' . $phone_id);

  echo '<notification-acknowledgement xmlns="http://checkout.google.com/schema/2"
    serial-number="' . $_POST['serial-number'] . '" />';
  
  exit;