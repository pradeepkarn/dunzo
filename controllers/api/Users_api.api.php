<?php

class Users_api extends Main_ctrl
{

    function create_account($req=null) {
        header('Content-Type: application/json');
        $ok = true;
        $req = obj($req);
        $data  = $_POST;
        $data['image'] = $_FILES['image']??null;
        $data['vhcl_doc'] = $_FILES['vhcl_doc']??null;
        $data['dl_doc'] = $_FILES['dl_doc']??null;
        $data['nid_doc'] = $_FILES['nid_doc']??null;
        
        if(isset($req->ug)){
            if (!in_array($req->ug, USER_GROUP)) {
                $ok = false;
               msg_set("Invalid account group");
            }
        }else{
            $ok = false;
            msg_set("No user group provided");
        }
        if (!$ok) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return:true);
            echo json_encode($api);
            exit;
        }
        // myprint($data);
        // return;
        
        $rules = [
            'email' => 'required|email',
            'username' => 'required|string|min:4|max:16',
            'image' => 'required|file',
            'dl_doc' => 'required|file',
            'nid_doc' => 'required|file',
            'vhcl_doc' => 'required|file',
            'first_name' => 'required|string',
            'password' => 'required|string'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return:true);
            echo json_encode($api);
            exit;
        }
       
        $request = obj($data);
        $username_exists = (new Model('pk_user'))->exists(['username' => generate_clean_username($request->username)]);
        $email_exists = (new Model('pk_user'))->exists(['email' => $request->email]);
        if ($username_exists) {
            $_SESSION['msg'][] = 'Usernam not availble please try with another usernam';
            $ok = false;
        }
        if ($email_exists) {
            $_SESSION['msg'][] = 'Email is already exists';
            $ok = false;
        }
        if (!$ok) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return:true);
            echo json_encode($api);
            exit;
        }
        if (isset($request->email)) {
            $arr = null;
            $arr['user_group'] = $req->ug;
            $arr['email'] = $request->email;
            $arr['username'] = generate_clean_username($request->username);
            $arr['first_name'] = sanitize_remove_tags($request->first_name);
            $arr['last_name'] = sanitize_remove_tags($request->last_name);
            $arr['password'] = md5($request->password);
            if (isset($request->bio)) {
                $arr['bio'] = sanitize_remove_tags($request->bio);
            }

            $arr['created_at'] = date('Y-m-d H:i:s');
            $postid = (new Model('pk_user'))->store($arr);
            if (intval($postid)) {
                $ext = pathinfo($request->image['name'], PATHINFO_EXTENSION);
                $imgname = str_replace(" ", "_", getUrlSafeString($request->username)) . uniqid("_") . "." . $ext;
                $dir = MEDIA_ROOT . "images/profiles/" . $imgname;
                $upload = move_uploaded_file($request->image['tmp_name'], $dir);
                if ($upload) {
                    (new Model('pk_user'))->update($postid, array('image' => $imgname));
                }
                msg_set('Account created');
                $ok = true;
            } else {
                msg_set('Account not created');
                $ok = false;
            }
        } else {
            msg_set('Missing required field, uaser not created');
            $ok = false;
        }
        if (!$ok) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return:true);
            echo json_encode($api);
            exit;
        }else{
            $api['success'] = true;
            $api['data'] = [];
            $api['msg'] = msg_ssn(return:true);
            echo json_encode($api);
            exit;
        }

    }

    function load_users($req)
    {
        $this->user_list(ord: 'desc', page: $this->get->page, limit: $this->get->limit);
    }

    public function user_list($ord = "DESC", $page = 1, $limit = 10, $active = 1)
    {
        header('Content-Type: application/json');
        $data_limit = "{$page},{$limit}";
        $cntobj = new Model('pk_user');
        $users = $cntobj->filter_index(array('user_group' => 'user', 'is_active' => $active), $ord, $limit = $data_limit);
        $user_list = array();
        foreach ($users as $u) {
            $myreq = obj(check_request($myid = USER['id'], $req_to = $u['id']));
            // myprint($myreq);
            $is_liked = is_liked($myid = USER['id'], $obj_id = $u['id'], $obj_group = 'profile');

            $user_list[]  = array(
                'id' => $u['id'],
                'first_name' => $u['first_name'],
                'last_name' => $u['last_name'],
                'image' => dp_or_null($u['image']),
                'dob' => $u['dob'],
                'age' => getAgeFromDOB($u['dob']),
                'caste' => $u['caste'],
                'caste_detail' => $u['caste_detail'],
                'gender' => $u['gender'],
                'religion' => $u['religion'],
                'occupation' => $u['occupation'],
                'education' => $u['education'],
                'address' => $u['address'],
                'email' => $u['email'],
                'annual_income' => $u['annual_income'],
                'bride_or_groom' => bride_or_grom($u['gender']),
                'profile_link'=> "/".home.route('showPublicProfile', ['profile_id' => $u['id']]),
                'is_liked'=>$is_liked,
                'myreq'=>$myreq
            );
        }
        if (count($users) > 0) {
            $data['success'] = true;
            $data['data'] = $user_list;
            $data['msg'] = null;
            echo json_encode($data);
            exit;
        } else {
            $data['success'] = false;
            $data['data'] = null;
            $data['msg'] = 'No data found';
            echo json_encode($data);
            exit;
        }
    }
}
