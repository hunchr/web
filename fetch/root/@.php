<?php
require '.php';
$name = $q;

if (!preg_match('/^@[\w.-]{2,20}$/', $name)) e404();

// ----- Profile -----
// $q =
// 'SELECT s.*, UNIX_TIMESTAMP(s.created_at) AS created_at,'
// .($uid ?
//     'IF ((SELECT uid FROM liked_subs l WHERE l.uid = '.$uid.' AND l.sid = s.id), TRUE, NULL) AS liked':
//     'NULL AS liked'
// ).'
// FROM subs s
// WHERE s.id = (SELECT uid FROM users u WHERE u.name = "'.$name.'");';

// conn(
//     false,
//     true,
//     function($q) {
//         global $name;
//         global $fetch;

//         $fetch .= '
//         <main>
//         <article class="p">
//             todo: profile
//         </article>
//         '; // TODO
//     }
// );

// ----- Posts -----
$q =
'SELECT p.*, UNIX_TIMESTAMP(p.created_at) AS created_at,
IF (p.pid, (SELECT c.name FROM communities c WHERE c.cid = p.pid), NULL) as cname,'
.($uid ?
    'IF ((SELECT NULL FROM liked_posts l WHERE l.uid = '.$uid.' AND l.pid = p.id), " class=\"l\"", NULL) AS liked':
    'NULL AS liked'
).'
FROM posts p
WHERE p.uid = (SELECT uid FROM users u WHERE u.name = "'.$name.'")
AND NOT p.type = 0
ORDER BY p.created_at DESC LIMIT 5;';

function type($type) {
    switch  ($type) {
        case 2:
            return '[IMG]';
        case 3:
            return '[VIDEO]';
        case 4:
            return '[AUDIO]';
        default:
            return '[COMING SOON]';
    }
};

conn(
    'this user hasn\'t posted anything',
    false,
    function($q) {
        global $name;
        global $fetch;

        $fetch .= '
        <article class="p">
            <div>
                <button data-f="pa">'.($q['cname'] ? '&'.$q['cname'] : '@'.$name).'</button>&nbsp;•&nbsp;
                <button data-f="pb">'.fdate($q['created_at']).'</button>&nbsp;
                <button data-f="pc"><svg viewBox="0 0 8 2"><circle cx="1" cy="1" r="1"/><circle cx="7" cy="1" r="1"/><circle cx="4" cy="1" r="1"/></svg></button>
            </div>
            <h2>'.fstring($q['title']).'</h2>
            <p>'.fstring($q['content']).'</p>
            '.($q['type'] === 1 ? '' : '<span class="m">'.type($q['type']).'</span>').'
            <div data-id="'.$q['id'].'">
                <button data-f="pd"'.$q['liked'].'><svg viewBox="0 0 8 7.5"><path d="m4 7.5 3.56-3.93c.67-.91.56-2.2-.29-2.98-.92-.84-2.34-.77-3.18.14-.04.04-.05.09-.08.13-.03-.04-.06-.09-.09-.13C3.07-.18 1.65-.25.73.59c-.86.78-.96 2.06-.3 2.98"/></svg>'.fnumber($q['likes']).'</button>
                <button data-f="pe"><svg viewBox="0 0 7.5 7.5"><path d="M0 0v7.5L2.5 6h5V0H0z"/></svg>'.fnumber($q['replies']).'</button>
                <button data-f="pf"><svg viewBox="0 0 8 7.61"><path d="m4 6.31-2.47 1.3L2 4.86 0 2.91l2.76-.41L4 0l1.24 2.5L8 2.91 6 4.86l.47 2.75L4 6.31z"/></svg>Save</button>
                <button data-f="pg"><svg viewBox="0 0 8 6.5"><path d="M7 4C5.64 2.06 3.53 1.6 3 1.5V0L0 3l3 3V4.5c.55-.05 1.71-.09 3 .5 1.01.46 1.66 1.1 2 1.5-.09-.6-.33-1.55-1-2.5z"/></svg>Reply</button>    
            </div>
        </article>
        ';
    }
);

echo $fetch.'</main>';
?>
