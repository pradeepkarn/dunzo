<?php

$user_routes = [
    // '/dashboard' => 'DashboardController@index@name.dashboard',
    "/search" => "Search_users_ctrl@index@name.search",
    // "/dashboard/profile" => 'Profile_ctrl@index@name.userProfile', // currently disabled for testing and full confirmation of no bug
    "/dashboard/news-feeds" => 'News_feeds_ctrl@index@name.newsFeeds',
    "/dashboard/profile-edit" => 'Profile_ctrl@edit@name.userProfileEdit',
    "/dashboard/upload-user-cover-image-ajax" => 'Profile_ctrl@upload_cover_image_ajax@name.uploadUserCoverImageAjax',
    "/dashboard/upload-user-profile-image-ajax" => 'Profile_ctrl@upload_profile_image_ajax@name.uploadUserProfileImageAjax',
    "/dashboard/upload-user-profile-ajax" => 'Profile_ctrl@update_my_profile_ajax@name.updateMyProfileAjax',
    "/dashboard/send-request-ajax" => 'Profile_ctrl@send_request_ajax@name.sendRequestAjax',
    "/dashboard/like-unlike-ajax" => 'Profile_ctrl@like_unlike_ajax@name.likeUnlikeProfileAjax',
];