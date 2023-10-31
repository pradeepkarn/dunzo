<?php
class Support_admin_ctrl
{
    // support list page
    public function list($req = null)
    {
        $req = obj($req);
        $current_page = 0;
        $data_limit = DB_ROW_LIMIT;
        $page_limit = "0,$data_limit";
        $cp = 0;
        if (isset($req->page) && intval($req->page)) {
            $cp = $req->page;
            $current_page = (abs($req->page) - 1) * $data_limit;
            $page_limit = "$current_page,$data_limit";
        }
        $total_support = $this->support_list(content_group: $req->cg, ord: "DESC", limit: 10000, active: 1);
        $tc = count($total_support);
        if ($tc %  $data_limit == 0) {
            $tc = $tc / $data_limit;
        } else {
            $tc = floor($tc / $data_limit) + 1;
        }
        if (isset($req->search)) {
            $support_list = $this->support_search_list(content_group: $req->cg, keyword: $req->search, ord: "DESC", limit: $page_limit, active: 1);
        } else {
            $support_list = $this->support_list(content_group: $req->cg, ord: "DESC", limit: $page_limit, active: 1);
        }
        $context = (object) array(
            'page' => 'supports/list.php',
            'data' => (object) array(
                'req' => obj($req),
                'support_list' => $support_list,
                'total_support' => $tc,
                'current_page' => $cp,
                'is_active' => true
            )
        );
        $this->render_main($context);
    }
    public function create($req = null)
    {
        $req = obj($req);
        $current_page = 0;
        $data_limit = DB_ROW_LIMIT;
        $page_limit = "0,$data_limit";
        $cp = 0;
        if (isset($req->page) && intval($req->page)) {
            $cp = $req->page;
            $current_page = (abs($req->page) - 1) * $data_limit;
            $page_limit = "$current_page,$data_limit";
        }
        $total_support = $this->support_list(content_group: $req->cg, ord: "DESC", limit: 10000, active: 1);
        $tc = count($total_support);
        if ($tc %  $data_limit == 0) {
            $tc = $tc / $data_limit;
        } else {
            $tc = floor($tc / $data_limit) + 1;
        }
        if (isset($req->search)) {
            $support_list = $this->support_search_list(content_group: $req->cg, keyword: $req->search, ord: "DESC", limit: $page_limit, active: 1);
        } else {
            $support_list = $this->support_list(content_group: $req->cg, ord: "DESC", limit: $page_limit, active: 1);
        }
        $context = (object) array(
            'page' => 'supports/create.php',
            'data' => (object) array(
                'req' => obj($req),
                'support_list' => $support_list,
                'total_support' => $tc,
                'current_page' => $cp,
                'is_active' => true
            )
        );
        $this->render_main($context);
    }

