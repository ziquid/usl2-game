#!/bin/sh

set -x

# copy stlouis to celestial glory
sed -e 's,stlouis/,celestial_glory/,gi' \
  -e 's,St. Louis,Celestial Glory,g' \
  -e 's,stlouis_,celestial_glory_,g' \
  -e 's,stlouis content,celestial_glory content,g' \
  -e 's,#stlouis,#celestial_glory,g' \
  -e "s/module....stlouis/module', 'celestial_glory/g" \
  -e 's,Uprising: ,,g' \
  -e 's,game_stlouis,game_celestial_glory,g' \
  -e 's,Influence,Spirituality,g' \
  -e 's,function celestial_glory_cron,function celestial_glory_copy_of_stlouis_cron,' \
  -e 's,real_celestial_glory_cron,celestial_glory_cron,' \
  < ../stlouis/stlouis.module > celestial_glory.module

sed -e 's,stlouis/,celestial_glory/,gi' \
  -e 's,St. Louis,Celestial Glory,g' \
  -e 's,stlouis_,celestial_glory_,g' \
  -e 's,stlouis content,celestial_glory content,g' \
  -e 's,#stlouis,#celestial_glory,g' \
  -e "s/module....stlouis/module', 'celestial_glory/g" \
  -e 's,Uprising: ,,g' \
  -e 's,game_stlouis,game_celestial_glory,g' \
  -e 's,Influence,Spirituality,g' \
  -e 's,function celestial_glory_cron,function celestial_glory_copy_of_stlouis_cron,' \
  -e 's,real_celestial_glory_cron,celestial_glory_cron,' \
  < ../stlouis/menu.inc > menu.inc

sed -e 's,stlouis/,celestial_glory/,gi' \
  -e 's,St. Louis,Celestial Glory,g' \
  -e 's,stlouis_,celestial_glory_,g' \
  -e 's,stlouis content,celestial_glory content,g' \
  -e 's,#stlouis,#celestial_glory,g' \
  -e "s/module....stlouis/module', 'celestial_glory/g" \
  -e 's,Uprising: ,,g' \
  -e 's,game_stlouis,game_celestial_glory,g' \
  -e 's,Influence,Spirituality,g' \
  -e 's,function celestial_glory_cron,function celestial_glory_copy_of_stlouis_cron,' \
  -e 's,real_celestial_glory_cron,celestial_glory_cron,' \
  < ../stlouis/api.inc > api.inc

sed -e 's,1ca443,bf6f2f,gi' \
  -e 's,stlouis_wise_old_man,celestial_glory_lehi,g' \
  -e 's,stlouis_header,celestial_glory_header,g' \
  -e 's,000000,1c130b,g' -e 's,111811,45331f,g' \
  -e 's,0e5221,442211,gi' -e 's,188433,693318,g' \
  -e 's,0d0f0d,24170e,gi' -e 's,1aa443,daa443,gi' \
  < ../stlouis/stlouis.css > celestial_glory.css

