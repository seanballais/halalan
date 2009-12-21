<?php

class Gate extends Controller {

	function Gate()
	{
		parent::Controller();
		if ($this->uri->segment(2) != 'results' && $this->uri->segment(2) != 'statistics' && $this->uri->segment(2) != 'logout')
		{
			if ($this->session->userdata('admin'))
			{
				redirect('admin/home');
			}
			else if ($this->session->userdata('voter'))
			{
				redirect('voter/vote');
			}
		}
		
	}

	function index()
	{
		redirect('gate/voter');
	}

	function voter()
	{
		$messages = $this->_get_messages();
		$data['messages'] = $messages['messages'];
		$data['message_type'] = $messages['message_type'];
		$this->load->model('Option');
		$data['option'] = $this->Option->select(1);
		$data['settings'] = $this->config->item('halalan');
		$gate['login'] = 'voter';
		$gate['title'] = e('gate_voter_title');
		$gate['body'] = $this->load->view('gate/voter', $data, TRUE);
		$this->load->view('gate', $gate);
	}

	function voter_login()
	{
		if (!$this->input->post('username') || !$this->input->post('password'))
		{
			$error[] = e('gate_common_login_failure');
			$this->session->set_flashdata('error', $error);
			redirect('gate/voter');
		}
		$this->load->model('Boter');
		$username = $this->input->post('username');
		if (strlen($this->input->post('password')) == 40)
			$password = $this->input->post('password');
		else
			$password = sha1($this->input->post('password'));
		if ($voter = $this->Boter->authenticate($username, $password))
		{
			if (strtotime($voter['login']) > strtotime($voter['logout']))
			{
				$error[] = e('gate_voter_currently_logged_in');
				$this->session->set_flashdata('error', $error);
				redirect('gate/voter');
			}

			if ($voter['voted'] == 1)
			{
				//$error[] = e('gate_voter_already_voted');
				//$this->session->set_flashdata('error', $error);
				//redirect('gate/voter');
				$this->session->set_userdata('voter_id', $voter['id']);
				redirect('voter/votes');
			}
			else
			{
				$this->Boter->update(array('login'=>date("Y-m-d H:i:s"), 'ip_address'=>ip2long($this->input->ip_address())), $voter['id']);
				// don't save password to session
				unset($voter['password']);
				$this->session->set_userdata('voter', $voter);
				redirect('voter/vote');
			}
			
		}
		else
		{
			$error[] = e('gate_common_login_failure');
			$this->session->set_flashdata('error', $error);
			redirect('gate/voter');
		}
	}
	
	function admin()
	{
		$messages = $this->_get_messages();
		$data['messages'] = $messages['messages'];
		$data['message_type'] = $messages['message_type'];
		$data['settings'] = $this->config->item('halalan');
		$gate['login'] = 'admin';
		$gate['title'] = e('gate_admin_title');
		$gate['body'] = $this->load->view('gate/admin', $data, TRUE);
		$this->load->view('gate', $gate);
	}

	function admin_login()
	{
		if (!$this->input->post('username') || !$this->input->post('password'))
		{
			$error[] = e('gate_common_login_failure');
			$this->session->set_flashdata('error', $error);
			redirect('gate/admin');
		}
		$this->load->model('Abmin');
		$username = $this->input->post('username');
		if (strlen($this->input->post('password')) == 40)
			$password = $this->input->post('password');
		else
			$password = sha1($this->input->post('password'));
		if ($admin = $this->Abmin->authenticate($username, $password))
		{
			// don't save password to session
			unset($admin['password']);
			$this->session->set_userdata('admin', $admin);
			redirect('admin/home');
		}
		else
		{
			$error[] = e('gate_common_login_failure');
			$this->session->set_flashdata('error', $error);
			redirect('gate/admin');
		}
	}

	function logout()
	{
		if ($this->session->userdata('admin'))
		{
			$gate = 'admin';
		}
		else if($voter = $this->session->userdata('voter'))
		{
			// voter has not yet voted
			$this->load->model('Boter');
			$this->Boter->update(array('logout'=>date("Y-m-d H:i:s")), $voter['id']);
			$gate = 'voter';
		}
		else
		{
			// voter has already voted
			$gate = 'voter';
		}
		setcookie('halalan_cookie', '', time() - 3600, '/'); // destroy cookie
		$this->session->sess_destroy();
		redirect('gate/' . $gate);
	}

	function results()
	{
		$this->load->model('Option');
		$option = $this->Option->select(1);
		$settings = $this->config->item('halalan');
		if (($option['result'] && !$option['status']) || ($settings['realtime_results'] && $this->session->userdata('admin')))
		{
			$selected = array_keys($this->input->post('positions', TRUE));
			if (!empty($selected))
			{
				$this->load->model('Abstain');
				$this->load->model('Candidate');
				$this->load->model('Party');
				$this->load->model('Vote');
			}
			else
			{
				$selected = FALSE;
			}
			$this->load->model('Position');
			$all_positions = $this->Position->select_all();
			$positions = $this->Position->select_multiple($selected);
			foreach ($positions as $key=>$position)
			{
				$candidates = array();
				$votes = $this->Vote->count_all_by_position_id($position['id']);
				foreach ($votes as $vote)
				{
					$candidate_id = $vote['candidate_id'];
					$candidate = $this->Candidate->select($candidate_id);
					$candidates[$candidate_id] = $candidate;
					$candidates[$candidate_id]['votes'] = $vote['votes'];
					$candidates[$candidate_id]['party'] = $this->Party->select($candidate['party_id']);
				}
				$positions[$key]['candidates'] = $candidates;
				$positions[$key]['abstains'] = $this->Abstain->count_all_by_position_id($position['id']);
			}
			$data['settings'] = $this->config->item('halalan');
			$data['all_positions'] = $all_positions;
			$data['selected'] = $selected;
			$data['positions'] = $positions;
			$gate['login'] = 'result';
			$gate['title'] = e('gate_result_title');
			$gate['body'] = $this->load->view('gate/result', $data, TRUE);
			$this->load->view('gate', $gate);
		}
		else
		{
			$this->session->set_flashdata('error', array(e('gate_result_unavailable')));
			redirect('gate/voter');
		}
	}

	function statistics()
	{
		$this->load->model('Option');
		$option = $this->Option->select(1);
		$settings = $this->config->item('halalan');
		if (($option['result'] && !$option['status']) || ($settings['realtime_results'] && $this->session->userdata('admin')))
		{
			$this->load->model('Statistics');
			$data['voter_count'] = $this->Statistics->count_all_voters();
			$data['voted_count'] = $this->Statistics->count_voted();
			$data['settings'] = $this->config->item('halalan');
			$gate['login'] = 'statistics';
			$gate['title'] = e('gate_statistics_title');
			$gate['body'] = $this->load->view('gate/statistics', $data, TRUE);
			$this->load->view('gate', $gate);
		}
		else
		{
			$this->session->set_flashdata('error', array(e('gate_result_unavailable')));
			redirect('gate/voter');
		}
	}

	function _get_messages()
	{
		$messages = '';
		$message_type = '';
		if($error = $this->session->flashdata('error'))
		{
			$messages = $error;
			$message_type = 'negative';
		}
		else if($success = $this->session->flashdata('success'))
		{
			$messages = $success;
			$message_type = 'positive';
		}
		return array('messages'=>$messages, 'message_type'=>$message_type);
	}

}

?>
