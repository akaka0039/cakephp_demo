<h1>Articles</h1>
<p><?= $this->Html->link("Add Article", ['action' => 'add']) ?></p>
<br>
<p><?= $this->Html->link(__('check users'), ['controller' => 'Users', 'action' => 'index']) ?></p>
<table>
    <tr>
        <th>Title</th>
        <th>Created</th>
        <th>Action</th>
    </tr>

<!-- $articles クエリーオブジェクトを繰り返して(
    コントローラーからの、記事を出力 -->
<?php foreach ($articles as $article): ?>
    <tr>
        <td>
            <!--  link() メソッドは、 
            与えられたリンクテキスト(第１パラメーター) と 
            URL (第２パラメーター) を元に HTML リンクを生成 -->
            <?= $this->Html->link($article->title, ['action' => 'view', $article->slug]) ?>
        </td>
        <td>
            <?= $article->created->format(DATE_RFC850) ?>
        </td>
        <td>
            <?= $this->Html->link('Edit', ['action' => 'edit', $article->slug]) ?>
            <?= $this->Form->postLink(
                'Delete',
                ['action' => 'delete', $article->slug],
                ['confirm' => 'Are you sure?'])
            ?>
        </td>
    </tr>
<?php endforeach; ?>

</table>