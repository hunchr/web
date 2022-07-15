<?php
require '.php';

exit(
'<main data-url="signup" data-title="'.v('Signup').'" class="center">
    <form action="#todo" method="post">
        <input type="text" placeholder="'.v('Username').'" maxlength="20" spellcheck="false" autocomplete="off" autofocus>
        <input type="email" placeholder="'.v('Email').'" maxlength="100" spellcheck="false" autocomplete="off">
        <input type="password" placeholder="'.v('Password').'" maxlength="1000" autocomplete="new-password">
        <input type="password" placeholder="'.v('Confirm Password').'" maxlength="1000" autocomplete="new-password">
        <button data-f="Lb" class="btn">'.v('Sign Up').'</button>
        <div>'.v('Already have an account?').'&nbsp;<button data-f="_$" data-n="login" class="a">'.v('Log In').'</button></div>
    </form>
</main>'
);

// exit(
// '<main data-url="signup" data-title="'.v('Signup').'" class="center">
//     <form action="#todo" method="post">
//         <input type="text" placeholder="'.v('Username').'" maxlength="20" spellcheck="false" autocomplete="off" autofocus>
//         <input type="email" placeholder="'.v('Email').'" maxlength="100" spellcheck="false" autocomplete="off">
//         <input type="password" placeholder="'.v('Password').'" maxlength="1000" autocomplete="new-password">
//         <input type="password" placeholder="'.v('Confirm Password').'" maxlength="1000" autocomplete="new-password">
//         <button data-f="Lb" class="btn">'.v('Sign Up').'</button>
//         <div>'.v('Already have an account?').'&nbsp;<button data-f="_$" data-n="login" class="a">'.v('Log In').'</button></div>
//     </form>
// </main>'
// );
?>