    public function create_ticket($req = null)
    {
        $request = obj($_POST);
        if (isset($request->email) && isset($request->name) && isset($request->post_id)) {
            $rules = [
                'name' => 'required|string',
                'email' => 'required|email',
                'post_id' => 'required|integer',
                'message' => 'required|string|max:1000|min:1'
            ];
            $pass = validateData(data: $_POST, rules: $rules);
            if (!$pass) {
                echo js_alert(msg_ssn("msg", true));
                exit;
            }
            $reply_to = (isset($request->reply_to) && $request->reply_to > 0) ? $request->reply_to : 0;
            $is_spam = detectSpam($request->message);
            $lastId = (new Model('supports'))->store(
                [
                    'name' => sanitize_remove_tags($request->name),
                    'email' => sanitize_remove_tags($request->email),
                    'content_id' => $request->post_id,
                    'is_active' => 1,
                    'is_approved' => 0,
                    'message' => sanitize_remove_tags($request->message),
                    'replied_to' => $reply_to,
                    'content_group' => $is_spam ? "closed" : "open"
                ]
            );
            if ($lastId) {
                $_SESSION['msg'][] = "Your support has been sent now, please wait for approval";
                echo js_alert(msg_ssn("msg", true));
                echo RELOAD;
                exit;
            }
        } else {
            return js_alert("support not submitted");
        }
    }
    // support trash list page
    public function trash_list($req = null)
    {
        $req = obj($req);

        $current_page = 0;
        $data_limit = DB_ROW_LIMIT;
        $page_limit = "0,$data_limit";
        $cp = 0;
        if (isset($req->page) && intval($req->page)) {
            $cp = $req->page;
            $current_page = (abs($req->page) - 1) * $data_limit;
            $page_limit = "$current_page,$data_limit";
        }
        $total_support = $this->support_list(content_group: $req->cg, ord: "DESC", limit: 10000, active: 0);
        $tc = count($total_support);
        if ($tc %  $data_limit == 0) {
            $tc = $tc / $data_limit;
        } else {
            $tp = floor($tc / $data_limit) + 1;
        }
        if (isset($req->search)) {
            $support_list = $this->support_search_list($content_group = $req->cg, $keyword = $req->search, $ord = "DESC", $limit = $page_limit, $active = 0);
        } else {
            $support_list = $this->support_list(content_group: $req->cg, ord: "DESC", limit: $page_limit, active: 0);
        }
        $context = (object) array(
            'page' => 'supports/list.php',
            'data' => (object) array(
                'req' => obj($req),
                'support_list' => $support_list,
                'total_support' => $tc,
                'current_page' => $cp,
                'is_active' => false
            )
        );
        $this->render_main($context);
    }
    public function toggle_closed($req = null)
    {

        $request = json_decode(file_get_contents('php://input'));
        if (isset($request->support_id) && isset($request->action) && ($request->action == 'content_group')) {
            $id = $request->support_id;
            $tobj = new Model('supports');
            $arr['id'] = $id;
            $arr[$request->action] = 'closed';
            $support = $tobj->filter_index($arr);
            if (count($support) > 0) {
                $tobj->update($id, [$request->action => 'open']);
                $res['msg'] = 'success';
                $res['data'] = "Support moved to open";
            } else {
                $tobj->update($id, [$request->action => 'closed']);
                $res['msg'] = 'success';
                $res['data'] = "Support is closed now";
            }
            echo json_encode($res);
            exit;
        } else {
            $res['msg'] = 'Something went wrong';
            $res['data'] = null;
            echo json_encode($res);
            exit;
        }
    }
    public function toggle_approve($req = null)
    {

        $request = json_decode(file_get_contents('php://input'));
        if (isset($request->support_id) && isset($request->action) && ($request->action == 'is_approved' || $request->action == 'toggle_closed')) {
            $id = $request->support_id;
            $tobj = new Model('supports');
            $arr['id'] = $id;
            $arr[$request->action] = 1;
            $support = $tobj->filter_index($arr);
            if (count($support) > 0) {
                $tobj->update($id, [$request->action => 0]);
                $res['msg'] = 'success';
                $res['data'] = "support removed from $request->action";
            } else {
                $tobj->update($id, [$request->action => 1]);
                $res['msg'] = 'success';
                $res['data'] = "support marked as $request->action";
            }
            echo json_encode($res);
            exit;
        } else {
            $res['msg'] = 'Something went wrong';
            $res['data'] = null;
            echo json_encode($res);
            exit;
        }
    }
    // Edit page
    public function edit($req = null)
    {
        $req = obj($req);
        $context = (object) array(
            'page' => 'supports/edit.php',
            'data' => (object) array(
                'req' => obj($req),
                'support_detail' => $this->support_detail(id: $req->id, content_group: $req->cg)
            )
        );
        $this->render_main($context);
    }
    // Update
    public function update($req = null)
    {
        $req = obj($req);
        $support_exists = (new Model('supports'))->exists(['id' => $req->id, 'content_group' => $req->cg]);
        if ($support_exists == false) {
            $_SESSION['msg'][] = "Object not found";
            echo js_alert(msg_ssn("msg", true));
            exit;
        }
        $request = null;
        $data = null;
        $data = $_POST;
        $data['id'] = $req->id;
        $rules = [
            'id' => 'required|integer',
            'email' => 'required|email',
            'name' => 'required|string',
            'message' => 'required|string'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            exit;
        }
        $request = obj($data);
        $support_exists = (new Model('supports'))->exists(['id' => $request->id]);
        if (!$support_exists) {
            $_SESSION['msg'][] = 'support not availble';
            echo js_alert(msg_ssn("msg", true));
            exit;
        }
        if (isset($request->email)) {
            $arr = null;
            $arr['content_group'] = $req->cg;
            $arr['email'] = $request->email;
            $arr['name'] = sanitize_remove_tags($request->name);
            $arr['message'] = sanitize_remove_tags($request->message);
            $arr['updated_at'] = date('Y-m-d H:i:s');
            try {
                (new Model('supports'))->update($request->id, $arr);
                $_SESSION['msg'][] = "Updated";
                echo js_alert(msg_ssn(return: true));
                echo go_to(route('supportEdit', ['cg' => $req->cg, 'id' => $request->id]));
                exit;
            } catch (PDOException $e) {
                echo js_alert('support not updated');
                exit;
            }
        }
    }
    public function move_to_trash($req = null)
    {
        $req = obj($req);
        $support_exists = (new Model('supports'))->exists(['id' => $req->id, 'content_group' => $req->cg]);
        if ($support_exists == false) {
            $_SESSION['msg'][] = "Object not found";
            echo js_alert(msg_ssn("msg", true));
            echo go_to(route('supportList', ['cg' => $req->cg]));
            exit;
        }
        $data = null;
        $data['id'] = $req->id;
        $rules = [
            'id' => 'required|integer'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            echo go_to(route('supportList', ['cg' => $req->cg]));
            exit;
        }
        try {
            (new Model('supports'))->update($req->id, array('is_active' => 0));
            echo js_alert('support moved to trash');
            echo go_to(route('supportList', ['cg' => $req->cg]));
            exit;
        } catch (PDOException $e) {
            echo js_alert('support not moved to trash');
            exit;
        }
    }
    public function restore($req = null)
    {
        $req = obj($req);
        $support_exists = (new Model('supports'))->exists(['id' => $req->id, 'content_group' => $req->cg]);
        if ($support_exists == false) {
            $_SESSION['msg'][] = "Object not found";
            echo js_alert(msg_ssn("msg", true));
            echo go_to(route('supportTrashList', ['cg' => $req->cg]));
            exit;
        }
        // $support = obj(getData(table: 'supports', id: $req->id));
        $data = null;
        $data['id'] = $req->id;
        $rules = [
            'id' => 'required|integer'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            echo go_to(route('supportTrashList', ['cg' => $req->cg]));
            exit;
        }
        try {
            (new Model('supports'))->update($req->id, array('is_active' => 1));
            echo js_alert('support restored');
            echo go_to(route('supportTrashList', ['cg' => $req->cg]));
            exit;
        } catch (PDOException $e) {
            echo js_alert('support can not be restored');
            exit;
        }
    }
    public function delete_trash($req = null)
    {
        $req = obj($req);
        $support_exists = (new Model('supports'))->exists(['id' => $req->id, 'content_group' => $req->cg]);
        if ($support_exists == false) {
            $_SESSION['msg'][] = "Object not found";
            echo js_alert(msg_ssn("msg", true));
            echo go_to(route('supportTrashList', ['cg' => $req->cg]));
            exit;
        }
        // $support = obj(getData(table: 'supports', id: $req->id));
        $data = null;
        $data['id'] = $req->id;
        $rules = [
            'id' => 'required|integer'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            echo go_to(route('supportTrashList', ['cg' => $req->cg]));
            exit;
        }
        try {
            $support_exists = (new Model('supports'))->exists(['id' => $req->id, 'is_active' => 0, 'content_group' => $req->cg]);
            if ($support_exists) {
                if ((new Model('supports'))->destroy($req->id)) {
                    echo js_alert('support deleted permanatly');
                    echo go_to(route('supportTrashList', ['cg' => $req->cg]));
                    exit;
                }
            }
            echo js_alert('support does not exist');
            echo go_to(route('supportTrashList', ['cg' => $req->cg]));
            exit;
        } catch (PDOException $e) {
            echo js_alert('support not deleted');
            exit;
        }
    }
    public function support_search_list($content_group = 'open', $keyword = "", $ord = "DESC", $limit = 5, $active = 1)
    {
        $cntobj = new Dbobjects;
        $sql = "SELECT supports.id, supports.user_id, supports.unique_id, supports.content_id, supports.content_group, supports.is_active, supports.is_approved, supports.created_at, supports.message, pk_user.first_name as name, pk_user.last_name, pk_user.isd_code, pk_user.mobile, pk_user.email
        FROM supports 
        LEFT JOIN pk_user ON COALESCE(supports.user_id, 0) = COALESCE(pk_user.id, 0)
        WHERE supports.is_active = $active 
        AND supports.content_group = '$content_group' 
        AND (
            supports.message LIKE '%$keyword%' 
             OR supports.unique_id LIKE '%$keyword%' 
             OR pk_user.first_name LIKE '%$keyword%' 
             OR pk_user.last_name LIKE '%$keyword%' 
             OR pk_user.email LIKE '%$keyword%')
        ORDER BY supports.id $ord 
        LIMIT $limit";
        return $cntobj->show($sql);
    }
    public function support_list($content_group = "open", $ord = "DESC", $limit = 5, $active = 1)
    {
        $cntobj = new Dbobjects;
        $sql = "SELECT supports.id, supports.user_id, supports.unique_id, supports.content_id, supports.content_group, supports.is_active, supports.is_approved, supports.created_at, supports.message, pk_user.first_name as name, pk_user.last_name, pk_user.isd_code, pk_user.mobile, pk_user.email
        FROM supports 
        LEFT JOIN pk_user ON COALESCE(supports.user_id, 0) = COALESCE(pk_user.id, 0)
        WHERE supports.is_active = $active AND supports.content_group = '$content_group' 
        ORDER BY supports.id $ord 
        LIMIT $limit
        ";
        return $cntobj->show($sql);
    }
    // support detail
    public function support_detail($id, $content_group = 'open')
    {
        $cntobj = new Dbobjects;
        $sql = "SELECT supports.id, supports.user_id, supports.unique_id, supports.content_id, supports.content_group, supports.is_active, supports.is_approved, supports.created_at, supports.message, pk_user.first_name as name, pk_user.isd_code, pk_user.mobile, pk_user.email
        FROM supports 
        LEFT JOIN pk_user ON COALESCE(supports.user_id, 0) = COALESCE(pk_user.id, 0)
        WHERE supports.id = '$id' AND supports.content_group = '$content_group'
        ";
        return $cntobj->showOne($sql);
    }
    public function render_main($context = null)
    {
        import("apps/admin/layouts/admin-main.php", $context);
    }
}
