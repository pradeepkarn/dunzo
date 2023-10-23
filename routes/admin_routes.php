<?php
$admin_routes = [
    '/admin' => 'Admin_ctrl@index@name.adminhome',
    // posts
    '/admin/post/create' => 'Post_ctrl@create@name.postCreate',
    '/admin/post/create/save-by-ajax' => 'Post_ctrl@save@name.postStoreAjax',
    '/admin/post/list' => 'Post_ctrl@list@name.postList',
    '/admin/post/trash-list' => 'Post_ctrl@trash_list@name.postTrashList',
    '/admin/post/edit/{id}' => 'Post_ctrl@edit@name.postEdit',
    '/admin/post/trash/{id}' => 'Post_ctrl@move_to_trash@name.postTrash',
    '/admin/post/restore/{id}' => 'Post_ctrl@restore@name.postRestore',
    '/admin/post/delete/{id}' => 'Post_ctrl@delete_trash@name.postDelete',
    '/admin/post/edit/{id}/save-by-ajax' => 'Post_ctrl@update@name.postUpdateAjax',
    '/admin/post/toggle-marked-post' => 'Post_ctrl@toggle_trending@name.postToggleMarked',
    // pages
    '/admin/page/create' => 'Page_ctrl@create@name.pageCreate',
    '/admin/page/create/save-by-ajax' => 'Page_ctrl@save@name.pageStoreAjax',
    '/admin/page/list' => 'Page_ctrl@list@name.pageList',
    '/admin/page/trash-list' => 'Page_ctrl@trash_list@name.pageTrashList',
    '/admin/page/edit/{id}' => 'Page_ctrl@edit@name.pageEdit',
    '/admin/page/trash/{id}' => 'Page_ctrl@move_to_trash@name.pageTrash',
    '/admin/page/restore/{id}' => 'Page_ctrl@restore@name.pageRestore',
    '/admin/page/delete/{id}' => 'Page_ctrl@delete_trash@name.pageDelete',
    '/admin/page/edit/{id}/save-by-ajax' => 'Page_ctrl@update@name.pageUpdateAjax',
    '/admin/page/toggle-marked-page' => 'Page_ctrl@toggle_trending@name.pageToggleMarked',
    // products
    '/admin/product/create' => 'Product_ctrl@create@name.productCreate',
    '/admin/product/create/save-by-ajax' => 'Product_ctrl@save@name.productStoreAjax',
    '/admin/product/list' => 'Product_ctrl@list@name.productList',
    '/admin/product/trash-list' => 'Product_ctrl@trash_list@name.productTrashList',
    '/admin/product/edit/{id}' => 'Product_ctrl@edit@name.productEdit',
    '/admin/product/trash/{id}' => 'Product_ctrl@move_to_trash@name.productTrash',
    '/admin/product/restore/{id}' => 'Product_ctrl@restore@name.productRestore',
    '/admin/product/delete/{id}' => 'Product_ctrl@delete_trash@name.productDelete',
    '/admin/product/edit/{id}/save-by-ajax' => 'Product_ctrl@update@name.productUpdateAjax',
    '/admin/product/toggle-marked-page' => 'Product_ctrl@toggle_trending@name.productToggleMarked',
    // 
    '/admin/package/create' => 'Package_ctrl@create@name.packageCreate',
    '/admin/package/create/save-by-ajax' => 'Package_ctrl@save@name.packageStoreAjax',
    '/admin/package/list' => 'Package_ctrl@list@name.packageList',
    '/admin/package/trash-list' => 'Package_ctrl@trash_list@name.packageTrashList',
    '/admin/package/edit/{id}' => 'Package_ctrl@edit@name.packageEdit',
    '/admin/package/delete-more-img-ajax' => 'Package_ctrl@delete_more_img@name.packageDeleteMoreImgAjax',
    '/admin/package/trash/{id}' => 'Package_ctrl@move_to_trash@name.packageTrash',
    '/admin/package/restore/{id}' => 'Package_ctrl@restore@name.packageRestore',
    '/admin/package/delete/{id}' => 'Package_ctrl@delete_trash@name.packageDelete',
    '/admin/package/edit/{id}/save-by-ajax' => 'Package_ctrl@update@name.packageUpdateAjax',
    '/admin/package/toggle-marked-page' => 'Package_ctrl@toggle_trending@name.packageToggleMarked',
    // Sliders
    '/admin/slider/create' => 'Slider_ctrl@create@name.sliderCreate',
    '/admin/slider/create/save-by-ajax' => 'Slider_ctrl@save@name.sliderStoreAjax',
    '/admin/slider/list' => 'Slider_ctrl@list@name.sliderList',
    '/admin/slider/trash-list' => 'Slider_ctrl@trash_list@name.sliderTrashList',
    '/admin/slider/edit/{id}' => 'Slider_ctrl@edit@name.sliderEdit',
    '/admin/slider/trash/{id}' => 'Slider_ctrl@move_to_trash@name.sliderTrash',
    '/admin/slider/restore/{id}' => 'Slider_ctrl@restore@name.sliderRestore',
    '/admin/slider/delete/{id}' => 'Slider_ctrl@delete_trash@name.sliderDelete',
    '/admin/slider/edit/{id}/save-by-ajax' => 'Slider_ctrl@update@name.sliderUpdateAjax',
    '/admin/slider/toggle-marked-page' => 'Slider_ctrl@toggle_trending@name.sliderToggleMarked',
    // post category 
    '/admin/post-category/create' => 'Post_category_ctrl@create@name.postCatCreate',
    '/admin/post-category/create/save-by-ajax' => 'Post_category_ctrl@save@name.postCatStoreAjax',
    '/admin/post-category/list' => 'Post_category_ctrl@list@name.postCatList',
    '/admin/post-category/trash-list' => 'Post_category_ctrl@trash_list@name.postCatTrashList',
    '/admin/post-category/edit/{id}' => 'Post_category_ctrl@edit@name.postCatEdit',
    '/admin/post-category/trash/{id}' => 'Post_category_ctrl@move_to_trash@name.postCatTrash',
    '/admin/post-category/restore/{id}' => 'Post_category_ctrl@restore@name.postCatRestore',
    '/admin/post-category/delete/{id}' => 'Post_category_ctrl@delete_trash@name.postCatDelete',
    '/admin/post-category/edit/{id}/save-by-ajax' => 'Post_category_ctrl@update@name.postCatUpdateAjax',
    // Product category 
    '/admin/product-category/create' => 'Product_category_admin_ctrl@create@name.productCatCreate',
    '/admin/product-category/create/save-by-ajax' => 'Product_category_admin_ctrl@save@name.productCatStoreAjax',
    '/admin/product-category/list' => 'Product_category_admin_ctrl@list@name.productCatList',
    '/admin/product-category/trash-list' => 'Product_category_admin_ctrl@trash_list@name.productCatTrashList',
    '/admin/product-category/edit/{id}' => 'Product_category_admin_ctrl@edit@name.productCatEdit',
    '/admin/product-category/trash/{id}' => 'Product_category_admin_ctrl@move_to_trash@name.productCatTrash',
    '/admin/product-category/restore/{id}' => 'Product_category_admin_ctrl@restore@name.productCatRestore',
    '/admin/product-category/delete/{id}' => 'Product_category_admin_ctrl@delete_trash@name.productCatDelete',
    '/admin/product-category/edit/{id}/save-by-ajax' => 'Product_category_admin_ctrl@update@name.productCatUpdateAjax',
    // Accounts
    '/admin/account/{ug}/create' => 'Admin_user_ctrl@create@name.userCreate',
    '/admin/account/{ug}/create/save-by-ajax' => 'Admin_user_ctrl@save@name.userStoreAjax',
    '/admin/account/{ug}/list' => 'Admin_user_ctrl@list@name.userList',
    '/admin/account/{ug}/trash-list' => 'Admin_user_ctrl@trash_list@name.userTrashList',
    '/admin/account/{ug}/edit/{id}' => 'Admin_user_ctrl@edit@name.userEdit',
    '/admin/account/{ug}/trash/{id}' => 'Admin_user_ctrl@move_to_trash@name.userTrash',
    '/admin/account/{ug}/restore/{id}' => 'Admin_user_ctrl@restore@name.userRestore',
    '/admin/account/{ug}/delete/{id}' => 'Admin_user_ctrl@delete_trash@name.userDelete',
    '/admin/account/{ug}/edit/{id}/save-by-ajax' => 'Admin_user_ctrl@update@name.userUpdateAjax',
    // fuels
    '/admin/orders/{fg}/list' => 'Orders_api_ctrl@list@name.orderListApi',
    // fuels
    '/admin/fuel/{fg}/create' => 'Admin_fuel_ctrl@create@name.fuelCreate',
    '/admin/fuel/{fg}/create/save-by-ajax' => 'Admin_fuel_ctrl@save@name.fuelStoreAjax',
    '/admin/fuel/{fg}/list' => 'Admin_fuel_ctrl@list@name.fuelList',
    '/admin/fuel/{fg}/trash-list' => 'Admin_fuel_ctrl@trash_list@name.fuelTrashList',
    '/admin/fuel/{fg}/edit/{id}' => 'Admin_fuel_ctrl@edit@name.fuelEdit',
    '/admin/fuel/{fg}/trash/{id}' => 'Admin_fuel_ctrl@move_to_trash@name.fuelTrash',
    '/admin/fuel/{fg}/restore/{id}' => 'Admin_fuel_ctrl@restore@name.fuelRestore',
    '/admin/fuel/{fg}/delete/{id}' => 'Admin_fuel_ctrl@delete_trash@name.fuelDelete',
    '/admin/fuel/{fg}/edit/{id}/save-by-ajax' => 'Admin_fuel_ctrl@update@name.fuelUpdateAjax',
    // Comments
    '/admin/comments/{cg}/list' => 'Comment_admin_ctrl@list@name.commentList',
    '/admin/comments/{cg}/trash-list' => 'Comment_admin_ctrl@trash_list@name.commentTrashList',
    '/admin/comments/{cg}/trash/{id}' => 'Comment_admin_ctrl@move_to_trash@name.commentTrash',
    '/admin/comments/{cg}/restore/{id}' => 'Comment_admin_ctrl@restore@name.commentRestore',
    '/admin/comments/{cg}/delete/{id}' => 'Comment_admin_ctrl@delete_trash@name.commentDelete',
    '/admin/comments/{cg}/edit/{id}' => 'Comment_admin_ctrl@edit@name.commentEdit',
    '/admin/comments/{cg}/edit/{id}/save-by-ajax' => 'Comment_admin_ctrl@update@name.commentUpdateAjax',
    '/admin/comments/{cg}/toggle-marked-comment' => 'Comment_admin_ctrl@toggle_approve@name.commentToggleMarked',
    '/admin/comments/{cg}/toggle-spam-comment' => 'Comment_admin_ctrl@toggle_spam@name.commentToggleSpam',
    // reviews 
    '/admin/reviews/{rg}/add-new' => 'Review_ctrl@add_review_ajax@name.addReviewAjax',
    '/admin/reviews/{rg}/delete' => 'Review_ctrl@delete_review_ajax@name.deleteReviewAjax',

];