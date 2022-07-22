<?php
require '.php';

exit(
'<main data-url="login" data-title="'.$v[0].'" class="center">
<div class="f">
    <input type="text" placeholder="'.$v[1].'" maxlength="20" spellcheck="false" autocomplete="username" autofocus>
    <input type="password" placeholder="'.$v[2].'" maxlength="1000" autocomplete="current-password">
    <button data-f="La" class="btn">'.$v[3].'</button>
    <div>'.$v[4].'&nbsp;<button data-f="_$" data-n="signup" class="a">'.$v[5].'</button></div>
</div>
</main>'
);
?>
