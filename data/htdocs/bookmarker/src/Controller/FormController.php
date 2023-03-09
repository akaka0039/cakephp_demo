<?php
 
namespace App\Controller;
use Cake\Event\Event;
use App\Controller\FilesController;
use Cake\Utility\Security;
Use App\Form\ContactForm;
 
class FormController extends AppController {


  public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Security');
    }

  public function beforeFilter(Event $event)
  {
      parent::beforeFilter($event);
      $this->Auth->allow( ['index', 'sendForm'] );
      // Csrfがチェックされるように設定
      $this->loadComponent('Csrf');
  }

  // 問い合わせフォームの構築
  public function index() {

  }

  public function sendForm() {
    $contact = new ContactForm();
    $token = $this->request->getParam('_csrfToken');
      
    if ($this->request->is('post')) {
      if ($contact->execute($this->request->data)) {
          $this->Flash->success('メール送信しました');
      } else {
          $this->Flash->error('バリデーションに引っかかりました。');
      }
  }
  $contact = $this->request->getData();
  $this->set(compact('contact', 'token'));
  }
}
?>