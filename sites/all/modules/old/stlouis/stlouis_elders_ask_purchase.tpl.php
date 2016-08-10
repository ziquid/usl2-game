<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $fetch_header($game_user);
  include_once(drupal_get_path('module', $game) . '/game_defs.inc');
  
/*  if ($game == 'stlouis') {
  	
  	echo <<< EOF
<div class="title">
  Luck-free 4th
</div>
<div class="subtitle">
  Sorry!&nbsp; No $luck today!
</div>
<div class="subtitle">
  <a href="/$game/elders/$phone_id">
    <img src="/sites/default/files/images/{$game}_continue.png"/>
  </a>
</div>
EOF;

  	db_set_active('default');
    
    return;
  	
  }
*/  
  if ((strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== FALSE ) ||
     (strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== FALSE )) {
    
    echo <<< EOF
    
  <div class="elders-menu big">
    <div class="menu-option"><a href="https://www.paypal.com/buy/luck/10">Buy
      10 Luck (US $1.99)</a></div>
    <div class="menu-option"><a href="https://www.paypal.com/buy/luck/35">Buy
      35 Luck (US $5.99)</a></div>
    <div class="menu-option"><a href="https://www.paypal.com/buy/luck/150">Buy
      150 Luck (US $24.99)</a></div>
  </div>
  
EOF;

    db_set_active('default');
    
    return;
    
  }
    
  if ((strpos($_SERVER['HTTP_USER_AGENT'], 'Playbook') !== FALSE )) {
    
    echo <<< EOF
    
  <div class="elders-menu big">
    <div class="menu-option"><a
      href="javascript:qnx.callExtensionMethod('buy_luck_10');">Buy
      10 Luck (US $1.99)</a></div>
    <div class="menu-option"><a
      href="javascript:qnx.callExtensionMethod('buy_luck_35');">Buy
      35 Luck (US $5.99)</a></div>
    <div class="menu-option"><a
       href="javascript:qnx.callExtensionMethod('buy_luck_120');">Buy
      120 Luck (US $19.99)</a></div>
  </div>
  
EOF;

    db_set_active('default');
    
    return;
    
  }
  
  if (substr(arg(2), 0, 4) == 'nkc ') { // nook color - no in app purchases!
    
    echo <<< EOF
    
<div class="title">
Sorry!
</div>
<div class="subtitle">
The Nook Color does not support $luck purchases
</div>

<div class="subtitle">Normal PayPal $luck prices:</div>

<div class="subtitle" style="text-decoration: line-through;">
Purchase 10 $luck for US $1.99
</div>

<div class="subtitle" style="text-decoration: line-through;">
Purchase 35 $luck for US $5.99
</div>

<div class="subtitle" style="text-decoration: line-through;">
Purchase 150 $luck for US $24.99
</div>

<div class="subtitle" style="text-decoration: line-through;">
Purchase 320 $luck for US $49.99
</div>

<div class="subtitle">
  <a href="/$game/elders/$phone_id">
    <img src="/sites/default/files/images/{$game}_continue.png"/>
  </a>
</div>
EOF;

    db_set_active('default');
    
    return;
    
  } // nook
  
  if (stripos($_SERVER['HTTP_USER_AGENT'], 'GoogleIAP') !== FALSE) {
// support for Google IAPs

    if (stripos($_SERVER['HTTP_USER_AGENT'], 'PlayBook') !== FALSE) {
// BlackBerry with Android emulation

      echo <<< EOF
<div class="title">Purchase through BlackBerry Commerce</div>
<div class="elders-menu big">
  <div class="menu-option">
    <a href="iap://luck__10">Buy 10 Luck (US $1.99)</a>
  </div>
  <div class="menu-option">
    <a href="iap://luck_35">Buy 35 Luck (US $5.99)</a>
  </div>
  <div class="menu-option">
    <a href="iap://buy_luck_120">Buy 120 Luck (US $19.99)</a>
  </div>
  <div class="menu-option">
    <a href="iap://luck_320">Buy 320 Luck (US $49.99)</a>
  </div>
  </div>
<div class="title">
or purchase through PayPal
</div>
EOF;

    } else { // *real* Android with GoogleIAP

    echo <<< EOF
<div class="title">Purchase through Google Checkout</div>
<div class="elders-menu big">
  <div class="menu-option">
    <!--<a href="iap://buy_luck_10">Buy 10 Luck (US $1.99)</a>-->
    <a href="iap://luck__10">Buy 10 Luck (US $1.99)</a>
  </div>
  <div class="menu-option">
    <a href="iap://luck_35">Buy 35 Luck (US $5.99)</a>
  </div>
  <div class="menu-option">
    <a href="iap://luck.150">Buy 150 Luck (US $24.99)</a>
  </div>
  <div class="menu-option">
    <a href="iap://luck.320">Buy 320 Luck (US $49.99)</a>
  </div>
  </div>
<div class="title">
or purchase through PayPal
</div>
EOF;
    
  }

  }

