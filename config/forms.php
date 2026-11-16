<?php

// Кому йдуть листи про нові заявки з форм сайту (через кому). Порожньо — лист не надсилається,
// заявка все одно зберігається й видна в адмінці. Відгуки на вакансії — окремо, в налаштуваннях HR.
$list = fn (?string $v) => array_values(array_filter(array_map('trim', explode(',', (string) $v))));

return [
    'notify' => [
        'contact' => $list(env('FORM_NOTIFY_CONTACT')),
        'land' => $list(env('FORM_NOTIFY_LAND', 'landplot@kandevelopment.com')),
        'apartment' => $list(env('FORM_NOTIFY_APARTMENT')),
        'deck' => $list(env('FORM_NOTIFY_DECK', env('FORM_NOTIFY_APARTMENT'))),
    ],
];
