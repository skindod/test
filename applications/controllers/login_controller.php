<?php

/**
 * 
 */
class login_controller extends slm_controller {

    function __construct() {
        parent::__construct();
        if (session::get('log_status') == true) 
        {
            $this->redirect('dashboard');
        }
    }

    function login_action() 
    {
        $this->render('login');
    }

    function login_submit_action() 
    {
        $username = $this->request->post('username');
        $password = $this->request->post('password');
        
        $password_check = login::check_password($username, $password);

        # check if password is correct
        if($password_check['check'] == true)
        { 
            session::set('id', $password_check['id']);
            session::set('username', $password_check['username']);
            session::set('password', $password_check['password']);
            session::set('log_status', true);

            $this->redirect('dashboard');
        }
        else if($password_check['check'] == false)
        {
            session::set('log_status', false);
            session::set('notification', "Kata Laluan Salah. Sila cuba sekali lagi.");
            $this->redirect('login');
        } 
    }
    
    function forgot_password_action() 
    {
        $this->render('recoverpw');
    }
    
    function forgot_submit_action() 
    {
        $email = $this->request->post('email');
        $email_check = login::check_email($email);

        if($email_check['check'] == true)
        {
            // the message
            $msg = "Assalamualaikum ".$email_check['name'].", \n\nNama Pengguna anda ialah : ".$email_check['username']." \nKata laluan anda ialah : ".$email_check['password']." \n\nTerima kasih. \n\nUnit Teknologi Maklumat";

            // use wordwrap() if lines are longer than 70 characters
            $msg = wordwrap($msg,70);

            // send email
            mail($email_check['email'],"[Sistem Pangkalan Data Fatwa] Lupa Kata Laluan",$msg);
            session::set('notification', "Emel telah dihantar.<br>Sila semak emel anda dan log masuk semula.");
            $this->redirect('login');
        }
        if($email_check['check'] == false)
        {
            session::set('notification', $email_check['msg']);
            $this->redirect('login');
        } 
    }
    
    function logout_action() 
    {
        unset($_SESSION['log_status']);
        $this->redirect('login');
    }
    
    function update_profile_action() 
    {
            global $slm_app;

            $name = $this->request->post('name');
            $email = $this->request->post('email');
            $username = $this->request->post('username');
            $old_password = $this->request->post('old_password');
            $new_password = $this->request->post('new_password1');
            $retype_new_password = $this->request->post('new_password2');

            if(isset($_FILES['file']))
            {
                $file = $_FILES['file'];
                //File properties
                $file_name = $file['name'];
                $file_tmp = $file['tmp_name'];
                $file_size = $file['size'];

                //Work out the file extension
                $file_ext1 = explode('.',$file_name);
                $file_ext = strtolower(end($file_ext1));

                $allowed = array('png');

                if(in_array($file_ext, $allowed))
                {
                    $file_destination = $slm_app['upload_dir']."picture/".session::get('id').".".$file_ext;

                    if(move_uploaded_file($file_tmp, $file_destination))
                    {
                        session::set('notification', 'Gambar berjaya disimpan!');
                    }
                    else
                    {
                        session::set('notification', 'Gambar gagal disimpan!');
                    }
                }
                else
                {
                    session::set('notification', 'Format Gambar Tidak Sah!');
                }
            }

            if($old_password == "" && $new_password == "" && $retype_new_password == "")
            {
                $result_email = login::update_profile($name, $email, $username);
                if($result_email['check'] == true)
                {
                    session::set('notification', $result_email['msg']);
                    session::set('name', $name);
                    session::set('username', $username);
                    session::set('email', $email);
                    $this->redirect('dashboard'); 
                }
                else if($result_email['check'] == false)
                {
                    session::set('notification', $result_email['msg']);
                    $this->redirect('dashboard');
                }
            }
            else if($old_password != "" && $new_password != "" && $retype_new_password != "")
            {
                if($new_password == $retype_new_password)
                {
                    if(session::get('password') == $old_password)
                    {
                        $result = login::update_profile_and_password($name, $email, $username, $new_password);

                        if($result['check'] == true)
                        {
                            session::set('notification', $result['msg']);
                            session::set('name', $name);
                            session::set('username', $username);
                            session::set('email', $email);
                            $this->redirect('dashboard'); 
                        }
                        else if($result['check'] == false)
                        {
                            session::set('notification', $result['msg']);
                            $this->redirect('dashboard');
                        }
                    }
                    else
                    {
                        session::set('notification', 'Kata laluan lama anda salah!');
                        $this->redirect('dashboard');
                    }

                }
                else
                {
                    session::set('notification', 'Kata laluan baru anda tidak sama!');
                    $this->redirect('settings_account');
                }

            }
            else
            {
                session::set('notification', 'Sila isi ketiga-tiga ruang kosong untuk menukar kata laluan anda!');
                $this->redirect('settings_account');
            }
           
    }
}
