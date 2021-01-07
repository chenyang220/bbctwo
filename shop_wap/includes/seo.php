<?php 
// seo 匹配，由URL匹配shop的web_config表中有config_key
$seo_match = [
    //首页
    '/' => [
        'title' => 'title',
        'keyword' => 'keyword',
        'description' => 'description',
    ],
    //团购列表
    'group_buy_index' => [
        'title' => 'tg_title',
        'keyword' => 'tg_keyword',
        'description' => 'tg_description',
    ],
    //积分
    'integral' => [
        'title' => 'point_title',
        'keyword' => 'point_keyword',
        'description' => 'point_description',
    ],
//    //积分内容
//    'integral' => [
//        'title' => 'point_title_content',
//        'keyword' => 'point_keyword_content',
//        'description' => 'point_description_content',
//    ],
    //文章
    'information_news_list' => [
        'title' => 'article_title',
        'keyword' => 'article_keyword',
        'description' => 'article_description',
    ],
    //文章内容
    'informationnews_details' => [
        'title' => 'article_title_content',
        'keyword' => 'article_keyword_content',
        'description' => 'article_description_content',
    ],
    //店铺
    'store-list' => [
        'title' => 'shop_title',
        'keyword' => 'shop_keyword',
        'description' => 'shop_description',
    ],
    //商品
    'product_list' => [
        'title' => 'product_title',
        'keyword' => 'product_keyword',
        'description' => 'product_description',
    ],
    //商品
    'product_detail' => [
        'title' => 'product_title',
        'keyword' => 'product_keyword',
        'description' => 'product_description',
    ],
    //商品分类
    'product_first_categroy' => [
        'title' => 'category_title',
        'keyword' => 'category_keyword',
        'description' => 'category_description',
        //'name'=>'爱拼团',
    ],
];