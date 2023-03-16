<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <title>jQuery・Ajax・Cake</title>
    <?php
    // Ajax 送信用の JavaScript を読み込み
    echo $this->Html->script('http://code.jquery.com/jquery-1.11.3.min.js');
    echo $this->Html->script('send_data');
    echo $this->Html->script($ajax_name);
    ?> 
</head>
<body>
    <h1>jQuery・Ajax・Cake</h1>
    <div id="overlay">
        <div class="cv-spinner">
            <span class="spinner"></span>
        </div>
    </div>
<?php
    echo $this->Form->create ("null",[ "type" => "post"]);
    echo $this->Form->textarea("textdata",['cols'=> 20, 'rows' => 4,'id' => 'textdata']);
    echo $this->Form->submit('送信',['id' => 'send']);
    echo $this->Form->end (); 
?>

</body>
</html>

<style>
#overlay{ 
  position: fixed;
  top: 0;
  z-index: 100;
  width: 100%;
  height:100%;
  display: none;
  background: rgba(0,0,0,0.6);
}
.cv-spinner {
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;  
}
.spinner {
  width: 40px;
  height: 40px;
  border: 4px #ddd solid;
  border-top: 4px #2e93e6 solid;
  border-radius: 50%;
  animation: sp-anime 0.8s infinite linear;
}
@keyframes sp-anime {
  100% { 
    transform: rotate(360deg); 
  }
}
.is-hide{
  display:none;
} 
</style> 