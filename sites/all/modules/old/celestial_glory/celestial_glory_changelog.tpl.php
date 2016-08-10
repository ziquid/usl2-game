<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $fetch_header($game_user);
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

  echo <<< EOF
<div class="news">
  <a href="/$game/help/$arg2" class="button">Help</a>
  <a href="/$game/changelog/$arg2" class="button active">Changelog</a>
</div>

<div class="help">
  <div class="title">
  Celestial Glory Changelog
  </div>

  <div class="subtitle">
    v2.1.4, 27 May 2016
  </div>
  <ul>
    <li>
      Better notification to developers, support for in-game errors.
    </li>
  </ul>

  <div class="subtitle">
    v2.1.3, 25 May 2016
  </div>
  <ul>
    <li>
      Add another competency.
    </li>
    <li>
      Show how many competencies found on competency screen.
    </li>
  </ul>

  <div class="subtitle">
    v2.1.2, 25 May 2016
  </div>
  <ul>
    <li>
      Decrease energy time from five minutes to four minutes.
    </li>
    <li>
      Fix FB login.
    </li>
  </ul>

  <div class="subtitle">
    v2.1.1, 20 May 2016
  </div>
  <ul>
    <li>
      Highlight competencies that have been recently increased.
    </li>
  </ul>

  <div class="subtitle">
    v2.1, 20 May 2016
  </div>
  <ul>
    <li>
      Competencies!!!
    </li>
  </ul>

  <div class="subtitle">
    v2.0.22, 16 May 2016
  </div>
  <ul>
    <li>
      Center some menus.
    </li>
  </ul>

  <div class="subtitle">
    v2.0.21, 12 May 2016
  </div>
  <ul>
    <li>
      Allow players to use passwords to authenticate after loading an update
      to the game client.&nbsp; Previously a player would have to e-mail
      zipport in almost all situations.
    </li>
  </ul>

  <div class="subtitle">
    v2.0.20, 6 May 2016
  </div>
  <ul>
    <li>
      Give another chance to do &quot;Love in the Desert&quot; for Mother's Day.
    </li>
  </ul>

  <div class="subtitle">
    v2.0.19, 29 Apr 2016
  </div>
  <ul>
    <li>
      Support for the &quot;Speed Energy&quot; event.
    </li>
  </ul>

  <div class="subtitle">
    v2.0.18, 28 Apr 2016
  </div>
  <ul>
    <li>
      Two new defense aides exist: Radiant Bronze Shield and Brilliant
      Bronze Shield.&nbsp; Use both to help defend your seats from
      challengers.
    </li>
  </ul>

  <div class="subtitle">
    v2.0.17, 28 Apr 2016
  </div>
  <ul>
    <li>
      You can now perform the second round for &quot;Food and Faith, Pt. 2&quot;
      quests.&nbsp; No second-round bonus exists yet.
    </li>
  </ul>

  <div class="subtitle">
    v2.0.16, 26 Apr 2016
  </div>
  <ul>
    <li>
      The map now automatically updates each time a Chief Priest changes.&nbsp;
      Previously the map only updated every fifteen minutes.
    </li>
  </ul>

  <div class="subtitle">
    v2.0.15, 25 Apr 2016
  </div>
  <ul>
    <li>
      Added a second-round bonus for &quot;Finding the Liahona&quot;.
    </li>
    <li>
      Made most second-round bonus items give extra energy every 5 minutes.
    </li>
  </ul>

  <div class="subtitle">
    v2.0.14, 25 Apr 2016
  </div>
  <ul>
    <li>
      Fewer extra resident voters.
    </li>
    <li>
      Region piety moves toward 50 more quickly.
    </li>
  </ul>

  <div class="subtitle">
    v2.0.13, 19 Apr 2016
  </div>
  <ul>
    <li>
      Fix leaderboard.
    </li>
  </ul>

  <div class="subtitle">
    v2.0.12, 16 Apr 2016
  </div>
  <ul>
    <li>
      Top 20 list shows all Amethyst gatherers, even if you aren't in the
      top 20.
    </li>
    <li>
      User profile page shows how much Amethyst you have gathered.
    </li>
  </ul>

  <div class="subtitle">
    v2.0.11, 15 Apr 2016
  </div>
  <ul>
    <li>
      Added the &quot;Create a sign&quot; action for Chief Priests.
    </li>
  </ul>

  <div class="subtitle">
    v2.0.10, 15 Apr 2016
  </div>
  <ul>
    <li>
      Gathering Amethyst Event
    </li>
  </ul>

  <div class="subtitle">
    v2.0.9, 14 Apr 2016
  </div>
  <ul>
    <li>
      Made the version text on the home page a link to the changelog.
    </li>
    <li>
      Fixed the size of merchants icon on the Offices page.
    </li>
  </ul>

  <div class="subtitle">
    v2.0.8, 13 Apr 2016
  </div>
  <ul>
    <li>
      Added &quot;Offices&quot; link on home page, removed &quot;Forum&quot;
      link.
    </li>
  </ul>

  <div class="subtitle">
    v2.0.7, 13 Apr 2016
  </div>
  <ul>
    <li>
      Changed Merchants logo.
    </li>
  </ul>

  <div class="subtitle">
    v2.0.6, 12 Apr 2016
  </div>
  <ul>
    <li>
      Raised the money limit for each piece of luck spent.
    </li>
  </ul>

  <div class="subtitle">
    v2.0.5, 12 Apr 2016
  </div>
  <ul>
    <li>
      Altered &quot;Run someone out of the region&quot; to use only 1/3 of
      the level of the user for actions, with a max of 25 actions.
    </li>
  </ul>

  <div class="subtitle">
    v2.0.4, 11 Apr 2016
  </div>
  <ul>
    <li>
      Fixed luck purchases on Apple devices, re-added game to Apple Store.
    </li>
    <li>
      Added a &quot;Continue&quot; button to the top of the debates result
      screen,
      as I was tired of having to scroll down the entire page each time
      I debated someone.
    </li>
  </ul>

  <div class="subtitle">
    v2.0.3, 09 Apr 2016
  </div>
  <ul>
    <li>
      Support for 411dip-width screens, such as the Galaxy Note 5.
    </li>
  </ul>

  <div class="subtitle">
    v2.0.2, 09 Apr 2016
  </div>
  <ul>
    <li>
      Added &quot;Get challenge results&quot; action (shows detailed results of
      the last five office challenges against you)
    </li>
    <li>
      Minor formatting changes &mdash; added commas on some numbers on the
      Profile page.
    </li>
  </ul>

  <div class="subtitle">
    v2.0.1, 07 Apr 2016
  </div>
  <ul>
    <li>
      Fix minor display bug &mdash; menus were shown in the center of the
      screen on some devices
    </li>
  </ul>

  <div class="subtitle">
    v2.0.0, 06 Apr 2016
  </div>
  <ul>
    <li>
      Merchant Quests!!!
    </li>
  </ul>

  <div class="subtitle">
    v1.94.3, 30 Mar 2016
  </div>
  <ul>
    <li>
      Added support for 100-energy Quests
    </li>
  </ul>

  <div class="subtitle">
    v1.94.2, 19 Feb 2016
  </div>
  <ul>
    <li>
      Completed Love in the Desert quest group
    </li>
    <li>
      Added two and three-heart items
    </li>
  </ul>

  <div class="subtitle">
    v1.94.1, 18 Feb 2016
  </div>
  <ul>
    <li>
      Changed support email address to <strong>zipport@ziquid.com</strong>
    </li>
    <li>
      Added more words to profanity filter
    </li>
  </ul>

  <div class="subtitle">
    v1.94, 06 Feb 2016
  </div>
  <ul>
    <li>
      New side quests group: Love in the Desert
    </li>
  </ul>

  <div class="subtitle">
    v1.93, 17 Dec 2015
  </div>
  <ul>
    <li>
      New quests: Added the &quot;Your father asks the Lord&quot;, &quot;The
      Lord tells your father&quot;, and &quot;Inspecting the Liahona&quot;
      quests as the third, fourth, and fifth quests in the &quot;Food and Faith,
      Pt. 2&quot; quest group, with their associated loot.
    </li>
    <li>
      New supplies: Faith, Diligence, and Heed.
    </li>
  </ul>

  <div class="subtitle">
    v1.92, 12 Dec 2015
  </div>
  <ul>
    <li>
      New quest: Added the &quot;And ask your father&quot; quest as the second
      quest in the &quot;Food and Faith, Pt. 2&quot; quest group
    </li>
    <li>
      Bugfix: Fixed an issue where previously purchased/looted supplies would
      not show in your inventory if they could not be purchased/looted
      currently.
    </li>
    <li>
      Bugfix: Fixed an issue where you could try to sell items even if you had
      none.
    </li>
  </ul>

  <div class="subtitle">
    v1.91.3, 10 Dec 2015
  </div>
  <ul>
    <li>
      New quest: Added the &quot;You make a bow&quot; quest, with its associated
      supplies and loot, as the first quest in the &quot;Food and Faith,
      Pt. 2&quot; quest group
    </li>
    <li>
      New supplies: Fine Steel Bow, Nephi's Fine Steel Bow, Sling, and
      Wooden Bow
    </li>
  </ul>

  <div class="subtitle">
    v1.91.2, 7 Dec 2015
  </div>
  <ul>
    <li>
      Added loot for the &quot;Go Hunting&quot; quest
    </li>
    <li>
      Added a question completion bonus for finishing the &quot;Food and Faith,
      Pt.1&quot; quest group
    </li>
    <li>
      Formatted the supplies cost amounts so that they are easier to read
    </li>
  </ul>

  <div class="subtitle">
    v1.91.1, 7 Dec 2015
  </div>
  <ul>
    <li>
      Gave Merchants an extra 1% Daily Bonus
    </li>
    <li>
      Formatted Daily Bonus amount so that it is easier to read
    </li>
  </ul>

  <div class="subtitle">
    v1.91, 27 Nov 2015
  </div>
  <ul>
    <li>
      Support for Android 6
    </li>
  </ul>

  <div class="subtitle">
    v1.90.3, 29 Aug 2015
  </div>
  <ul>
    <li>
      Added message box to 'All' tab on home page
    </li>
  </ul>

  <div class="subtitle">
    v1.90.2, 10 Aug 2015
  </div>
  <ul>
    <li>
      Made the home page news roll filter fancier
    </li>
  </ul>

  <div class="subtitle">
    v1.90.1, 10 Aug 2015
  </div>
  <ul>
    <li>
      Added a few more items to the banned words list
    </li>
    <li>
      Removed the 'Forum' link
    </li>
    <li>
      Added a version string on the home page
    </li>
    <li>
      Centered the &quot;News&quot; buttons on the home page
    </li>
  </ul>

  <div class="subtitle">
    v1.90.0, 08 Aug 2015
  </div>
  <ul>
    <li>
      Added this changelog
    </li>
    <li>
      Added <strong>Merchant Comprehension</strong> as a second-round bonus
      for Quest Group 3, <strong>Retrieving the Records, Pt. 2</strong>
    </li>
  </ul>



</div>
EOF;

  db_set_active('default');
