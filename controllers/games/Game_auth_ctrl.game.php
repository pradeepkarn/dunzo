<?php

use Paynow\Payments\Paynow;
use PHPMailer\PHPMailer\PHPMailer;
class Game_auth_ctrl extends Main_ctrl
{
    function pay($db,$paymentid,$item,$amount,$mobile="0772222222",$email="virgil@dealcity.co.ke")
    {
        $paynow = new Paynow(
            INTEGRATION_ID,
            INTEGRATION_KEY,
            BASEURI,
            // The return url can be set at later stages. You might want to do this if you want to pass data to the return url (like the reference of the transaction)
            BASEURI
        );
        $payment = $paynow->createPayment("INV".$paymentid, $email);
        $payment->add($item, $amount);
        $response = $paynow->sendMobile($payment, strval($mobile), 'ecocash');
        $datas = $response->data();
        $this->save_json_file($datas);
        $this->save_json_file($response);
        if($response->success()) {
            $db->tableName='payment';
            $db->pk($paymentid);
            $parr['pollurl'] = $response->pollUrl();
            $parr['instructions'] = $response->instructions();
            $db->insertData = $parr;
            $db->update();
            return true;
        }else{
            return false;
        }
    }
    function save_json_file($response)
    {
        $filename = "payref/" . uniqid(time() . '_json') . '.json';
        $content = json_encode($response);
        $file = fopen($filename, 'w');
        if ($file) {
            // Write the content to the file
            fwrite($file, $content);
            // Close the file
            fclose($file);
        }
    }
    function send_otp($req = null)
    {
        $req = obj($_POST);
        $_SESSION['msg'] = null;
        // $_SESSION['registration_otp'] = null;
        $rules = [
            'email' => 'required|email',
        ];
        $pass = validateData(data: $_POST, rules: $rules);
        if (!$pass) {
            $data['msg'] = msg_ssn(return: true, lnbrk: "<br>");
            $data['success'] = false;
            $data['data'] = null;
            echo json_encode($data);
            exit;
        }
        if (!email_has_valid_dns($req->email)) {
            msg_set("Invalid email, we can not send email here");
            $data['msg'] = msg_ssn(return: true, lnbrk: "<br>");
            $data['success'] = false;
            $data['data'] = null;
            echo json_encode($data);
            exit;
        }
        $obj = new stdClass;
        $obj->col = 'email';
        $obj->val = $req->email;
        $emailcheck = $this->check_dup($obj);
        if ($emailcheck) {
            msg_set("This email has already been registered");
            $data['msg'] = msg_ssn(return: true, lnbrk: "<br>");
            $data['success'] = false;
            $data['data'] = null;
            echo json_encode($data);
            exit;
        } else {
            $otp = random_int(100000, 999999);
            $mail = php_mailer(new PHPMailer());
            $mail->setFrom(email, SITE_NAME . "OTP");
            $mail->isHTML(true);
            $mail->Subject = 'One Time Password';
            $mail->Body = "<b>$otp</b>";
            $mail->addAddress($req->email, "$req->email");
            if (isset($_SESSION['otp_sent_count'])) {
                if ($_SESSION['otp_sent_count'] > 6) {
                    msg_set("You have sent OTP so many times, please check your email and try after an hour");
                    $data['msg'] = msg_ssn(return: true, lnbrk: "<br>");
                    $data['success'] = false;
                    $data['data'] = null;
                    echo json_encode($data);
                    exit;
                }
            }
            if ($mail->send()) {
                if (!isset($_SESSION['otp_sent_count'])) {
                    $_SESSION['otp_sent_count'] = 1;
                } else {
                    $_SESSION['otp_sent_count'] += 1;
                }
                msg_set("An OTP has been sent to $req->email.");
                $_SESSION['registration_otp'] = $otp;
                $data['msg'] = msg_ssn(return: true, lnbrk: "<br>");
                $data['success'] = true;
                $data['data'] = null;
                echo json_encode($data);
                exit;
            } else {
                // $_SESSION['registration_otp'] = null;
                msg_set("Email sending error");
                $data['msg'] = msg_ssn(return: true, lnbrk: "<br>");
                $data['success'] = false;
                $data['data'] = null;
                echo json_encode($data);
                exit;
            }
        }
    }
    public function register()
    {
        $data = null;
        $data = $_POST;
        $rules = [
            'email' => 'required|email',
            'username' => 'required|string|min:4|max:12',
            // 'otp' => 'required|integer|min:4|max:6',
            // 'password' => 'required|string|min:6',
            // 'confirm_password' => 'required|string|min:6',
            'terms_and_conditions_and_privacy_policy' => 'required',
        ];
        $pass = validateData(data: $_POST, rules: $rules);
        if ($pass) {
            $data = obj($data);
            if (!isset($data->password) || !isset($data->confirm_password)) {
                $_SESSION['msg'][] = 'Password and confirm password are required';
                echo js_alert(msg_ssn(return: true));
                exit;
            }
            if (empty($data->password) || empty($data->confirm_password)) {
                $_SESSION['msg'][] = 'Password and confirm password must not be empty';
                echo js_alert(msg_ssn(return: true));
                exit;
            }
            if ($data->password != $data->confirm_password) {
                $_SESSION['msg'][] = 'Password and confirm password must be same';
                msg_ssn();
                exit;
            }
            if (!email_has_valid_dns($data->email)) {
                $_SESSION['msg'][] = 'Please provide a working email';
                echo js_alert(msg_ssn(return: true));
                exit;
            }
            if (strtolower($data->username) != generate_clean_username($data->username)) {
                $_SESSION['msg'][] = 'Only alpha-numeric is allowed in lower characters';
                echo js_alert(msg_ssn(return: true));
                exit;
            }
            
            $username = generate_clean_username($data->username);
            $obj = new stdClass;
            $obj->col = 'email';
            $obj->val = $data->email;
            $emailcheck = $this->check_dup($obj);
            $obj->col = 'username';
            $obj->val = $username;
            $usernamecheck = $this->check_dup($obj);
            if ($emailcheck) {
                $_SESSION['msg'][] = 'This email is already taken';
                msg_ssn();
                exit;
            }
            if ($usernamecheck) {
                $_SESSION['msg'][] = 'This username is already taken';
                msg_ssn();
                exit;
            }
            // if (!isset($_SESSION['registration_otp'])) {
            //     $_SESSION['msg'][] = 'Otp not created yet';
            //     msg_ssn();
            //     exit;
            // }
            // if ($_SESSION['registration_otp'] != $data->otp) {
            //     $_SESSION['msg'][] = 'Invalid otp, please try again';
            //     msg_ssn();
            //     exit;
            // }
            $username = generate_username_by_email($data->email);
            $password = md5($data->password);
            $role = 'subscriber';

            try {
                $user_id = (new Model('pk_user'))->store(
                    array(
                        'email' => $data->email,
                        'username' => $username,
                        'password' => $password,
                        'role' => $role,
                    )
                );
                if (intval($user_id)) {
                    $user = $this->login($uniqcol = $data->email, $password = $data->password, $ug = 'user');
                    if ($user != false) {
                        echo RELOAD;
                        exit;
                    }
                }
            } catch (PDOException $e) {
                // echo "Error: " . $e->getMessage();
                $_SESSION['msg'][] = 'Something went wrong while saving in database';
                msg_ssn("msg");
                exit;
            }
        } else {
            msg_ssn("msg");
            exit;
        }
    }
    public function game_register()
    {
        $data = null;
        $data = $_POST;
        $rules = [
            'gameid' => 'required|integer',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string',
            'isd_code' => 'required|numeric',
            'price' => 'required|numeric',
            'mobile' => 'required|integer',
            'terms_and_conditions_and_privacy_policy' => 'required',
        ];
        $pass = validateData(data: $_POST, rules: $rules);
        if ($pass) {
            $data = obj($data);
            $status = 'initiated';
            $db = new Dbobjects;
            $pdo = $db->dbpdo();
            $pdo->beginTransaction();
            $game = $db->showOne("select id,is_sold,price,qty,link,parent_id from content where is_sold = 0 and content_group='game' and content.id = $data->gameid");
            
            if (!$game) {
                $_SESSION['msg'][] = 'Game not available';
                msg_ssn("msg");
                exit;
            }
            $game = obj($game);
            $cat = $db->showOne("select id,price from content where content_group='product_category' and content.id = '$game->parent_id'");
            if (!$cat) {
                $_SESSION['msg'][] = 'Game price available';
                msg_ssn("msg");
                exit;
            }
            $cat = obj($cat);
            $game->price = $cat->price;
            $db->tableName = 'payment';
            $db->insertData = array(
                'user_id' => $data->gameid,
                'unique_id' => uniqid('gm'),
                'isd_code' => $data->isd_code,
                'mobile' => $data->mobile,
                'first_name' => $data->first_name,
                'last_name' => $data->last_name,
                'amount' => $cat->price,
                'status' => $status,
                'payment_method' => 'paynow',
                'user_id' => USER['id'],
                'created_at' => date('Y-m-d H:i:s'),
            );
            try {
                $paymentid = $db->create();
                $db->tableName = 'customer_order';
                $db->insertData = array(
                    'item_id' => $data->gameid,
                    'payment_id' => $paymentid,
                    'qty' => 1,
                    'price' => $cat->price,
                    'status' => 'confirmed',
                    'customer_email' => $data->email,
                    'user_id' => USER['id'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'link' => $game->link,
                    'is_paid' => true,
                );
                $db->create();
                $db->insertData = null;
                $db->tableName = 'content';
                $db->insertData['is_sold'] = 1;
                $db->pk($game->id);
                $db->update();
                $_SESSION['msg'][] = 'Success';
                msg_ssn("msg");
                $_SESSION['cp'] = array(
                    'user_id'=>USER['id'],
                    'payment_id'=>$paymentid,
                    'amount'=>floatval($cat->price),
                );
                // $email="virgil@dealcity.co.ke";
                // $mobile="0772222222";
                $mobileglobalwith0 = strval("0".$data->isd_code.$data->mobile);
                $paycheck = $this->pay($db,$paymentid,"Pay2Play_{$data->gameid}",floatval($cat->price),$mobile=$mobileglobalwith0,$email=$data->email);
                if ($paycheck==true) {
                    $link = BASEURI.route("checkStatusPage",['pid'=>$paymentid]);
                    echo "<a class='btn btn-warning text-dark' href='$link'>Check Status</a>";
                    $pdo->commit();
                    exit;
                }else{
                    $pdo->rollBack();
                    $_SESSION['msg'][] = 'Failed';
                    msg_ssn("msg");
                    exit;
                }
                
            } catch (PDOException $th) {
                // echo $th;
                $pdo->rollBack();
                $_SESSION['msg'][] = 'Failed';
                msg_ssn("msg");
                exit;
            }
        } else {
            msg_ssn("msg");
            exit;
        }
    }
    function check_status_page($req=null)  {
        if (!authenticate()) {
            header("Location:/" . home);
            exit;
        }
        $req = obj($req);
        $context = (object) array(
            'page' => 'auth/paymentupdate.php',
            'data' => (object) array(
                'req' => obj($req)
            )
        );
        $this->render_layout($context);
    }
    function reset_password_page($req = null)
    {
        if (authenticate()) {
            header("Location:/" . home);
            exit;
        }
        $context = (object) array(
            'page' => 'auth/reset-password.php',
            'data' => (object) array(
                'req' => obj($req)
            )
        );
        $this->render_layout($context);
    }
    function verify_reset_token($req, $db)
    {
        $now = date('Y-m-d H:i:s');
        $reset = (object)$db->showOne("select id,token from account_reset where token = '$req->prt' and valid_up_to > '$now'");
        if (!isset($reset->token)) {
            msg_set('Link expired or invalid');
            return false;
        } else if ($reset->token != '') {
            $emailtoken = decrypt($req->prt, sitekey);
            $dbtoken = decrypt($reset->token, sitekey);
            $token_arr_email = explode("_", $emailtoken);
            $token_arr_db = explode("_", $dbtoken);
            if (isset($token_arr_email['0']) && isset($token_arr_db['0'])) {
                if ($token_arr_email['0'] === $token_arr_db['0']) {
                    return $token_arr_db['0'];
                }
            }
            msg_set('Invalid token');
            return false;
        }
        return false;
    }
    function email_temp_passwod(object $req, object $db)
    {
        $now = date('Y-m-d H:i:s');
        $reset = (object)$db->showOne("select id,token from account_reset where token = '$req->prt' and valid_up_to > '$now'");
        if (!isset($reset->token)) {
            msg_set('Link expired or invalid');
            return false;
        } else if ($reset->token != '') {
            unset($_SESSION['msg']);
            $emailtoken = decrypt($req->prt, sitekey);
            $dbtoken = decrypt($reset->token, sitekey);
            $token_arr_email = explode("_", $emailtoken);
            $token_arr_db = explode("_", $dbtoken);
            if (isset($token_arr_email['0']) && isset($token_arr_db['0'])) {

                if ($token_arr_email['0'] === $token_arr_db['0']) {
                    try {
                        $user_to_reset = (object)$db->showOne("select id,email from pk_user where email = '{$token_arr_db['0']}' and is_active = 1");
                        $pdo = $db->dbpdo();
                        $pdo->beginTransaction();
                        $db->tableName = 'pk_user';
                        $db->pk($user_to_reset->id);
                        $tempass = rand(100000, 999999);
                        $db->insertData['password'] = md5($tempass);
                        if ($db->update()) {
                            $mail = php_mailer(new PHPMailer());
                            $mail->setFrom(email, SITE_NAME . "Temporary password");
                            $mail->isHTML(true);
                            $mail->Subject = 'Temporary password';
                            $mail->Body = $tempass;
                            $mail->addAddress($user_to_reset->email, "$user_to_reset->email");
                            if ($mail->send()) {
                                msg_set("A temporary password has been sent to $user_to_reset->email, please login and change to strong password.");
                                $db->tableName = 'account_reset';
                                $db->pk($reset->id);
                                $db->delete();
                                $pdo->commit();
                                return true;
                            } else {
                                $pdo->rollBack();
                                msg_set("Email sending error");
                                return false;
                            }
                        } else {
                            $pdo->rollBack();
                            msg_set("Password not reset");
                            return false;
                        }
                    } catch (PDOException $th) {
                        $pdo->rollBack();
                        msg_set("Something went wrong");
                        return false;
                    }
                } else {
                    msg_set("Invalid token");
                    return false;
                }
            }
            return false;
        } else {
            return false;
        }
    }
    // send temp pass to email via ajax after successfull loading create password page
    function send_me_temp_password_ajax($req = null)
    {
        $req = obj($req);
        $req->prt = isset($this->post->prt) ? $this->post->prt : null;
        if (!isset($req->prt)) {
            $data['msg'] = "Token not found";
            $data['success'] = false;
            $data['data'] = null;
            echo json_encode($data);
            exit;
        }
        $db = new Dbobjects;
        $res = $this->email_temp_passwod($req, $db);
        $msg = str_replace("\n", "<br>", msg_ssn(return: true));
        $msg = str_replace("\\n", "<br>", $msg);
        if ($res === true) {
            $data['msg'] = $msg;
            $data['success'] = true;
            $data['data'] = null;
            echo json_encode($data);
            exit;
        } else {
            $data['msg'] = $msg;
            $data['success'] = false;
            $data['data'] = null;
            echo json_encode($data);
            exit;
        }
    }
    // load create password page from email link
    function create_new_password_page($req = null)
    {
        $req = obj($req);
        if (!isset($req->prt)) {
            header("Location:/" . home);
            exit;
        }
        $db = new Dbobjects;
        $email = $this->verify_reset_token($req, $db);
        $page = 'auth/create-new-password.php';
        if ($email == false) {
            $page = "auth/link-expired-to-create-new-password.php";
        }
        $context = (object) array(
            'page' => $page,
            'data' => (object) array(
                'req' => obj($req),
                'email' => maskEmailBy50Percent($email)
            )
        );
        $this->render_layout($context);
    }
    // send reste link to email via ajax
    function reset_password_ajax($req = null)
    {
        $db = new Dbobjects;
        $db->tableName = "pk_user";
        header('Content-Type: application/json');
        if (!isset($this->post->email)) {
            $data['msg'] = "Please enter registered email";
            $data['success'] = false;
            $data['data'] = null;
            echo json_encode($data);
            exit;
        }
        $email = $this->post->email;
        if (!email_has_valid_dns($this->post->email)) {
            $data['msg'] = "Your email is not a valid email so we can not send reset link";
            $data['success'] = false;
            $data['data'] = null;
            echo json_encode($data);
            exit;
        }
        $user = $db->filter(array('email' => $email));
        if (count($user) == 1) {
            $user = obj($user[0]);
            if (!user_group($user, 'user')) {
                $data['msg'] = "You can not reset your password from here";
                $data['success'] = false;
                $data['data'] = null;
                echo json_encode($data);
                exit;
            } else {
                $token = bin2hex(random_bytes(16));
                $email = $user->email;
                $token_email = $email . "_" . $token;
                $enc_token = encrypt($token_email, sitekey);
                try {
                    $db->dbpdo()->beginTransaction();
                    $db->tableName = 'account_reset';
                    $old = $db->get(['user_id' => $user->id]);
                    $db->insertData = null;
                    $db->insertData['token'] = $enc_token;
                    $db->insertData['is_active'] = 1;
                    $db->insertData['created_at'] = date('Y-m-d H:i:s');
                    $db->insertData['valid_up_to'] = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                    if ($old) {
                        $db->update();
                    } else {
                        $db->insertData['user_id'] = $user->id;
                        $db->create();
                    }
                    $mail = php_mailer(new PHPMailer());
                    $mail->setFrom(email, SITE_NAME . "Password reset");
                    $mail->isHTML(true);
                    $mail->Subject = 'Password reset link';
                    $mailObj = obj([
                        'token' => $enc_token,
                        'user' => $user
                    ]);
                    $body = render_template("emails/password_reset/password-reset-mail.php", $mailObj);
                    $mail->Body = $body;
                    $mail->addAddress($email, "$user->first_name");
                    $db->dbpdo()->commit();
                    try {
                        $mail->send();
                        $data['msg'] = "A password reset link has been sent to <b>$email</b>, please check and follow the steps.";
                        $data['success'] = true;
                        $data['data'] = null;
                        echo json_encode($data);
                        exit;
                    } catch (ErrorException $e) {
                        $data['msg'] = "Email not sent";
                        $data['success'] = false;
                        $data['data'] = null;
                        echo json_encode($data);
                        exit;
                    }
                } catch (PDOException $th) {
                    $db->dbpdo()->rollBack();
                    $data['msg'] = "DB error";
                    $data['success'] = false;
                    $data['data'] = null;
                    echo json_encode($data);
                    exit;
                }
            }
        } else {
            $data['msg'] = "Email not found";
            $data['success'] = false;
            $data['data'] = null;
            echo json_encode($data);
            exit;
        }
    }
    public function check_dup($obj)
    {
        $arr = null;
        $arr[$obj->col] = $obj->val;
        return (new Model('pk_user'))->exists($arr);
    }
    public function login($uniqcol = null, $password = null, $ug = 'user')
    {
        if ($uniqcol == null) {
            $_SESSION['msg'][] = 'Empty data is not allowed';
            msg_ssn();
            return false;
        }
        $userObj = new Model('pk_user');
        $user = $userObj->filter_index(array('username' => $uniqcol, 'password' => md5($password)));
        if (count($user) == 1) {
            $user = obj($user[0]);
            if (!user_group($user, $ug)) {
                $_SESSION['msg'][] = 'Invalid login portal';
                msg_ssn("msg");
                return false;
            }
            $_SESSION['user_id'] = $user->id;
            $this->save_in_cookie($user->id);
            $_SESSION['msg'][] = 'Logged in success via username';
            msg_ssn("msg");
            return $user;
        }
        $user = $userObj->filter_index(array('email' => $uniqcol, 'password' => md5($password)));
        if (count($user) == 1) {
            $user = obj($user[0]);
            if (!user_group($user, $ug)) {
                $_SESSION['msg'][] = 'Invalid login portal';
                msg_ssn("msg");
                return false;
            }
            $_SESSION['user_id'] = $user->id;
            $this->save_in_cookie($user->id);
            $_SESSION['msg'][] = 'Logged in success via email';
            msg_ssn("msg");
            return $user;
        }
        $user = $userObj->filter_index(array('mobile' => $uniqcol, 'password' => md5($password)));
        if (count($user) == 1) {
            $user = obj($user[0]);
            if (!user_role($user, $ug)) {
                $_SESSION['msg'][] = 'Invalid login portal';
                msg_ssn("msg");
                return false;
            }
            $_SESSION['user_id'] = $user->id;
            $this->save_in_cookie($user->id);
            $_SESSION['msg'][] = 'Logged in success via mobile';
            msg_ssn("msg");
            return $user;
        } else {
            $_SESSION['msg'][] = 'Username or password wrong';
            msg_ssn("msg");
            return false;
        }
    }

    public function save_in_cookie($userid, Dbobjects $db = null)
    {
        if ($db != null) {
            $db = $db;
        } else {
            $db = new Dbobjects;
        }
        $db->tableName = 'pk_user';
        $cookie_name = "remember_token";
        $cookie_value = bin2hex(random_bytes(32)) . "_uid_" . $userid;
        setcookie($cookie_name, $cookie_value, time() + (86400 * 30 * 12), "/"); // 86400 = 1 day
        // $db = new Model('pk_user');
        $db->pk($userid);
        $arr = null;
        $arr['remember_token'] = $cookie_value;
        $db->insertData = $arr;
        $db->update();
        $arr = null;
    }
    public function logout()
    {
        if (USER) {
            $role = USER['role'];
        } else {
            $role = false;
        }
        // Unset all session values
        $_SESSION = array();
        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 42000, '/');
        }
        // Destroy the session
        session_destroy();
        if ($role == 'superuser') {
            header("Location: /" . home . route('adminLogin'));
            exit;
        }
        header("Location: /" . home);
    }

    ############################################### pages and ajax

    public function user_login_page($req = null)
    {
        if (authenticate()) {
            header("Location: /" . home . route('home'));
            exit;
        }
        $context = (object) array(
            'page' => 'auth/user-login.php',
            'data' => (object) array(
                'req' => obj($req)
            )
        );
        $this->render_layout($context);
    }

    public function registration_page($req = null)
    {
        if (is_superuser()) {
            header("Location: /" . home . route('adminhome'));
            exit;
        }
        if (authenticate()) {
            header("Location: /" . home . route('home'));
            exit;
        }
        $context = (object) array(
            'page' => 'auth/registration.php',
            'data' => (object) array(
                'req' => obj($req)
            )
        );
        $this->render_layout($context);
    }
    public function game_registration_page($req = null)
    {
        $req = obj($req);
        $db = new Dbobjects;
        $game = $db->showOne("select id,parent_id,title,content,banner,price,is_sold,imgs from content where content_group='game' and content.id='$req->gameid'");
        if (!is_numeric($req->gameid) || !$game) {
            header('location:' . BASEURI);
            return;
        }
        $cat = $db->showOne("select id,price from content where content_group='product_category' and content.id = {$game['parent_id']}");
        if (!$cat) {
            header('location:' . BASEURI);
            return;
        }
        $cat = obj($cat);
        $game['price'] = $cat->price;
        $context = (object) array(
            'page' => 'auth/game-registration.php',
            'data' => (object) array(
                'req' => obj($req),
                'game' => obj($game),
            )
        );
        $this->render_layout($context);
    }
    public function admin_login_page($req = null)
    {
        if (is_superuser()) {
            header("Location: /" . home . route('adminhome'));
            exit;
        }
        $context = (object) array(
            'page' => 'auth/admin-login.php',
            'data' => (object) array(
                'req' => obj($req)
            )
        );
        $this->render_layout($context);
    }
    public function admin_login($req = null)
    {
        $rules = [
            'username' => 'required|string',
            'password' => 'required|string|min:6|max:20'
        ];
        $pass = validateData(data: $_POST, rules: $rules);
        if ($pass) {
            $auth = new Auth();
            $user = $auth->login(uniqcol: $_POST['username'], password: $_POST['password'], ug: "admin");
            if ($user != false) {
                echo RELOAD;

                exit;
            }
        } else {
            msg_ssn("msg");
        }
    }
    public function user_login($req = null)
    {
        $rules = [
            'username' => 'required|string',
            'password' => 'required|string|max:20'
        ];
        $pass = validateData(data: $_POST, rules: $rules);
        if ($pass) {
            $auth = new Auth();
            $user = $auth->login($_POST['username'], $_POST['password']);
            if ($user != false) {
                echo RELOAD;
                exit;
            }
        } else {
            msg_ssn("msg");
        }
    }

    public function render_main($context = null)
    {
        import("apps/view/layouts/main.php", $context);
    }
}
