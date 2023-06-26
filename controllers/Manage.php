<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once MODPATH . 'core/libraries/Nova_controller_admin.php';

class __extensions__nova_ext_anti_spam_questions__Manage extends Nova_controller_admin
{
    public function __construct()
    {
        parent::__construct();
     

        $this->ci = & get_instance();

        $this->ci->load->model('settings_model', 'settings');
        $this->_regions['nav_sub'] = Menu::build('adminsub', 'manageext');
        //$this->_regions['nav_sub'] = Menu::build('sub', 'sim');
        

        
    }

    public function writeControllerCode()
  {   
          
        $extControllerPath = APPPATH.'controllers/Main.php';
        if ( !file_exists( $extControllerPath ) ) { 
        return [];
        }
        $controllerFile = file_get_contents( $extControllerPath );
        $pattern = '/public\sfunction\scontact/';
        if (!preg_match($pattern, $controllerFile)) {
       $writeFilePath = dirname(__FILE__).'/../main.txt';
        if ( !file_exists( $writeFilePath ) ) { 
           return [];
        }
        $file = file_get_contents( $writeFilePath );

       $contents = file($extControllerPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      $size = count($contents);
      $contents[$size-1] = "\n".$file;
      $temp = implode("\n", $contents);

     
      file_put_contents($extControllerPath, $temp);
         
         return true;
        }
      return false;
              


  }


   



    public function index()
    {
          Auth::check_access('site/settings');
        $data['title'] = 'Manage Questions';
      $data['write']=true;

         $extControllerPath = APPPATH.'controllers/Main.php';
         
        if ( !file_exists( $extControllerPath ) ) { 
        return [];
        }
        $file = file_get_contents( $extControllerPath );
        $pattern = '/public\sfunction\scontact/';


           if (!preg_match($pattern, $file)) {
           $data['write']=false;

        if(isset($_POST['submit']) && $_POST['submit']=='write')
        {
             
            if($this->writeControllerCode())
            {
              $data['write']=true;
                $message = sprintf(
               lang('flash_success'),
          // TODO: i18n...
              'Controller',
          lang('actions_added'),
          ''
        );
            }else {
                    $message = sprintf(
               lang('flash_failure'),
          // TODO: i18n...
              'Controller',
          lang('actions_added'),
          ''
        );
            }
         

        $flash['status'] = 'success';
        $flash['message'] = text_output($message);

        $this->_regions['flash_message'] = Location::view('flash', $this->skin, 'admin', $flash);

        }
        }
          

        if(isset($_POST['submit']) && $_POST['submit'] == 'Submit')
        {

             $id = $this->input->post('id', true);
        $id = (is_numeric($id)) ? $id : false;
        $delete = $this->ci->settings->delete_setting($id);
        if ($delete > 0)
        {
                     
                        $message = sprintf(
                                    lang('flash_success'),
                                    'Question',
                                    lang('actions_deleted'),
                                    ''
                                );


                        $flash['status'] = 'success';
                        $flash['message'] = text_output($message);

                        $this->_regions['flash_message'] = Location::view('flash', $this->skin, 'admin', $flash);
                    }
        }

        $data['images'] =
        ['add' => array(
                'src' => Location::img('icon-add.png', $this->skin, 'admin'),
                'alt' => ucfirst(lang('actions_add')),
                'title' => ucfirst(lang('actions_add')),
                'class' => 'image inline_img_left'),

        'delete' => array(
                'src' => Location::img('icon-delete.png', $this->skin, 'admin'),
                'alt' => lang('actions_delete'),
                'title' => ucfirst(lang('actions_delete')),
                'class' => 'image'),
        'edit' => array(
                'src' => Location::img('icon-edit.png', $this->skin, 'admin'),
                'alt' => lang('actions_edit'),
                'title' => ucfirst(lang('actions_edit')),
                'class' => 'image'),
       ]; 

        $this->db->from('settings');
        $this->db->where('setting_key', 'question');
        $query = $this->db->get();
        $data['models']= $query->result();
        
        $this->_regions['title'] .= 'Manage Questions';


           $this->_regions['javascript'] .= $this->extension['nova_ext_anti_spam_questions']->inline_js('custom', 'admin', $data);

        $this->_regions['content'] = $this->extension['nova_ext_anti_spam_questions']
            ->view('index', $this->skin, 'admin', $data);

        Template::assign($this->_regions);
        Template::render();
    }


   

     public function create()
    {
          Auth::check_access('site/settings');

            $data['title'] = 'Create Questions';


      if (isset($_POST['submit']) && $_POST['submit'] == 'Submit')
        {
           
            
                $json['question']=$_POST['question'];
                $json['answer']=$_POST['answer'];

            $this->ci->settings->add_new_setting( [
                'setting_key' => 'question',
                'setting_label' => 'Questions and Answer',
                'setting_value' => json_encode( $json)
            ] );



             $message = sprintf(lang('flash_success') ,
            // TODO: i18n...
            'Question Added successfully', '', '');

            $flash['status'] = 'success';
            $flash['message'] = text_output($message);

            $this->_regions['flash_message'] = Location::view('flash', $this->skin, 'admin', $flash);

        }

           $this->_regions['title'] .= 'Create Questions';

           $this->_regions['javascript'] .= $this->extension['nova_ext_anti_spam_questions']->inline_js('custom', 'admin', $data);

            $this->_regions['javascript'] .= $this->extension['nova_ext_anti_spam_questions']->inline_css('custom', 'admin', $data);

           $this->_regions['content'] = $this->extension['nova_ext_anti_spam_questions']
            ->view('create', $this->skin, 'admin', $data);

        Template::assign($this->_regions);
        Template::render();

    }


    public function edit()
    {
         Auth::check_access('site/settings');

            $data['title'] = 'Update Questions';

        $id = $this->uri->segment(5);


         
      if (isset($_POST['submit']) && $_POST['submit'] == 'Submit')
        {
           
            
                $json['question']=$_POST['question'];
                $json['answer']=$_POST['answer'];

            $this->ci->settings->update_setting( $id,[
                'setting_value' => json_encode( $json)
            ],'setting_id' );



             $message = sprintf(lang('flash_success') ,
            // TODO: i18n...
            'Question Updated successfully', '', '');

            $flash['status'] = 'success';
            $flash['message'] = text_output($message);

            $this->_regions['flash_message'] = Location::view('flash', $this->skin, 'admin', $flash);

        }

         $query = $this->db->get_where('settings', array('setting_id' => $id));
        $data['model'] = ($query->num_rows() > 0) ? $query->row() : false;

           $this->_regions['title'] .= 'Update Questions';

           $this->_regions['javascript'] .= $this->extension['nova_ext_anti_spam_questions']->inline_js('custom', 'admin', $data);

            $this->_regions['javascript'] .= $this->extension['nova_ext_anti_spam_questions']->inline_css('custom', 'admin', $data);

           $this->_regions['content'] = $this->extension['nova_ext_anti_spam_questions']
            ->view('update', $this->skin, 'admin', $data);

        Template::assign($this->_regions);
        Template::render();
    }

}
