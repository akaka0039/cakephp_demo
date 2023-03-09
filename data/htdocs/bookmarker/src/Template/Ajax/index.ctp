<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <title>jQuery・Ajax・Cake</title>
    <?php
    // Ajax 送信用の JavaScript を読み込み
    echo $this->Html->script('http://code.jquery.com/jquery-1.11.3.min.js');
    //echo $this->Html->script('send_data');
    echo $this->Html->script($ajax_name);
    ?> 
</head>
<body>
    <h1>jQuery・Ajax・Cake</h1>
<?php
    echo $this->Form->create ("null",[ "type" => "post"]);
    echo $this->Form->textarea("textdata",['cols'=> 20, 'rows' => 4,'id' => 'textdata']);
    echo $this->Form->submit('送信',['id' => 'send']);
    echo $this->Form->end (); 
?>
</body>
</html>