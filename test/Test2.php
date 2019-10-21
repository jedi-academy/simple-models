<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require 'SimpleModel.php'; require 'JSONModel.php'; require 'JSON1.php';
$model = new JSON1();
echo var_dump($model->fields);
$record = $model->first();
$key = $record->{$model->keyField};
$model->delete($key);
