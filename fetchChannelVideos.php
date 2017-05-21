<?php
/**
 * Created by PhpStorm.
 * User: LJS
 * Date: 2017/5/19
 * Time: 23:25
 */
// 引入youtube类
include 'youtube.class.php';
$youtube = new youtube();

// 从数据库获取is_get_detail为0的数据
$ytbRes = $youtube->db->select('ytb_channels', ['id','user_url'], ['is_get_video'=>0]);
foreach($ytbRes as $k => $v){
    $youtube->init($v['user_url']);
    $videoLinks = $youtube->get_video_link();
    $videoDetail = [];
    $videotype = [];
    foreach($videoLinks as $u_k){
        $detail = [];
        $detailUrl = 'https://www.youtube.com'.$u_k;
        $youtube->init($detailUrl);
        $detail['channel_id'] = $v['id'];
        $detail['title'] = $youtube->get_video_title();
        $detail['upload_time'] = $youtube->get_video_upload();
        $detail['view'] = $youtube->get_video_view();
        $detail['type'] = $youtube->get_video_type();
        $videotype[$detail['type']]++;
        $res = $youtube->db->insert("ytb_video", $detail);
    }
    arsort($videotype);
    reset($videotype);
    $first_key = key($videotype);
    $youtube->db->update("ytb_channels", ['type'=>$first_key,'is_get_video'=>1],['id'=>$v['id']]);
}