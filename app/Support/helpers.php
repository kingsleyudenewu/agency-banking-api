<?php


/**
 * Make random number
 *
 * @param $len
 *
 * @return string
 */
function makeRandomInt( $len ): string {
    $alpha = array();
    for ($u = 1; $u <= 9; $u++) {
        array_push($alpha, $u);
    }
    $rand_alpha_key = array_rand($alpha);
    $rand_alpha = $alpha[$rand_alpha_key];
    $rand = array($rand_alpha);
    for ($c = 0; $c < $len - 1; $c++) {
        array_push($rand, mt_rand(0, 9));
        shuffle($rand);
    }

    return implode('', $rand);
}
