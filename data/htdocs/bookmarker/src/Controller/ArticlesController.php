<?php

namespace App\Controller;
use App\Controller\AppController;
use Cake\ORM\Query;

class ArticlesController extends AppController
{

    /* initialize */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->loadComponent('Flash');
        $this->Auth->allow(['tags']);
    }

    /* 記事一覧表示 */
    public function index()
    {
        $this->loadComponent('Paginator');
        $articles = $this->Paginator->paginate($this->Articles->find());
        // set()・・・テンプレートに値を受け渡し
        $this->set(compact('articles'));
    }

    /* 単一記事表示 */
    public function view($slug = null)
    {
        $article = $this->Articles->findBySlug($slug)->firstOrFail();
        $this->set(compact('article'));
    }
    
    /* 記事追加 */
    public function add()
    {
        $article = $this->Articles->newEntity();
        if ($this->request->is('post')) {
            $article = $this->Articles->patchEntity($article, $this->request->getData());

            // セッションから user_id をセット
            $article->user_id = $this->Auth->user('id');

            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            // error
            $this->Flash->error(__('Unable to add your article.'));
        }
        $this->set('article', $article);
    }

    /* 記事編集 */
    public function edit($slug)
    {
        $article = $this->Articles
            ->findBySlug($slug)
            ->contain('Tags') // 関連づけられた Tags を読み込む
            ->firstOrFail();

        if ($this->request->is(['post', 'put'])) {
            $this->Articles->patchEntity($article, $this->request->getData(), [
                // user_id の更新を無効化:user_id should be unique in this time
                'accessibleFields' => ['user_id' => false]
            ]);
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been updated.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to update your article.'));
        }
        $this->set('article', $article);
    }

    /* 記事削除 */
    public function delete($slug)
    {
        $this->request->allowMethod(['post', 'delete']);
        $article = $this->Articles->findBySlug($slug)->firstOrFail();
        if ($this->Articles->delete($article)) {
            $this->Flash->success(__('The {0} article has been deleted.', $article->title));
            return $this->redirect(['action' => 'index']);
        }
    }

     /* 記事タグ表示 */
    public function tags()
    {
        /* 'pass' キーは CakePHP によって提供され、リクエストに渡された
         全ての URL パスセグメントを含む。*/
        $tags = $this->request->getParam('pass');

        // ArticlesTable を使用してタグ付きの記事を検索。
        $articles = $this->Articles->find('tagged', [
            'tags' => $tags
        ]);

        // 変数をビューテンプレートのコンテキストに。
        $this->set([
            'articles' => $articles,
            'tags' => $tags
        ]);
    }

     /* 記事ページ対する認証機能 */
    //  
    public function isAuthorized($user)
    {
        // adminは全て編集可能
        if($user['role'] == 'admin'){
            return true;
        }
        
        $action = $this->request->getParam('action');
        // add および tags アクションは、常にログインしているユーザー[user]には許可
        if (in_array($action, ['add', 'tags'])) {
            return true;
        }

        // 他のすべてのアクションにはスラッグ必要
        $slug = $this->request->getParam('pass.0');
        if (!$slug) {
            return false;
        }

        // user_idと一致するかどうか（他人の記事編集防止）
        $article = $this->Articles->findBySlug($slug)->first();
        return $article->user_id === $user['id'];
    }
}