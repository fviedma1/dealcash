<?php
function ordenarProductos($products, $order, $direction) {
    $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

    switch ($order) {
        case 'price':
            usort($products, function($a, $b) use ($direction) {
                if (!isset($a['preu']) || !isset($b['preu'])) return 0; 
                return ($direction === 'asc') ? $a['preu'] <=> $b['preu'] : $b['preu'] <=> $a['preu'];
            });
            break;

        case 'likes':
            usort($products, function($a, $b) use ($direction) {
                if (!isset($a['likes']) || !isset($b['likes'])) return 0;
                return ($direction === 'asc') ? $a['likes'] <=> $b['likes'] : $b['likes'] <=> $a['likes'];
            });
            break;

        default:
            return $products;
    }

    return $products;
}
?>
