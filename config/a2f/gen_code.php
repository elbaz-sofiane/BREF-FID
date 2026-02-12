<?php
function gen_code() {
    // Génère un code aléatoire à 6 chiffres
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}


