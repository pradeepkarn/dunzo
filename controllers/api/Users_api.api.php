<?php

class Users_api
{
    public $get;
    public $post;
    public $files;
    public $db;
    function __construct()
    {
        $this->db = (new DB_ctrl)->db;
        $this->post = obj($_POST);
        $this->get = obj($_GET);
        $this->files = isset($_FILES) ? obj($_FILES) : null;
    }
    function login($req = null)
    {
        header('Content-Type: application/json');
        $ok = true;
        $req = obj($req);
        $data  = json_decode(file_get_contents('php://input'));
        if (isset($req->ug)) {
            if (!in_array($req->ug, USER_GROUP_LIST)) {
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
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $rules = [
            'credit' => 'required|string',
            'password' => 'required|string'
        ];

        $pass = validateData(data: arr($data), rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $user = false;
        $this->db->tableName = "pk_user";
        if (!$user) {
            $arr['username'] = $data->credit;
            $arr['password'] = md5($data->password);
            $user = $this->db->findOne($arr);
            $arr = null;
        }
        if (!$user) {
            $arr['email'] = $data->credit;
            $arr['password'] = md5($data->password);
            $user = $this->db->findOne($arr);
            $arr = null;
        }

        if (!$user) {
            $arr['mobile'] = $data->credit;
            $arr['password'] = md5($data->password);
            $user = $this->db->findOne($arr);
            $arr = null;
        }

        if ($user) {
            if ($user['user_group'] != $req->ug) {
                $ok = false;
                msg_set("Invalid login portal");
                $api['success'] = false;
                $api['data'] = null;
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            }
            $after_second = 10 * 60;
            $app_login_time = strtotime($user['app_login_time'] ?? date('Y-m-d H:i:s'));
            $time_out = $after_second + $app_login_time;
            $current_time = strtotime(date('Y-m-d H:i:s'));
            if ($current_time > $time_out) {
                $token = uniqid() . bin2hex(random_bytes(8)) . "u" . $user['id'];
                $datetime = date('Y-m-d H:i:s');
                $this->db->tableName = 'pk_user';
                $this->db->insertData = array('app_login_token' => $token, 'app_login_time' => $datetime);
                $this->db->pk($user['id']);
                $this->db->update();
                $user = $this->get_user_by_id($id = $user['id']);
                msg_set("User found, token refreshed");
                $api['success'] = true;
                $api['data'] = $user;
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            } else {
                $user = $this->get_user_by_id($id = $user['id']);
                msg_set("User found");
                $api['success'] = true;
                $api['data'] = $user;
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            }
        } else {
            msg_set("User not found");
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
    function login_via_token($req = null)
    {
        header('Content-Type: application/json');
        $ok = true;
        $req = obj($req);
        $data  = json_decode(file_get_contents('php://input'));
        if (isset($req->ug)) {
            if (!in_array($req->ug, USER_GROUP_LIST)) {
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
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $rules = [
            'token' => 'required|string'
        ];

        $pass = validateData(data: arr($data), rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $user = false;
        $user = $this->get_user_by_token($data->token);

        if ($user) {
            if ($user['user_group'] != $req->ug) {
                $ok = false;
                msg_set("Invalid login portal");
                $api['success'] = false;
                $api['data'] = null;
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            }
            msg_set("User found");
            $api['success'] = true;
            $api['data'] = $user;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }else{
            msg_set("User not found, invalid token");
            $api['success'] = true;
            $api['data'] = $user;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
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
            if (!in_array($req->ug, USER_GROUP_LIST)) {
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
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $rules = [
            'email' => 'required|email',
            // 'username' => 'required|string|min:4|max:16',
            'image' => 'required|file',
            'first_name' => 'required|string',
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
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }

        $request = obj($data);
        $request->username = $request->username??uniqid();
        $this->db = $this->db;
        $pdo = $this->db->conn;
        $pdo->beginTransaction();
        $this->db->tableName = 'pk_user';
        $username = generate_clean_username($request->username );
        $username_exists = $this->db->get(['username' => $username]);
        $email_exists = $this->db->get(['email' => $request->email]);
        if ($username_exists) {
            $_SESSION['msg'][] = 'Usernam not availble please try with another username';
            $ok = false;
        }
        if ($email_exists) {
            $_SESSION['msg'][] = 'Email is already exists';
            $ok = false;
        }
        if (!$ok) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        if (isset($request->email)) {
            $arr = null;
            $arr['user_group'] = $req->ug;
            $arr['email'] = $request->email;
            $arr['username'] = $username;
            $arr['first_name'] = $request->first_name;
            $arr['last_name'] = $request->last_name ?? null;
            $arr['isd_code'] = intval($request?->isd_code) ?? null;
            $arr['mobile'] = intval($request?->mobile) ?? null;
            $arr['password'] = md5($request->password);
            $arr['nid_no'] = sanitize_remove_tags($request->nid_no ?? null);
            $arr['dl_no'] = sanitize_remove_tags($request->dl_no ?? null);
            $arr['vhcl_no'] = sanitize_remove_tags($request->vhcl_no ?? null);
            if (isset($request->bio)) {
                $arr['bio'] = $request->bio;
            }
            $arr['created_at'] = date('Y-m-d H:i:s');
            $this->db->tableName = 'pk_user';
            $this->db->insertData = $arr;
            try {
                $userid = $this->db->create();
                $filearr = $this->upload_files($userid, $request);
                if ($filearr) {
                    $this->db->pk($userid);
                    $this->db->insertData = $filearr;
                    $this->db->update();
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
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        } else {
            $api['success'] = true;
            $api['data'] = [];
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
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

    function search_users($req = null)
    {
        header('Content-Type: application/json');
        $ok = true;
        $req = obj($_GET);
        $data  = $_GET;

        $rules = [
            'q' => 'required|string'
        ];

        $pass = validateData(data: arr($data), rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $users = $this->user_search_list(user_group: "driver", keyword: $req->q);
        if (count($users) > 0) {
            $searchedData = array_map(function ($user) {
                return [
                    "id" => $user["id"],
                    "first_name" => $user["first_name"],
                    "last_name" => $user["last_name"],
                    "username" => $user["username"],
                    "email" => $user["email"],
                    "isd_code" => $user["isd_code"],
                    "mobile" => $user["mobile"],
                ];
            }, $users);
            $api['success'] = true;
            $api['data'] = $searchedData;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        } else {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }

    // User search list
    public function user_search_list($user_group = 'driver', $keyword = '', $ord = "DESC", $limit = 5, $active = 1)
    {
        $cntobj = new Model('pk_user');
        $search_arr['username'] = $keyword;
        $search_arr['email'] = $keyword;
        $search_arr['first_name'] = $keyword;
        $search_arr['last_name'] = $keyword;
        $search_arr['mobile'] = $keyword;
        return $cntobj->search(
            assoc_arr: $search_arr,
            ord: $ord,
            limit: $limit,
            whr_arr: array('user_group' => $user_group, 'is_active' => $active)
        );
    }

    function get_user_by_id($id = null)
    {
        if ($id) {
            $u = $this->db->showOne("select * from pk_user where id = $id");
            if ($u) {
                $u = obj($u);
                return array(
                    'id' => strval($u->id),
                    'user_group' => $u->user_group,
                    'username' => strval($u->username),
                    'first_name' => $u->first_name,
                    'last_name' => $u->last_name,
                    'image' => img_or_null($u->image),
                    'email' => $u->email,
                    'isd_code' => $u->isd_code,
                    'mobile' => $u->mobile,
                    'token' => $u->app_login_token,
                );
            }
        }
        return false;
    }
    function get_user_by_token($token = null)
    {
        if ($token) {
            $u = $this->db->showOne("select * from pk_user where app_login_token = '$token'");
            if ($u) {
                $u = obj($u);
                return array(
                    'id' => strval($u->id),
                    'user_group' => $u->user_group,
                    'username' => strval($u->username),
                    'first_name' => $u->first_name,
                    'last_name' => $u->last_name,
                    'image' => img_or_null($u->image),
                    'email' => $u->email,
                    'isd_code' => $u->isd_code,
                    'mobile' => $u->mobile,
                    'token' => $u->app_login_token,
                );
            }
        }
        return false;
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
