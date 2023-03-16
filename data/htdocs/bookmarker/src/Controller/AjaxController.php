<?php
namespace App\Controller;
 
use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
 
class AjaxController extends AppController
{
 
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow( ['index', 'add'] );
    }

  public function add(){
    $data = $this->request->data('request');
    
    sleep(2);
    $connection = ConnectionManager::get('default');
    $connection->insert('ajax', [ 'text' => $data ]);
    
  }
  public function index()
  {
    $this->set('ajax_name','send_data.js');
  }
}