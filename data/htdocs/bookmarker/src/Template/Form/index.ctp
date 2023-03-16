<h1>問い合わせページ</h1>
<p>
  <?php
 
    //フォームの作成
    echo $this->Form->create('contact',['url' => ['action' =>'sendForm']]);
    //コントロールを配置
    echo $this->Form->control('name');
    echo $this->Form->control('inquiry');
    
    echo $this->Form->button('送信');

    //フォームの終了
    echo $this->Form->end();
 
  ?>
</p>
</div>