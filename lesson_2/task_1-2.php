<?php
 // Задание 1 (Получить число шагов для заданного алгоритма): около 60 шагов
 // Задание 2 (Вычислить сложность алгоритма): сложность алгоритма – O(N log N)​



function ShellSort($elements) {
  $k = 0;
  $length = count($elements);
  $gap[0] = (int)($length / 2);
  while ($gap[$k] > 1) {
    $k++;
    $gap[$k] = (int)($gap[$k - 1] / 2);
  }
  for ($i = 0; $i <= $k; $i++) {
    $step = $gap[$i];
    for ($j = $step; $j < $length; $j++) {
      $temp = $elements[$j];
      $p = $j - $step;
      while ($p >= 0 && $temp['price'] < $elements[$p]['price']) {
        $elements[$p + $step] = $elements[$p];
        $p = $p - $step;
      }
      $elements[$p + $step] = $temp;
    }
  }
  return $elements;
}

$prices = [
  [
    'price' => 21999,
    'shop_name' => 'Shop 1',
    'shop_link' => 'http://'
  ],
  [
    'price' => 21550,
    'shop_name' => 'Shop 2',
    'shop_link' => 'http://'
  ],
  [
    'price' => 21950,
    'shop_name' => 'Shop 2',
    'shop_link' => 'http://'
  ],
  [
    'price' => 21350,
    'shop_name' => 'Shop 2',
    'shop_link' => 'http://'
  ],
  [
    'price' => 21050,
    'shop_name' => 'Shop 2',
    'shop_link' => 'http://'
  ]
];


$sort = ShellSort($prices);
var_dump($sort);