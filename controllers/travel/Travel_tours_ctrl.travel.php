<?php
class Travel_tours_ctrl extends Main_ctrl
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
        $total_products = $this->package_list(ord: "DESC", limit: 10000, active: 1);
        $to = count($total_products);
        if ($to %  $data_limit == 0) {
            $to = $to / $data_limit;
        } else {
            $to = floor($to / $data_limit) + 1;
        }

        $cat_list = $this->cat_list($ord = "DESC", $limit = $row_limit, $active = 1);
        $package_list = $this->package_list($ord = "DESC", $limit = $row_limit, $active = 1);

        $GLOBALS['meta_seo'] = (object) array('title' => 'Home', 'description' => 'Welcome to our Gift shop', 'keywords' => 'gifts,shop,wedding,birthday');
        $context = (object) array(
            'page' => 'tours.php',
            'data' => (object) array(
                'req' => obj($req),
                'cat_list' => $cat_list,
                'package_list' => $package_list,
                'current_page' => $cp,
                'total_object' => $to,
                'about' => $this->about_content(),
                "hero" => null
            )
        );
        if (isset($_COOKIE['remember_token'])) {
            $acc = new Account;
            $acc->loginWithCookie($_COOKIE['remember_token']);
        }
        $this->render_layout($context);
    }
    public function package_list($ord = "DESC", $limit = 1, $active = 1)
    {
        $cntobj = new Dbobjects;
        $cntobj->tableName = 'content';
        return $cntobj->filter(array('content_group' => 'package', 'is_active' => $active), $ord, $limit);
    }
    public function cat_list($ord = "DESC", $limit = 1, $active = 1)
    {
        $cntobj = new Dbobjects;
        $cntobj->tableName = 'content';
        return $cntobj->filter(array('content_group' => 'product_category', 'is_active' => $active), $ord, $limit);
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
