<?php

function mergeSort($arr) {
  $countArr = count($arr);
  if ($countArr <= 1) {
    return $arr;
  }
  $startArr = array_slice($arr, 0, (int)$countArr / 2);
  $endArr = array_slice($arr, (int)$countArr / 2);
  
  $startArr = mergeSort($startArr);
  $endArr = mergeSort($endArr);
  
  $outArr = [];
  
  while (count($startArr) > 0 && count($endArr) > 0) {
    if ($startArr[0] < $endArr[0]) {
      array_push($outArr, array_shift($startArr));
    } else {
      array_push($outArr, array_shift($endArr));
    }
  }
  
  return array_merge($outArr, $startArr, $endArr);
}

$arr = [];
$countTotal = 100;
while (count($arr) < $countTotal) {
  $rnd = rand(1, $countTotal);
  if (!in_array($rnd, $arr)) {
    array_push($arr, $rnd);
  }
}

$sort = mergeSort($arr);
var_dump($sort);