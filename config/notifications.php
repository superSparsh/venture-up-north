<?php

return [
    'admin_submission_recipients' => array_values(array_filter(array_map(
        'trim',
        explode(',', env('ADMIN_NOTIFY_LIST', ''))
    ))),
];
