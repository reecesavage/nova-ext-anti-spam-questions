# Anti Spam Questions - A [Nova](https://anodyne-productions.com/nova) Extension
## Created for you by [Sim Central](https://simcentral.org)

<p align="center">
  <a href="https://github.com/reecesavage/nova-ext-honeypot/releases/tag/v1.0.0"><img src="https://img.shields.io/badge/Version-v1.0.0-brightgreen.svg"></a>
  <a href="http://www.anodyne-productions.com/nova"><img src="https://img.shields.io/badge/Nova-v2.6.1-orange.svg"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-v5.3.0-blue.svg"></a>
  <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/license-MIT-red.svg"></a>
</p>

This extension allows the Game Manager to add questions to the Contact and Join forms for spambot prevention.

This extension requires:

- Nova 2.6+
- Nova Extension [`jquery`](https://github.com/jonmatterson/nova-ext-jquery)

## Installation

- Install Required Extensions.
- Copy the entire directory into `applications/extensions/nova_ext_anti_spam_questions`.
- Add the following to `application/config/extensions.php`: - Be sure the `jquery` line appears before `nova_ext_anti_spam_questions`
```
$config['extensions']['enabled'][] = 'nova_ext_anti_spam_questions';
```
### Setup Using Admin Panel - Preferred

- Navigate to your Admin Control Panel
- Choose Anti Spam Questions under Manage Extensions
- Click Update Controller Information to add the `contact` and `join` functions to your `application/controllers/main.php` file.

Installation is now complete!

## Usage

- Navigate to your Admin Control Panel
- Choose Anti Spam Questions under Manage Extensions
- Add, Remove, or Edit Quesions.
- Answers are NOT case Sensitive, but remember to add all acceptable answers:
	- Starship, USS Starship U.S.S. Starship
	- Seventeen, 17

### Manual Setup - If not using the method above.

- Add the following function in your `applications/controllers/main.php` file to overwrite `contact` and `join` functions. This will allow the email subject to include Post numbers before the Post title. 

```
public function contact()
	{
		// load the validation library
		$this->load->library('form_validation');

		// make sure the error messages are using the proper syntax
		$this->form_validation->set_error_delimiters('<span class="red bold error-icon">', '</span><br />');

		// set the validation rules
		$this->form_validation->set_rules('name', 'lang:labels_name', 'required');
		$this->form_validation->set_rules('email', 'lang:labels_email_address', 'required|valid_email');
		$this->form_validation->set_rules('subject', 'lang:labels_subject', 'required');
		$this->form_validation->set_rules('message', 'lang:labels_message', 'required');

		if (isset($_POST['submit']))
		{  
            
			$array = array(
				'name'		=> $this->input->post('name'),
				'email'		=> $this->input->post('email'),
				'subject'	=> $this->input->post('subject'),
				'message'	=> $this->input->post('message')
			);
             
              if ($this->form_validation->run())
			{ 
				  $settingId= isset($_POST['nova_ext_anti_spam_questions_setting_id'])?$_POST['nova_ext_anti_spam_questions_setting_id']:0;

           if(!empty($settingId))
           {
           	 $query = $this->db->get_where('settings', array('setting_id' => $settingId));
            $model = ($query->num_rows() > 0) ? $query->row() : false;
             if(!empty($model))
             {
             	$jsonDecode=json_decode($model->setting_value,true);


              $answer= strtoupper($_POST['nova_ext_anti_spam_questions_answer']);
               

               $answerArray=[];
              foreach ($jsonDecode['answer'] as $key => $value) {
                    $answerArray[]= strtoupper($value);
              }

             
            if(in_array($answer,$answerArray))
             	 {
                    
				$email = ($this->options['system_email'] == 'on') ? $this->_email('contact', $array) : false;

				if ( ! $email)
				{
					$message = sprintf(
						lang('flash_failure'),
						ucfirst(lang('labels_contact')),
						lang('actions_sent'),
						''
					);

					$flash['status'] = 'error';
					$flash['message'] = text_output($message);
				}
				else
				{
					$message = sprintf(
						lang('flash_success'),
						ucfirst(lang('labels_contact')),
						lang('actions_sent'),
						''
					);

					$flash['status'] = 'success';
					$flash['message'] = text_output($message);
				}

				$this->_regions['flash_message'] = Location::view('flash', $this->skin, 'main', $flash);
			
             	 }else {
                   

					$flash['status'] = 'error';
					$flash['message'] = text_output('Security Answer did not match or was blank. Please try again.');
					$this->_regions['flash_message'] = Location::view('flash', $this->skin, 'main', $flash);
             	 }
             }
         }


          }
			
		}

		// set the title, header and content variables
		$data['header'] = ucwords(lang('actions_contact').' '.lang('labels_us'));
		$data['msg'] = $this->msgs->get_message('contact');

		$data['button'] = array(
			'submit' => array(
				'type' => 'submit',
				'class' => 'button-main',
				'name' => 'submit',
				'value' => 'submit',
				'content' => ucwords(lang('actions_submit'))),
		);

		if ($this->options['system_email'] == 'off')
		{
			$data['button']['submit']['disabled'] = 'disabled';
		}

		$data['inputs'] = array(
			'name' => array(
				'name' => 'name',
				'id' => 'name',
				'value' => set_value('name')),
			'email' => array(
				'name' => 'email',
				'id' => 'email',
				'value' => set_value('email')),
			'subject' => array(
				'name' => 'subject',
				'id' => 'subject',
				'value' => set_value('subject')),
			'message' => array(
				'name' => 'message',
				'id' => 'message',
				'rows' => 12,
				'value' => set_value('message'))
			
		);

		$data['label'] = array(
			'send' => ucwords(lang('actions_send') .' '. lang('labels_to')),
			'name' => ucwords(lang('labels_name')),
			'email' => ucwords(lang('labels_email_address')),
			'subject' => ucwords(lang('labels_subject')),
			'message' => ucwords(lang('labels_message')),
			'nosubmit' => lang('flash_system_email_off_disabled'),
		);

		$this->_regions['content'] = Location::view('main_contact', $this->skin, 'main', $data);
		$this->_regions['title'].= $data['header'];

		Template::assign($this->_regions);

		Template::render();
	}


		public function join()
	{
		$this->load->model('positions_model', 'pos');
		$this->load->model('depts_model', 'dept');
		$this->load->model('ranks_model', 'ranks');
		$this->load->helper('utility');

		$agree = $this->input->post('agree', true);
		$submit = $this->input->post('submit', true);
		$selected_pos = $this->input->post('position', true);

		$data['selected_position'] = (is_numeric($selected_pos) and $selected_pos > 0) ? $selected_pos : 0;
		$desc = $this->pos->get_position($data['selected_position'], 'pos_desc');
		$data['pos_desc'] = ($desc !== false) ? $desc : false;

		if ($submit !== false)
		{
			// user POST variables
			$email = $this->input->post('email', true);
			$real_name = $this->input->post('name', true);
			$im = $this->input->post('instant_message', true);
			$dob = $this->input->post('date_of_birth', true);
			$password = $this->input->post('password', true);

			// character POST variables
			$first_name = $this->input->post('first_name',true);
			$middle_name = $this->input->post('middle_name', true);
			$last_name = $this->input->post('last_name', true);
			$suffix = $this->input->post('suffix',true);
			$position = $this->input->post('position_1',true);

			if ($position == 0 or $first_name == '' or empty($password) or empty($email))
			{
				$message = sprintf(
					lang('flash_empty_fields'),
					lang('flash_fields_join'),
					lang('actions_submit'),
					lang('actions_join') .' '. lang('actions_request')
				);

				$flash['status'] = 'error';
				$flash['message'] = text_output($message);
			}
			else
			{
				// check the ban list
				$ban['ip'] = $this->sys->get_item('bans', 'ban_ip', $this->input->ip_address());
				$ban['email'] = $this->sys->get_item('bans', 'ban_email', $email);

				if ($ban['ip'] !== false or $ban['email'] !== false)
				{
					$message = sprintf(
						lang('text_ban_join'),
						lang('global_sim'),
						lang('global_game_master')
					);

					$flash['status'] = 'error';
					$flash['message'] = text_output($message);
				}
				else
				{

                    $settingId= isset($_POST['nova_ext_anti_spam_questions_setting_id'])?$_POST['nova_ext_anti_spam_questions_setting_id']:0;

           if(!empty($settingId))
           {
           	 $query = $this->db->get_where('settings', array('setting_id' => $settingId));
            $model = ($query->num_rows() > 0) ? $query->row() : false;
             if(!empty($model))
             {
             	$jsonDecode=json_decode($model->setting_value,true);


              $answer= strtoupper($_POST['nova_ext_anti_spam_questions_answer']);

                $answerArray=[];
              foreach ($jsonDecode['answer'] as $key => $value) {
                    $answerArray[]= strtoupper($value);
              }

         if(in_array($answer,$answerArray))
             	 {


					// load the additional models
					$this->load->model('applications_model', 'apps');

					// grab the user id
					$check_user = $this->user->check_email($email);

					if ($check_user === false)
					{
						// build the users data array
						$user_array = array(
							'name' => $real_name,
							'email' => $email,
							'password' => Auth::hash($password),
							'instant_message' => $im,
							'date_of_birth' => $dob,
							'join_date' => now(),
							'status' => 'pending',
							'skin_main' => $this->sys->get_skinsec_default('main'),
							'skin_admin' => $this->sys->get_skinsec_default('admin'),
							'skin_wiki' => $this->sys->get_skinsec_default('wiki'),
							'display_rank' => $this->ranks->get_rank_default(),
						);

						// create the user
						$users = $this->user->create_user($user_array);
						$user_id = $this->db->insert_id();
						$prefs = $this->user->create_user_prefs($user_id);
						$my_links = $this->sys->update_my_links($user_id);
					}

					// set the user id
					$user = ($check_user === false) ? $user_id : $check_user;

					// build the characters data array
					$character_array = array(
						'user' => $user,
						'first_name' => $first_name,
						'middle_name' => $middle_name,
						'last_name' => $last_name,
						'suffix' => $suffix,
						'position_1' => $position,
						'crew_type' => 'pending'
					);

					// create the character
					$character = $this->char->create_character($character_array);
					$character_id = $this->db->insert_id();

					// update the main character if this is their first app
					if ($check_user === false)
					{
						$main_char = array('main_char' => $character_id);
						$update_main = $this->user->update_user($user, $main_char);
					}

					// optimize the tables
					$this->sys->optimize_table('characters');
					$this->sys->optimize_table('users');

					$name = array($first_name, $middle_name, $last_name, $suffix);

					// build the apps data array
					$app_array = array(
						'app_email' => $email,
						'app_ip' => $this->input->ip_address(),
						'app_user' => $user,
						'app_user_name' => $real_name,
						'app_character' => $character_id,
						'app_character_name' => parse_name($name),
						'app_position' => $this->pos->get_position($position, 'pos_name'),
						'app_date' => now()
					);

					// create new application record
					$apps = $this->apps->insert_application($app_array);

					foreach ($_POST as $key => $value)
					{
						if (is_numeric($key))
						{
							// build the array
							$array = array(
								'data_field' => $key,
								'data_char' => $character_id,
								'data_user' => $user,
								'data_value' => $value,
								'data_updated' => now()
							);

							// insert the data
							$this->char->create_character_data($array);
						}
					}

					if ($character < 1 and $users < 1)
					{
						$message = sprintf(
							lang('flash_failure'),
							ucfirst(lang('actions_join') .' '. lang('actions_request')),
							lang('actions_submitted'),
							lang('flash_additional_contact_gm')
						);

						$flash['status'] = 'error';
						$flash['message'] = text_output($message);
					}
					else
					{
						$user_data = array(
							'email' => $email,
							'password' => $password,
							'name' => $real_name
						);

						// execute the email method
						$email_user = ($this->options['system_email'] == 'on') ? $this->_email('join_user', $user_data) : false;

						$gm_data = array(
							'email' => $email,
							'name' => $real_name,
							'id' => $character_id,
							'user' => $user,
							'sample_post' => $this->input->post('sample_post'),
							'ipaddr' => $this->input->ip_address()
						);

						// execute the email method
						$email_gm = ($this->options['system_email'] == 'on') ? $this->_email('join_gm', $gm_data) : false;

						$message = sprintf(
							lang('flash_success'),
							ucfirst(lang('actions_join') .' '. lang('actions_request')),
							lang('actions_submitted'),
							''
						);

						$flash['status'] = 'success';
						$flash['message'] = text_output($message);
					}

				}else {

					
			    

					$flash['status'] = 'error';
					$flash['message'] = text_output('Security Answer did not match or was blank. Please try again.');

				}
				}
				}


				}
			}

			$this->_regions['flash_message'] = Location::view('flash', $this->skin, 'main', $flash);
		}
		elseif ($this->options['system_email'] == 'off')
		{
			$flash['status'] = 'info';
			$flash['message'] = lang_output('flash_system_email_off');

			$this->_regions['flash_message'] = Location::view('flash', $this->skin, 'main', $flash);
		}

		if ($agree == false and $submit == false)
		{
			$data['msg'] = $this->msgs->get_message('join_disclaimer');

			if ($this->uri->segment(3) != false)
			{
				$data['position'] = $this->uri->segment(3);
			}

			$view_loc = 'main_join_1';
		}
		else
		{
			// grab the join fields
			$sections = $this->char->get_bio_sections();

			if ($sections->num_rows() > 0)
			{
				foreach ($sections->result() as $sec)
				{
					$sid = $sec->section_id;

					// set the section name
					$data['join'][$sid]['name'] = $sec->section_name;

					// grab the fields for the given section
					$fields = $this->char->get_bio_fields($sec->section_id);

					if ($fields->num_rows() > 0)
					{
						foreach ($fields->result() as $field)
						{
							$f_id = $field->field_id;

							// set the page label and help
							$data['join'][$sid]['fields'][$f_id]['field_label'] = $field->field_label_page;
							$data['join'][$sid]['fields'][$f_id]['field_help'] = $field->field_help;

							switch ($field->field_type)
							{
								case 'text':
									$input = array(
										'name' => $field->field_id,
										'id' => $field->field_fid,
										'class' => $field->field_class,
										'value' => $field->field_value,
									);

									$data['join'][$sid]['fields'][$f_id]['input'] = form_input($input);
								break;

								case 'textarea':
									$input = array(
										'name' => $field->field_id,
										'id' => $field->field_fid,
										'class' => $field->field_class,
										'value' => $field->field_value,
										'rows' => $field->field_rows
									);

									$data['join'][$sid]['fields'][$f_id]['input'] = form_textarea($input);
								break;

								case 'select':
									$value = false;
									$values = false;
									$input = false;

									$values = $this->char->get_bio_values($field->field_id);

									if ($values->num_rows() > 0)
									{
										foreach ($values->result() as $value)
										{
											$input[$value->value_field_value] = $value->value_content;
										}
									}

									$data['join'][$sid]['fields'][$f_id]['input'] = form_dropdown($field->field_id, $input);
								break;
							}
						}
					}
				}
			}

			// get the join instructions
			$data['msg'] = $this->msgs->get_message('join_instructions');

			// figure out where the view should be coming from
			$view_loc = 'main_join_2';

			// inputs
			$data['inputs'] = array(
				'name' => array(
					'name' => 'name',
					'id' => 'name'),
				'email' => array(
					'name' => 'email',
					'id' => 'email'),
				'password' => array(
					'name' => 'password',
					'id' => 'password'),
				'dob' => array(
					'name' => 'date_of_birth',
					'id' => 'date_of_birth'),
				'im' => array(
					'name' => 'instant_message',
					'id' => 'instant_message',
					'rows' => 4),
				'first_name' => array(
					'name' => 'first_name',
					'id' => 'first_name'),
				'middle_name' => array(
					'name' => 'middle_name',
					'id' => 'middle_name'),
				'last_name' => array(
					'name' => 'last_name',
					'id' => 'last_name'),
				'suffix' => array(
					'name' => 'suffix',
					'id' => 'suffix',
					'class' => 'medium'),
				'sample_post' => array(
					'name' => 'sample_post',
					'id' => 'sample_post',
					'rows' => 30),
			);

			// get the sample post question
			$data['sample_post_msg'] = $this->msgs->get_message('join_post');

			$data['label'] = array(
				'user_info' => ucwords(lang('global_user') .' '. lang('labels_information')),
				'name' => ucwords(lang('labels_name')),
				'email' => ucwords(lang('labels_email_address')),
				'password' => ucwords(lang('labels_password')),
				'dob' => lang('labels_dob'),
				'im' => ucwords(lang('labels_im')),
				'im_inst' => lang('text_im_instructions'),
				'fname' => ucwords(lang('order_first') .' '. lang('labels_name')),
				'mname' => ucwords(lang('order_middle') .' '. lang('labels_name')),
				'next' => ucwords(lang('actions_next') .' '. lang('labels_step')) .' '. RARROW,
				'lname' => ucwords(lang('order_last') .' '. lang('labels_name')),
				'suffix' => ucfirst(lang('labels_suffix')),
				'position' => ucwords(lang('global_position')),
				'other' => ucfirst(lang('labels_other')),
				'samplepost' => ucwords(lang('labels_sample_post')),
				'character' => ucfirst(lang('global_character')),
				'character_info' => ucwords(lang('global_character') .' '. lang('labels_info')),
			);
		}

		// submit button
		$data['button'] = array(
			'submit' => array(
				'type' => 'submit',
				'class' => 'button-main',
				'name' => 'submit',
				'value' => 'submit',
				'id' => 'submitJoin',
				'content' => ucwords(lang('actions_submit'))),
			'next' => array(
				'type' => 'submit',
				'class' => 'button-sec',
				'name' => 'submit',
				'value' => 'submit',
				'id' => 'nextTab',
				'content' => ucwords(lang('actions_next') .' '. lang('labels_step'))),
			'agree' => array(
				'type' => 'submit',
				'class' => 'button-main',
				'name' => 'button_agree',
				'value' => 'agree',
				'content' => ucwords(lang('actions_agree')))
		);

		$data['header'] = ucfirst(lang('actions_join'));

		$data['loading'] = array(
			'src' => Location::img('loading-circle.gif', $this->skin, 'main'),
			'alt' => lang('actions_loading'),
			'class' => 'image'
		);

		$this->_regions['content'] = Location::view($view_loc, $this->skin, 'main', $data);
		$this->_regions['javascript'] = Location::js('main_join_js', $this->skin, 'main');
		$this->_regions['title'].= $data['header'];

		Template::assign($this->_regions);

		Template::render();
	}
	}
```
Installation is now complete!

## Issues

If you encounter a bug or have a feature request, please report it on GitHub in the issue tracker here: https://github.com/reecesavage/nova-ext-anti-spam-questions/issues

## License

Copyright (c) 2021 Reece Savage.

This module is open-source software licensed under the **MIT License**. The full text of the license may be found in the `LICENSE` file.
