<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require 'SimpleModel.php'; require 'CSVModel.php'; require 'CSV1.php';
$model = new CSV1();
$record = $model->first();
$key = $record->{$model->keyField};
$model->delete($key);
