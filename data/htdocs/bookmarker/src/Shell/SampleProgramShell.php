<?php
namespace App\Shell;
 
use Cake\Console\Shell;
use Cake\Mailer\Email;
use Cake\ORM\TableRegistry;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Controller\AppController;
 
class SampleProgramShell extends Shell
{
    public function main()
    {
        $this->out('これはサンプルシェルです');
    }
 
    public function sampleParameter ($parameter)
    {
        $this->out('サンプル関数の引数は「' . $parameter . '」です。');
    }

    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Users');
    }

    public function show()
    {
        if (empty($this->args[0])) {
            // CakePHP 3.2 より前なら error() を利用
            return $this->abort('Please enter a id.');
        }
        $user = $this->Users->findById($this->args[0])->first();
        $this->out(print_r($user, true));
    }

    // 記事情報をエクセルでダウンロードし、メール送信する
    public function mail()
    {

        $this->Articles = TableRegistry::get('articles');

        $_body = $this->Articles->find()->all();
    
        $sheet = new Spreadsheet();
        
        // レコードの取得数
        $number = $_body->count();
    
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
    
        // ダウンロード
        $fileName = '記事一覧リスト' . date('Y_m_d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;');
        header("Content-Disposition: attachment; filename=\"{$fileName}\""); header('Cache-Control: max-age=0');
    
        $writer = IOFactory::createWriter($sheet, 'Xlsx');
        // フルパスで保存先指定する必要あり
        // $writer->save('php://output');
        
        $email = new Email('default');
        $email->setFrom(['me@example.com' => 'My Site'])
        // 送り先
        ->setTo('me@example.com')
        ->setSubject('Test')
        // フルぱすに変更する必要あり
        // ->addAttachments('src/image/articles_info.xlsx')
        ->Send('My message');
        
        $this->out('成功です');
    }
}