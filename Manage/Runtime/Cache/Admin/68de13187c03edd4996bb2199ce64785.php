<?php if (!defined('THINK_PATH')) exit();?>
<style type="text/css">
    table.gridtable {
        font-family: verdana,arial,sans-serif;
        font-size:11px;
        color:#333333;
        border-width: 1px;
        border-color: #666666;
        border-collapse: collapse;
        width: 80%;
    }
    table.gridtable th {
        border-width: 1px;
        padding: 8px;
        border-style: solid;
        border-color: #666666;
        background-color: #dedede;
    }
    table.gridtable td {
        border-width: 1px;
        padding: 8px;
        border-style: solid;
        border-color: #666666;
        background-color: #ffffff;
    }
</style>
<div style="padding-top:50px;">
    <div  style='width:80%;margin: 0 auto;clear: both;font-size:18px;color:#080'>
        <?php
 echo '从'.date("Y年m月d日",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y"))).'到'.date("Y年m月d日",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y"))).'一周'; ?>
        <br><br>
    </div>
    <?php
 print_r($data); ?>
</div>