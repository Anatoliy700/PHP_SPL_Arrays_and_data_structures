<?php

function isPalindrome($word) {
  if (mb_strlen($word) == 1) {
    return true;
  } else {
    $firstChar = mb_substr($word, 0, 1);
    $lastChar = mb_substr($word, -1, 1);
    $subString = mb_substr($word, 1, mb_strlen($word) - 2);
    return $firstChar == $lastChar && isPalindrome($subString);
  }
}

var_dump(isPalindrome('тест'));
var_dump(isPalindrome('тесет'));