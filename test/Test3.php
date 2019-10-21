<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require 'SimpleModel.php'; require 'XMLModel.php'; require 'XML1.php';
$model = new XML1();
echo var_dump($model->data);
$record = $model->first();
$key = $record->{$model->keyField};
$model->delete($key);