// Win8/RT IAPs

  if (stripos($_SERVER['HTTP_USER_AGENT'], 'MSAppHost') !== FALSE) {

    echo <<< EOF
<div class="title">
  Purchase 10 $luck for US $1.99
</div>
<div class="purchase">
  <a href="javascript:window.parent.window.postMessage('buy-luck-10', '*');">
    <img src="https://www.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif"
      border="0" name="submit" alt="Pay with PayPal">
  </a>
</div>
<br/><br/>

<div class="title">
  Purchase 35 $luck for US $5.99
</div>
<div class="purchase">
  <a href="javascript:window.parent.window.postMessage('buy-luck-35', '*');">
    <img src="https://www.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif"
      border="0" name="submit" alt="Pay with PayPal">
  </a>
</div>
<br/><br/>

<div class="title">
  Purchase 150 $luck for US $24.99
</div>
<div class="purchase">
  <a href="javascript:window.parent.window.postMessage('buy-luck-150', '*');">
    <img src="https://www.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif"
      border="0" name="submit" alt="Pay with PayPal">
  </a>
</div>
<br/><br/>

<div class="title">
  Purchase 320 $luck for US $49.99
</div>
<div class="purchase">
  <a href="javascript:window.parent.window.postMessage('buy-luck-320', '*');">
    <img src="https://www.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif"
      border="0" name="submit" alt="Pay with PayPal">
  </a>
</div>
EOF;

    db_set_active('default');
    return;

  } // Win8/RT


  $nonce = date('Y-m-d-H-i-s-') . mt_rand();
  
  echo <<< EOF
<div class="title">
Purchase 10 $luck for US $1.99
</div>
<div class="purchase">
  <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_s-xclick">
    <input type="hidden" name="cs" value="1">
    <input type="hidden" name="cbt" value="Return to $game_name_full">
    <input type="hidden" name="cancel_return"
      value="http://stl2114.game.ziquid.com/$game/elders/$phone_id">
    <input type="hidden" name="notify_url"
      value="http://stl2114.game.ziquid.com/$game/elders_do_purchase/$phone_id/10/$nonce">
    <input type="hidden" name="return"
      value="http://stl2114.game.ziquid.com/$game/elders/$phone_id">
    <input type="hidden" name="hosted_button_id" value="DGL4LM2ZH4DZA">
    <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif"
      border="0" name="submit" alt="Pay with PayPal">
    <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif"
    width="1" height="1">
  </form>
</div>

<div class="title">
Purchase 35 $luck for US $5.99
</div>
<div class="purchase">
  <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_s-xclick">
    <input type="hidden" name="cs" value="1">
    <input type="hidden" name="cbt" value="Return to $game_name_full">
    <input type="hidden" name="cancel_return"
      value="http://stl2114.game.ziquid.com/$game/elders/$phone_id">
    <input type="hidden" name="notify_url"
      value="http://stl2114.game.ziquid.com/$game/elders_do_purchase/$phone_id/35/$nonce">
    <input type="hidden" name="return"
      value="http://stl2114.game.ziquid.com/$game/elders/$phone_id">
    <input type="hidden" name="hosted_button_id" value="6AY77UK7KP2VU">
    <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif"
      border="0" name="submit" alt="Pay with PayPal">
    <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif"
    width="1" height="1">
  </form>
</div>

<div class="title">
Purchase 150 $luck for US $24.99
</div>
<div class="purchase">
  <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_s-xclick">
    <input type="hidden" name="cs" value="1">
    <input type="hidden" name="cbt" value="Return to $game_name_full">
    <input type="hidden" name="cancel_return"
      value="http://stl2114.game.ziquid.com/$game/elders/$phone_id">
    <input type="hidden" name="notify_url"
      value="http://stl2114.game.ziquid.com/$game/elders_do_purchase/$phone_id/150/$nonce">
    <input type="hidden" name="return"
      value="http://stl2114.game.ziquid.com/$game/elders/$phone_id">
    <input type="hidden" name="hosted_button_id" value="PX5YB99UWYP32">
    <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif"
      border="0" name="submit" alt="Pay with PayPal">
    <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif"
    width="1" height="1">
  </form>
</div>

<div class="title">
Purchase 320 $luck for US $49.99
</div>
<div class="purchase">
  <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_s-xclick">
    <input type="hidden" name="cs" value="1">
    <input type="hidden" name="cbt" value="Return to $game_name_full">
    <input type="hidden" name="cancel_return"
      value="http://stl2114.game.ziquid.com/$game/elders/$phone_id">
    <input type="hidden" name="notify_url"
      value="http://stl2114.game.ziquid.com/$game/elders_do_purchase/$phone_id/320/$nonce">
    <input type="hidden" name="return"
      value="http://stl2114.game.ziquid.com/$game/elders/$phone_id">
    <input type="hidden" name="hosted_button_id" value="33MQY96PK4246">
    <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif"
      border="0" name="submit" alt="Pay with PayPal">
    <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif"
    width="1" height="1">
  </form>
</div>
EOF;

  if (ip_address() == '72.177.116.233') { // 2000 luck for kenny

    echo <<< EOF
<div class="title">
Purchase 2000 $luck for US $250
</div>
<div class="purchase">
  <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_s-xclick">
    <input type="hidden" name="cs" value="1">
    <input type="hidden" name="cbt" value="Return to $game_name_full">
    <input type="hidden" name="cancel_return"
      value="http://stl2114.game.ziquid.com/$game/elders/$phone_id">
    <input type="hidden" name="notify_url"
      value="http://stl2114.game.ziquid.com/$game/elders_do_purchase/$phone_id/2000/$nonce">
    <input type="hidden" name="return"
      value="http://stl2114.game.ziquid.com/$game/elders/$phone_id">
    <input type="hidden" name="hosted_button_id" value="MXF6YM27PZK7A">
    <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif"
      border="0" name="submit" alt="Pay with PayPal">
    <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif"
    width="1" height="1">
  </form>
</div>
EOF;

  }

  db_set_active('default');
