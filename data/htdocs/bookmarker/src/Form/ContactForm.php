<?php

namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;
use Cake\Network\Email\Email;

class ContactForm extends Form
{

    // お問い合わせフォームのスキーマを定義する
    protected function _buildSchema(Schema $schema)
    {
        return $schema->addField('name', 'string')
            ->addField('inquiry', ['type' => 'text']);
    }

    // バリデーション内容を定義する
    protected function _buildValidator(Validator $validator)
    {
        return $validator->add('name', 'length', [
                'rule' => ['minLength', 5],
                'message' => '名前は10文字以上入力してください。'
        ]);
    }

    // バリデーション後に実行したい処理を記述する
    protected function _execute(array $data)
    {
        return true;
    }
}
