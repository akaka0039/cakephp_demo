<?php
namespace App\Controller;

use App\Controller\AppController;
use \SplFileObject;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Articles'],
        ]);

        $this->set('user', $user);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function login()
    {
    if ($this->request->is('post')) {
        $user = $this->Auth->identify();
        if ($user) {
            $this->Auth->setUser($user);
            return $this->redirect($this->Auth->redirectUrl());
        }
        $this->Flash->error('ユーザー名またはパスワードが不正です。');
    }
    }

    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow(['logout', 'add']);
    
    }

    public function logout()
    {   
    $this->Flash->success('ログアウトしました。');
    return $this->redirect($this->Auth->logout());
    }

    // 20230219＿追記
    // CSVファイルダウンロード
    public function download()
    {
        $_body = $this->Users->find()->all();

        $_serialize = '_body';
        $_header = ['id', 'email', 'created', 'modified'];
        $_footer = ['これはフッターです'];
        $_extension = 'mbstring';
        $_dataEncoding = 'UTF-8';
        $_csvEncoding = 'CP932';
        $_newline = "\r\n";
        $_eol = "\r\n";
        

        $this->response = $this->response
            ->withType('csv')
            ->withHeader('Content-Disposition', 'attachment')
            ->withDownload('users.csv');

        $this->viewBuilder()->setClassName('CsvView.Csv');
        $this->set(compact('_body', '_serialize', '_header', '_footer', '_extension', '_dataEncoding', '_csvEncoding', '_newline', '_eol'));
    }

    // 20230220＿追記
    // CSVファイルアップロード
    public function upload()
    {
        if ($this->request->is('post')) {
            // ファイルの拡張子がcsv以外の場合はファイル形式エラー
            if (mb_strtolower(pathinfo($_FILES['upload_file']['name'], PATHINFO_EXTENSION)) !='csv') {
                $this->Flash->error(__('The file format is invalid.'));
                return;
            }
            
            // ファイル読込み準備
            $uploadFile = $_FILES['upload_file']['tmp_name'];
            file_put_contents($uploadFile, mb_convert_encoding(file_get_contents($uploadFile), 'UTF-8', 'SJIS'));
            $file = new SplFileObject($uploadFile);
            $file->setFlags(SplFileObject::READ_CSV);
            
            $new_users = array();   // データを入れておく配列
            $errors = array();      // エラーを入れておく配列
            
            foreach ($file as $rowIndex => $line) {
                if ($rowIndex < 1) {
                    // 1行目はヘッダー行なので読み飛ばし。
                    continue;
                }
                
               // 項目数が合わない場合は項目数エラーを記録し次の行を処理
               // 最終行が空の場合はスルーします。
               if (count($line) != 5) {
                    if ($file->valid() || ($file->eof() && !empty($line[0]))) {
                        $errors = $this->setError($errors, $rowIndex, __('The number of items is invalid.'));
                    }
                } else {
                    // 取り込んだCSVデータ行からユーザーデータ配列を作成
                    $arrUser = $this->createUserArray($line);
                    // ユーザーデータの配列をユーザーエンティティにパッチ
                    // このタイミングでValidation
                    $user = $this->Users->newEntity($arrUser);
                    
                    // Validationでエラーがあった場合、エンティティにエラーがセットされるので
                    // 最後にエラー一覧を表示するため、エラーがある場合は別で保存
                    $entityErrors = $user->getErrors();
                    foreach($entityErrors as $key=>$value) {
                        if (is_array($value)) {
                            foreach($value as $rule=>$message) {
                                $errors = $this->setError($errors, $rowIndex, $message);
                            }
                        }
                    }
                    // Validationエラーが無かった場合は、一括保存のために配列に入れる
                    if (empty($errors)) {
                        array_push($new_users, $user);
                    }
                }
            }

            // エラーが無かった場合データを保存し一覧画面に遷移
            // エラーがあった場合はファイル選択画面に遷移しエラー内容を表示
            
            if (!$errors) {
                // ユーザーデータを登録
                if ($this->Users->saveMany($new_users)) {
                    $this->Flash->success(__('The user has been saved.'));
                    return $this->redirect(['action' => 'index']);
                }
                // データセーブのタイミングでユーザーテーブルのbuildRulesメソッドでのチェック
                // buildRulesメソッドでエラーがあった場合、もしくはデータベースの保存時にエラーが発生した場合は
                // このエラーメッセージが表示
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            } else {
                // ファイルアップロード画面にエラー内容
                $this->Flash->error(__('Contains incorrect data. Please check the message, correct the data and upload again.'));
                $this->set(compact('errors'));
            }
        }
    }

    /**
     * ユーザーデータ取り込みcsvデータの1行から、1件のユーザーデータ配列を作成
     * @param [array] $line csvの行データ配列
     * @return ユーザーデータ配列
     */
    private function createUserArray($line)
    {
        $arr = array();
        $arr['id'] = $line[0];
        $arr['email'] = $line[1];
        $arr['password'] = $line[2];
        $arr['created'] = $line[3];
        $arr['modify'] = $line[4];

        return $arr;
    }

    private function setError($errors, $rowIndex, $description) {
        $error = array();
        empty($rowIndex) ? $error['LINE_NO'] = '' :  $error['LINE_NO'] = $rowIndex + 1;
        $error['DESCRIPTION'] =  $description;
        array_push($errors, $error);
        
        return $errors;
    }

    public function isAuthorized($user)
    {
        // ログイン者全員が使用することができる
        $action = $this->request->getParam('action');
        if (in_array($action, ['download', 'add', 'upload'])) {
            return true;
        }

        // ユーザー所有者のみが編集・削除することができる
        if (in_array($this->request->getParam('action'), ['edit', 'delete'])) {
            $Id = (int)$this->request->getParam('pass.0');
            if ($Id == $user['id']) {
                return true;
            }
        }

        return parent::isAuthorized($user);
    }  
}
