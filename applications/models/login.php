<?php

class login
{
    public $id, $type;
    
    function __construct($id)
    {
        global $slm_table;
        
        $this->id = $id;
        
        $db = new db();
        
        $result = $db->get_result("SELECT * FROM ".$slm_table['login']." WHERE id=".$id);
        if(count($result) > 0)
        {
            foreach($result as $record)
            {
                $this->id                           = $record['id'];
                $this->username                     = $record['username'];
                $this->password                     = $record['password'];
            }
        }
    }
    
    static function check_password($username, $password)
    {
        global $slm_table;
       
        $id = array('check' => false, 'id' => '', 'username' => '', 'password' => '');
        $db = new db();

        $result = $db->get_result("SELECT * FROM ".$slm_table['login']." WHERE username='$username' AND password='$password'");
        
        if(count($result) > 0)
        {
            $id['check'] = true;
            $id['id'] = $result[0]["id"];
            $id['username'] = $result[0]["username"];
            $id['password'] = $result[0]["password"];
        }

        return $id;
    }

    static function submit_data($data)
    {
        global $slm_table;
       
        $list = array('check' => false, 'msg' => '');
        $db = new db();

        $result = $db->insert($slm_table['fatwa'], array($data));
        
        if(!is_bool($result) && count($result) > 0)
        {
            $list['check'] = true;
            $list['msg'] = 'Fatwa Berjaya Ditambah' ;
        }
        else
        {
            $list['check'] = false;
            $list['msg'] = 'Fatwa Gagal Ditambah' ;
        }

        return $list;
    }

    static function get_list()
    {
        global $slm_table;
       
        $list = array();
        $db = new db();

        $result = $db->get_result("SELECT * FROM ".$slm_table['fatwa']);
        
        foreach($result as $record)
        {
            $list[] = new fatwa($record['id']);
        }
        
        return $list;
    }
    
    static function update_terakhir_dimuatnaik($id, $oleh, $pukul)
    {
        global $slm_table;
       
        $db = new db();
        $db->update($slm_table['fatwa'], array('terakhir_dimuatnaik_oleh' => $oleh, 'terakhir_dimuatnaik_pukul' => $pukul), array('id' => $id));
    }
    
    static function delete_data($id)
    {
        global $slm_table;
       
        $db = new db();

        $db->delete($slm_table['fatwa'], array('id' => $id));

    }

}
