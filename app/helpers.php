<?php

function valueIfExist ($v = null){
    return (!is_null($v) && !empty($v) ? 'value="'.$v.'"' : "");
}
function dde($data){
    echo json_encode($data), true;
    die();
}