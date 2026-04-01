<?php

return [
    // On change 'bcrypt' en 'argon2id'
    'driver' => 'argon2id',

    'argon' => [
        'memory' => 65536,   // 64 MB — coût mémoire élevé = résistant aux GPUs
        'threads' => 1,
        'time' => 4,         // 4 itérations — ralentit les attaques brute force
    ],
];