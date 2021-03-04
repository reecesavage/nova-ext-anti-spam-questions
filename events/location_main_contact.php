<?php 

$this->event->listen(['location', 'view', 'data', 'main', 'main_contact'], function($event){

         $this->db->order_by('setting_id', 'RANDOM');
         $this->db->limit(1);
         $this->db->where('setting_key','question');
         $query=   $this->db->get('settings');
         $model = ($query->num_rows() > 0) ? $query->row() : false;
         if(!empty($model))
         {
            $jsonDecode=json_decode($model->setting_value,true);


            $event['data']['label']['nova_ext_anti_spam_questions_question']=$jsonDecode['question'];
             $event['data']['inputs']['nova_ext_anti_spam_questions_setting_id']=$model->setting_id;


      $event['data']['inputs']['nova_ext_anti_spam_questions_answer'] = array(
        'name' => 'nova_ext_anti_spam_questions_answer',
        'id' => 'nova_ext_anti_spam_questions_answer'
        
      );


         }


});
$this->event->listen(['location', 'view', 'output', 'main', 'main_contact'], function($event){



  switch($this->uri->segment(4)){
    case 'view':
      break;
    default: 
    
                $event['output'] .= $this->extension['jquery']['generator']
                      ->select('[name="message"]')->closest('p')
                      ->before(
                        $this->extension['nova_ext_anti_spam_questions']
                             ->view('_form', $this->skin, 'main', $event['data'])
                      );
      
 }
});
