<?php
namespace App\Controller;
require 'vendor/autoload.php';

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use PhpOffice\PhpSpreadsheet\IOFactory;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Cake\Mailer\Email;


/**
 * File Controller
 *
 * 
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class FilesController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->loadComponent('Flash');
        $this->Articles = TableRegistry::get('articles');
    }

    /* データベースにある全てのarticleデータをエクセルに書き込み、ダウンロードする */
    public function exportAsExcel()
    {
        $_body = $this->Articles->find()->all();
    
        $sheet = new Spreadsheet();
        
        // テーブルのカラム名をセット
        $columns = $this->Articles->schema()->columns();
        $i = 2;
        foreach ( $columns as $column ) {
            $sheet->getActiveSheet()->setCellValueByColumnAndRow($i, 2, $column);
            $i++;
        }
    
        // テーブルの値をセット
        $i = 2;
        foreach ($_body as $body) {
            $sheet->getActiveSheet()->setCellValueByColumnAndRow($i ,3 , $body->id);
            $sheet->getActiveSheet()->setCellValueByColumnAndRow($i ,4 , $body->user_id);
            $sheet->getActiveSheet()->setCellValueByColumnAndRow($i ,5 , $body->title);
            $sheet->getActiveSheet()->setCellValueByColumnAndRow($i ,6 , $body->slug);
            $sheet->getActiveSheet()->setCellValueByColumnAndRow($i ,7 , $body->body);
            $sheet->getActiveSheet()->setCellValueByColumnAndRow($i ,8 , $body->published);
            $sheet->getActiveSheet()->setCellValueByColumnAndRow($i ,9 , $body->created);
            $sheet->getActiveSheet()->setCellValueByColumnAndRow($i ,10 , $body->modified);
            $i++;
        }
    
        // ブラウザからダウンロード
        $fileName = '記事一覧リスト' . date('Y_m_d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;');
        header("Content-Disposition: attachment; filename=\"{$fileName}\""); header('Cache-Control: max-age=0');
    
        $writer = IOFactory::createWriter($sheet, 'Xlsx');
        $writer->save('php://output');
        // フルパスで保存先指定する必要あり
        $writer->save('');
        
        $this->Flash->success(__('success to download'));
        exit;
    }

    // エクセルを相手先に送信する
    public function emailSend()
    {
        $email = new Email('gmail');
        $email->setFrom(['me@example.com' => 'My Site'])
            // 送り先
            ->setTo('')
            ->setSubject('Test')
            // フルパスに変更する必要あり
            ->addAttachments('')
            ->Send('My message');
            
            
        $this->Flash->success(__('success to send a email'));
        return $this->redirect(['controller' => 'Users','action' => 'index']);
    }


    
    public function isAuthorized($user)
    {
        // 全てのユーザが機能利用可能
        return true;
    }



}