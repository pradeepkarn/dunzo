<?php
class Game_home_ctrl extends  Main_ctrl
{
    public function index($req = null)
    {
        $req = obj($req);

        $data_limit = FRONT_ROW_LIMIT;
        $row_limit = "0,$data_limit";
        $cp = 0;
        if (isset($req->page) && intval($req->page)) {
            $cp = $req->page;
            $load_page = (abs($req->page) - 1) * $data_limit;
            $row_limit = "$load_page,$data_limit";
        }
        $total_products = $this->game_list(ord: "DESC", limit: 100, active: 1);
        $to = count($total_products);
        if ($to %  $data_limit == 0) {
            $to = $to / $data_limit;
        } else {
            $to = floor($to / $data_limit) + 1;
        }

        $cat_list = $this->cat_list($ord = "DESC", $limit = $row_limit, $active = 1);
        $game_list = $this->game_list($ord = "DESC", $limit = $row_limit, $active = 1);

        $GLOBALS['meta_seo'] = (object) array('title' => 'Home', 'description' => 'Welcome to our Gift shop', 'keywords' => 'gifts,shop,wedding,birthday');
        $context = (object) array(
            'page' => 'home.php',
            'data' => (object) array(
                'req' => obj($req),
                'cat_list' => $cat_list,
                'game_list' => $game_list,
                'current_page' => $cp,
                'total_object' => $to,
                'about' => $this->about_content(),
                "hero" => $this->homepage_slider($ord = "DESC", $catid = 354, $limit = 20, $active = 1)
            )
        );
        if (isset($_COOKIE['remember_token'])) {
            $acc = new Account;
            $acc->loginWithCookie($_COOKIE['remember_token']);
        }
        $this->render_layout($context);
    }
    public function homepage_slider($ord = "DESC", $catid = 362, $limit = 10, $active = 1)
    {
        $cntobj = new Dbobjects;
        $cntobj->tableName = 'content';
        return $cntobj->filter(array('content_group' => 'slider', 'parent_id' => $catid, 'is_active' => $active), $ord, $limit);
    }
    public function game_list($ord = "DESC", $limit = 1, $active = 1)
    {
        $cntobj = new Dbobjects;
        $cats = $cntobj->show("select * from content where content_group='product_category'");
        $now = date('Y-m-d H:i:s');
        $allgames = [];
        foreach ($cats as $key => $ct) {
            $ct = obj($ct);
            // $sql = "SELECT * FROM `content` WHERE content_group='game' and parent_id='$ct->id' AND '$now' BETWEEN `opens_at` AND `closes_at` and is_active=1 and is_sold=0 order by id $ord limit $limit;";
            $sql = "SELECT id,link FROM `content` WHERE content_group='game' and parent_id='$ct->id' and is_active=1 and is_sold=0 order by RAND() limit $limit;";
            $game = $cntobj->showOne($sql);
            if ($game) {
                // Assuming $gm->opens_at, $gm->closes_at, and $now contain opening, closing, and current times respectively
                $opensAt = strtotime($ct->opens_at); // Convert opening time to a timestamp
                $closesAt = strtotime($ct->closes_at); // Convert closing time to a timestamp
                $currentTime = strtotime($now); // Convert current time to a timestamp
                if ($currentTime >= $opensAt && $currentTime <= $closesAt) {
                    $is_closed = false;
                } else {
                    $is_closed = true;
                }
                $gameNew = arr($ct);
                $gameNew['link_id'] = $game['id'];
                $gameNew['link'] = $game['link'];
                // $game['closes_at'] = $ct->closes_at;
                // $game['banner'] = $ct->banner;
                $gameNew['is_closed'] = $is_closed;
                $allgames[] = $gameNew;
            }
        }
        return $allgames;
    }
    public function game_list_by_catid($catid, $ord = "DESC", $limit = 1, $active = 1)
    {
        $cntobj = new Dbobjects;
        $cntobj->tableName = 'content';
        return $cntobj->filter(array('content_group' => 'game', 'parent_id' => $catid, 'is_active' => $active), $ord, $limit);
    }
    public function cat_list($ord = "DESC", $limit = 1, $active = 1)
    {
        $cntobj = new Dbobjects;
        $cntobj->tableName = 'content';
        return $cntobj->filter(array('content_group' => 'product_category', 'is_active' => $active), $ord, $limit);
    }
    function fetch_games_by_catid($req = null)
    {
        $req = obj($req);
        $data = $_POST;
        $req->post = obj($_POST);
        $rules = [
            'cat_id' => 'required|integer'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn(return: true));
            return false;
        }
        $game_list = $this->game_list_by_catid($catid = $req->post->cat_id, $ord = "DESC", $limit = 100, $active = 1);
        if (count($game_list) == 0) {
            echo "<h3 class='text-center'>No games</h3>";
            return false;
        }
        $cat = (object)getData('content', $req->post->cat_id);
        $context = (object) array(
            'page' => 'home.php',
            'data' => (object) array(
                'req' => obj($req),
                'game_list' => $game_list,
                'cat_name' => $cat->title,
                'cat_details' => pk_excerpt($cat->content, 100),
            )
        );
        echo render_template("packages/top-picks.php", $context);
        return true;
    }

    function about_content()
    {
        $db = new Model('content');
        $abt = $db->filter_index(['slug' => 'about', 'content_group' => 'page', 'is_active' => 1]);
        if (count($abt) > 0) {
            return $abt[0];
        }
        return null;
    }
}
