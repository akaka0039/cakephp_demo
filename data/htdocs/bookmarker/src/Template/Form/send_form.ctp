<h1>問い合わせ内容確認ページ</h1>


<?php foreach ($contact as $i): ?>
    <p>
        <?= h($i); ?>
    </p>
   <p>-----------</p> 

<?php endforeach; ?>
<br>
<p>
    トークンの確認
</p>
<p>
    <?= $token ?>
</p>
<br>
<?= $this->Html->link(__('問い合わせフォームへ戻る'), ['action' => 'index']) ?>