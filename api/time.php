<?php

// Process counter cookie
$iCnt = intval($_COOKIE['cnt'] ?? '0');
$iCnt++;
setcookie('cnt', $iCnt);

// Output updated time
echo date('Y-m-d H:i:s');

// Output button based onn current count
$color = (['red', 'green', 'blue'])[$iCnt % 3];

echo <<<HTML
        \t<br />
        \t<button class="msg-button" data-msg="$iCnt clicks so far">$iCnt: $color</button>
    HTML;
