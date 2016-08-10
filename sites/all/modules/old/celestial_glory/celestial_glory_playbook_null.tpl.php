<?php

  echo <<< EOF
<p>
Uhoh!&nbsp; This game requires that you allow us to read your BlackBerry
PlayBook's Device Identifying Information.&nbsp; If we can't do that, we can't
tell your PlayBook from everyone else's in the game and provide a personalized
playing experience for you.
</p>
<p class="second">In the <strong>Security</strong> tab of your PlayBook's
settings page, please select <strong>Application Permissions</strong> and
allow <strong>Device Identifying Information</strong> for this game.&nbsp;
Then close and relaunch this game, and you will be able to play normally.
</p>
<p class="second">Thanks!</p>
<p class="second">The CheekDotCom team</p>
EOF;

  db_set_active('default');
