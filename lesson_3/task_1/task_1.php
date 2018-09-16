<?php

$db = new PDO('mysql:host=localhost;dbname=php_data_structures;', 'root', '');
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
//$sql = "SELECT * FROM category as c INNER JOIN category_links as cl ON cl.child_id = c.id WHERE cl.parent_id = :val";
$sql = "SELECT c.id, c.title,  max(cl.parent_id) AS pid, cl.level
          FROM category AS c
            RIGHT JOIN category_links AS cl ON cl.child_id = c.id
            WHERE c.id <> cl.parent_id OR cl.level = 0
            GROUP BY cl.child_id, cl.level";
$res = $db->prepare($sql);
$res->execute();
$arrays = $res->fetchAll();



echo buildTree(createStructure($arrays));




function createStructure($arrIn) {
  $arrOut = [];
  foreach ($arrIn as $arrChild) {
    if ($arrChild['level'] == 0) {
      $arrOut[] = $arrChild;
    } else {
      if (count($arrOut) > 0) {
        $arrPath = getParentById($arrOut, $arrChild['pid']);
        if (count($arrPath) == $arrChild['level']) {
          $link = &$arrOut;
          foreach ($arrPath as $num) {
            if (isset($link[$num])) {
              $link = &$link[$num];
            } else {
              $link = &$link['child'][$num];
            }
          }
          if (!isset($link['child'])) {
            $link['child'] = [];
          }
          $link['child'][] = $arrChild;
        }
      }
    }
  }
  return $arrOut;
}

function getParentById($arr, $id, $parentInd = null) {
  $arrOut = [];
  if (!is_null($parentInd)) {
    $arrOut[] = $parentInd;
  }
  foreach ($arr as $key => $arrParent) {
    if ($arrParent['id'] == $id) {
      $arrOut[] = $key;
      break;
    } elseif (isset($arrParent['child']) && count($arrParent['child']) > 0) {
      $arrOut = array_merge($arrOut, getParentById($arrParent['child'], $id, $key));
    }
  }
  return $arrOut;
}


function buildTree($categories) {
  $html = "<ul>";
  foreach ($categories as $item) {
    $html .= "<li>" . $item ["title"];
    if (isset($item["child"])) {
      $html .= "<ul>";
      $html .= buildTree($item["child"]);
      $html .= "</ul>";
    }
    $html .= "</li>";
  }
  $html .= "</ul>";
  return $html;
}
