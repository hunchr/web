<?php
require '.php';

exit(
'<main data-url="signup" data-title="'.$v[0].'" class="center">
    <div class="f">
        <input type="text" placeholder="'.$v[1].'" maxlength="20" spellcheck="false" autocomplete="off" autofocus>
        <input type="email" placeholder="'.$v[2].'" maxlength="100" spellcheck="false" autocomplete="off">
        <input type="password" placeholder="'.$v[3].'" maxlength="1000" autocomplete="new-password">
        <input type="password" placeholder="'.$v[4].'" maxlength="1000" autocomplete="new-password">
        <button data-f="Lb" class="btn">'.$v[5].'</button>
        <div>'.$v[6].'&nbsp;<button data-f="_$" data-n="login" class="a">'.$v[7].'</button></div>
    </div>
    <div class="f hidden">
        <input type="text" placeholder="'.$v[8].'" maxlength="8" spellcheck="false" autocomplete="off">
        <button data-f="Lc" class="btn">'.$v[9].'</button>
        <div>'.$v[10].'</div>
    </div>
</main>'
);
?>
