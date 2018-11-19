<?php 
    class Users extends CI_Controller{
        // Register user
        public function cadastrar(){
            $data['title'] = 'Cadastro';

            $this->form_validation->set_rules('nome','Nome', 'required');
            $this->form_validation->set_rules('nome_empresa','Nome da empresa', 'required');
            $this->form_validation->set_rules('telefone','Telefone', 'required|callback_check_telefone_exists');
            $this->form_validation->set_rules('email','Email', 'required|callback_check_email_exists');
            $this->form_validation->set_rules('senha','Senha', 'required');
            $this->form_validation->set_rules('senha2','Confirmar Senha', 'matches[senha]');

            if($this ->form_validation->run() === FALSE){
                $this->load->view('templates/header');
                $this->load->view('users/cadastrar', $data);
                $this->load->view('templates/footer');
            }else {
                // Encrypt password
                $enc_senha = md5($this->input->post('senha'));
                $this->user_model->cadastrar($enc_senha);
                // Set message
                $this->session->set_flashdata('user_registered', 'Cadastro realizado com sucesso. Por favor, faça o login.');
                
                redirect('users/login');
            }
        }
        // Login user
        public function login(){
            $data['title'] = 'Login';

            $this->form_validation->set_rules('email','email', 'required');
            $this->form_validation->set_rules('senha','Senha', 'required');
            
            if($this->form_validation->run() === FALSE){
                $this->load->view('templates/header');
                $this->load->view('users/login', $data);
                $this->load->view('templates/footer');
            }else {
                // Get email
                $email = $this->input->post('email');
                // Get and encrypt password
                $senha = md5($this->input->post('senha'));

                // Login user
                $user_id = $this->user_model->login($email, $senha);

                if($user_id){
                    // Create session
                    $user_data = array(
                        'user_id' => $user_id,
                        'email' => $email,
                        'logged_in' => true
                    );

                    $this->session->set_userdata($user_data);
                    redirect('pages/home');
                }else{
                    // Set message
                    $this->session->set_flashdata('login_failed', 'Login inválido.');
                    redirect('users/login');
                }

            }
        }

        // Log user out
        public function logout(){
            $this->session->unset_userdata('logged_in');
            $this->session->unset_userdata('user_id');
            $this->session->unset_userdata('email');

            redirect('users/login');

        }
        

        public function check_telefone_exists($telefone){
            $this->form_validation->set_message('check_telefone_exists', 'Telefone já cadastrado. Por favor insira outro.');
            if ($this->user_model->check_telefone_exists($telefone)) {
                return true;
            } else{
                return false;
            }
        }

        public function check_email_exists($email){
            $this->form_validation->set_message('check_email_exists', 'E-mail já cadastrado. Por favor insira outro.');
            if ($this->user_model->check_email_exists($email)) {
                return true;
            } else{
                return false;
            }
        }

        
    }