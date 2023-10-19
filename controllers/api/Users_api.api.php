<?php

class Users_api extends Main_ctrl
{

    function create_account($req = null)
    {
        header('Content-Type: application/json');
        $ok = true;
        $req = obj($req);
        $data  = $_POST;
        $data['image'] = $_FILES['image'] ?? null;
        $data['vhcl_doc'] = $_FILES['vhcl_doc'] ?? null;
        $data['dl_doc'] = $_FILES['dl_doc'] ?? null;
        $data['nid_doc'] = $_FILES['nid_doc'] ?? null;

        if (isset($req->ug)) {
            if (!in_array($req->ug, USER_GROUP)) {
                $ok = false;
                msg_set("Invalid account group");
            }
        } else {
            $ok = false;
            msg_set("No user group provided");
        }
        if (!$ok) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true);
            echo json_encode($api);
            exit;
        }
        $rules = [
            'email' => 'required|email',
            'username' => 'required|string|min:4|max:16',
            'image' => 'required|file',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'password' => 'required|string'
        ];
        if ($req->ug == 'driver') {
            $rules_driver = [
                'dl_doc' => 'required|file',
                'nid_doc' => 'required|file',
                'vhcl_doc' => 'required|file',
                'dl_no' => 'required|string',
                'nid_no' => 'required|string',
                'vhcl_no' => 'required|string',
            ];
            $rules = array_merge($rules, $rules_driver);
        }
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true);
            echo json_encode($api);
            exit;
        }

        $request = obj($data);
        $db = new Dbobjects;
        $pdo = $db->conn;
        $pdo->beginTransaction();
        $db->tableName = 'pk_user';
        $username = generate_clean_username($request->username);
        $username_exists = $db->get(['username' => $username]);
        $email_exists = $db->get(['email' => $request->email]);
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
            $api['msg'] = msg_ssn(return: true);
            echo json_encode($api);
            exit;
        }
        if (isset($request->email)) {
            $arr = null;
            $arr['user_group'] = $req->ug;
            $arr['email'] = $request->email;
            $arr['username'] = $username;
            $arr['first_name'] = $request->first_name;
            $arr['last_name'] = $request->last_name;
            $arr['password'] = md5($request->password);
            $arr['nid_no'] = sanitize_remove_tags($request->nid_no ?? null);
            $arr['dl_no'] = sanitize_remove_tags($request->dl_no ?? null);
            $arr['vhcl_no'] = sanitize_remove_tags($request->vhcl_no ?? null);
            if (isset($request->bio)) {
                $arr['bio'] = $request->bio;
            }
            $arr['created_at'] = date('Y-m-d H:i:s');
            $db->tableName = 'pk_user';
            $db->insertData = $arr;
            try {
                $userid = $db->create();
                $filearr = $this->upload_files($userid, $request);
                if ($filearr) {
                    $db->pk($userid);
                    $db->insertData = $filearr;
                    $db->update();
                }
                msg_set('Account created');
                $ok = true;
                $pdo->commit();
            } catch (PDOException $th) {
                $pdo->rollBack();
                msg_set('Account not created');
                $ok = false;
            }
        } else {
            $pdo->rollBack();
            msg_set('Missing required field, uaser not created');
            $ok = false;
        }
        if (!$ok) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true);
            echo json_encode($api);
            exit;
        } else {
            $api['success'] = true;
            $api['data'] = [];
            $api['msg'] = msg_ssn(return: true);
            echo json_encode($api);
            exit;
        }
    }

    function upload_files($postid, $request, $user = null)
    {
        if (intval($postid)) {
            $old = $user ? obj($user) : null;
            if (isset($request->image) && $request->image['name'] != "" && $request->image['error'] == 0) {
                $ext = pathinfo($request->image['name'], PATHINFO_EXTENSION);
                $imgname = str_replace(" ", "_", getUrlSafeString($request->username)) . uniqid("_") . "." . $ext;
                $dir = MEDIA_ROOT . "images/profiles/" . $imgname;
                $upload = move_uploaded_file($request->image['tmp_name'], $dir);
                if ($upload) {
                    $arr['image'] = $imgname;
                    if ($old) {
                        if ($old->image != "") {
                            $olddir = MEDIA_ROOT . "images/profiles/" . $old->image;
                            if (file_exists($olddir)) {
                                unlink($olddir);
                            }
                        }
                    }
                    $filearr['image'] = $imgname;
                }
            }
            if (isset($request->nid_doc) && $request->nid_doc['name'] != "" && $request->nid_doc['error'] == 0) {
                $ext = pathinfo($request->nid_doc['name'], PATHINFO_EXTENSION);
                $docname = str_replace(" ", "_", getUrlSafeString($request->username)) . uniqid("_") . "." . $ext;
                $dir = MEDIA_ROOT . "docs/" . $docname;
                $upload = move_uploaded_file($request->nid_doc['tmp_name'], $dir);
                if ($upload) {
                    $arr['nid_doc'] = $docname;
                    if ($old) {
                        if ($old->image != "") {
                            $olddir = MEDIA_ROOT . "docs/" . $old->nid_doc;
                            if (file_exists($olddir)) {
                                unlink($olddir);
                            }
                        }
                    }
                    $filearr['nid_doc'] = $docname;
                }
            }
            if (isset($request->dl_doc) && $request->dl_doc['name'] != "" && $request->dl_doc['error'] == 0) {
                $ext = pathinfo($request->dl_doc['name'], PATHINFO_EXTENSION);
                $docname = str_replace(" ", "_", getUrlSafeString($request->username)) . uniqid("_") . "." . $ext;
                $dir = MEDIA_ROOT . "docs/" . $docname;
                $upload = move_uploaded_file($request->dl_doc['tmp_name'], $dir);
                if ($upload) {
                    $arr['dl_doc'] = $docname;
                    if ($old) {
                        if ($old->image != "") {
                            $olddir = MEDIA_ROOT . "docs/" . $old->dl_doc;
                            if (file_exists($olddir)) {
                                unlink($olddir);
                            }
                        }
                    }
                    $filearr['dl_doc'] = $imgname;
                }
            }
            if (isset($request->vhcl_doc) && $request->vhcl_doc['name'] != "" && $request->vhcl_doc['error'] == 0) {
                $ext = pathinfo($request->vhcl_doc['name'], PATHINFO_EXTENSION);
                $docname = str_replace(" ", "_", getUrlSafeString($request->username)) . uniqid("_") . "." . $ext;
                $dir = MEDIA_ROOT . "docs/" . $docname;
                $upload = move_uploaded_file($request->vhcl_doc['tmp_name'], $dir);
                if ($upload) {
                    $arr['vhcl_doc'] = $docname;
                    if ($old) {
                        if ($old->image != "") {
                            $olddir = MEDIA_ROOT . "docs/" . $old->vhcl_doc;
                            if (file_exists($olddir)) {
                                unlink($olddir);
                            }
                        }
                    }
                    $filearr['vhcl_doc'] = $docname;
                }
            }
            return $filearr;
        } else {
            return false;
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
                'profile_link' => "/" . home . route('showPublicProfile', ['profile_id' => $u['id']]),
                'is_liked' => $is_liked,
                'myreq' => $myreq
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
