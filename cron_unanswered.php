<?php

include '/var/www/promobot/data/www/db.promo-bot.ru/new/include/config.inc.php';
include '/var/www/promobot/data/www/db.promo-bot.ru/new/page/telegram.php';

$telegramAPI->getUnanswered();