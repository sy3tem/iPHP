<?php
/**
 * @package iCMS
 * @copyright 2007-2017, iDreamSoft
 * @license http://www.idreamsoft.com iDreamSoft
 * @author coolmoo <idreamsoft@qq.com>
 */
defined('iPHP') OR exit('What are you doing?');
function user_data($vars=null){
    if($vars['cookie']){
        return user::get_cookie();
    }

	$vars['uid']   OR iUI::warning('iCMS&#x3a;user&#x3a;data 标签出错! 缺少"uid"属性或"uid"值为空.');
	$uid = $vars['uid'];
	if($uid=='me'){
		$uid  = 0;
		$auth = user::get_cookie();
		$auth && $uid = user::$userid;
	}
    $user = (array)user::get($uid);
    if(isset($user['uid'])){
        $vars['data'] && $user['data']= (array)user::data($uid);
    }else{
        if($vars['data']){
            $userdata = user::data($uid);
            foreach ($user as $key => $value) {
                $user[$key] = (array)$value;
                $user[$key]['data'] = (array)$userdata[$key];
            }
        }

    }
    return $user;
}

function user_list($vars=null){
	$maxperpage = isset($vars['row'])?(int)$vars['row']:"100";
	$cache_time	= isset($vars['time'])?(int)$vars['time']:"-1";

    $where_sql	= "WHERE `status`='1'";

	isset($vars['userid'])&& $where_sql.=" AND `uid`='{$vars['userid']}'";
	isset($vars['gid'])   && $where_sql.= " AND `gid` ='{$vars['gid']}'";

	isset($vars['type'])  && $where_sql.= " AND `type` ='{$vars['type']}'";
    if(isset($vars['pid']) && !isset($vars['pids'])){
        $where_sql.= iSQL::where($vars['pid'],'pid');
    }
    if(isset($vars['pids']) && !isset($vars['pid'])){
        iMap::init('prop',iCMS_APP_USER);
        //$where_sql.= iMap::exists($vars['pid'],'`#iCMS@__user`.uid'); //map 表大的用exists
        $map_where = iMap::where($vars['pids']);
    }

	$by=$vars['by']=="ASC"?"ASC":"DESC";
    switch ($vars['orderby']) {
        case "id":		$order_sql =" ORDER BY `uid` $by";      break;
        case "article":	$order_sql =" ORDER BY `article` $by";  break;
        case "comments":$order_sql =" ORDER BY `comments` $by"; break;
        case "follow":  $order_sql =" ORDER BY `follow` $by";   break;
        case "fans":    $order_sql =" ORDER BY `fans` $by";     break;
        case "hits":    $order_sql =" ORDER BY `hits` $by";     break;
        case "hot":     $order_sql =" ORDER BY `hits` $by";     break;
        case "week":    $order_sql =" ORDER BY `hits_week` $by";break;
        case "month":   $order_sql =" ORDER BY `hits_month` $by";break;
        default:$order_sql=" ORDER BY `uid` $by";
    }
    if($map_where){
        $map_sql   = iSQL::select_map($map_where);
        $where_sql = ",({$map_sql}) map {$where_sql} AND `uid` = map.`iid`";
    }
	$offset	= 0;
	$limit  = "LIMIT {$maxperpage}";
	if($vars['page']){
		$total	= iPHP::total('sql.md5',"SELECT count(*) FROM `#iCMS@__user` {$where_sql} ");
		$multi  = iPHP::page(array('total'=>$total,'perpage'=>$maxperpage,'unit'=>iUI::lang('iCMS:page:sql'),'nowindex'=>$GLOBALS['page']));
		$offset = $multi->offset;
		$limit  = "LIMIT {$offset},{$maxperpage}";
        iPHP::assign("user_list_total",$total);
	}
    $hash = md5($where_sql.$order_sql.$limit);

    if($map_sql || $offset){
        if($vars['cache']){
			$map_cache_name = iPHP_DEVICE.'/user_map/'.$hash;
			$ids_array      = iCache::get($map_cache_name);
        }
        if(empty($ids_array)){
            $ids_array = iDB::all("SELECT `id` FROM `#iCMS@__user` {$where_sql} {$order_sql} {$limit}");
            $vars['cache'] && iCache::set($map_cache_name,$ids_array,$cache_time);
        }
        $ids       = iSQL::values($ids_array,'uid');
        $ids       = $ids?$ids:'0';
        $where_sql = "WHERE `uid` IN({$ids})";
    }
    if($vars['cache']){
		$cache_name = iPHP_DEVICE.'/user_list/'.$hash;
		$resource   = iCache::get($cache_name);
    }
	if(empty($resource)){
        $resource = iDB::all("SELECT * FROM `#iCMS@__user` {$where_sql} {$order_sql} {$limit}");
        if($vars['data']){
            $uidArray = iSQL::values($resource,'uid','array',null);
            $uidArray && $user_data = (array) user::data($uidArray);
        }
        if($resource)foreach ($resource as $key => $value) {
            unset($value['password']);
			$value['url']    = user::router($value['uid'],"url");
			$value['urls']   = user::router($value['uid'],"urls");
            $value+=user::info($value['uid'],$value['nickname'],$vars['size']);
			$value['gender'] = $value['gender']?'male':'female';
            if($vars['data'] && $user_data){
                $value['data']  = (array)$user_data[$value['uid']];
            }
			$resource[$key]  = $value;
        }
		$vars['cache'] && iCache::set($cache_name,$resource,$cache_time);
	}
	return $resource;
}

function user_category($vars=null){
	$row       = isset($vars['row'])?(int)$vars['row']:"10";
	$where_sql = "WHERE `uid`='".(int)$vars['userid']."' ";
	$where_sql.= " AND `appid`='".(int)$vars['appid']."'";
	$rs  = iDB::all("SELECT * FROM `#iCMS@__user_category` {$where_sql} ORDER BY `cid` ASC LIMIT $row");
	$resource = array();
	if($rs)foreach ($rs as $key => $value) {
		if($value['appid']==iCMS_APP_ARTICLE){
			$router ='uid:cid';
		}else if($value['appid']==iCMS_APP_FAVORITE){
			$router ='uid:fav:cid';
		}
		$value['url'] = iURL::router(array($router,array($value['uid'],$value['cid'])));
		if(isset($vars['loop'])){
			$resource[$key] = $value;
		}else{
			$resource[$value['cid']]=$value;
		}
	}
	return $resource;
}
function user_follow($vars=null){
	$maxperpage = isset($vars['row'])?(int)$vars['row']:"30";
	if($vars['fuid']){
		$where_sql = "WHERE `fuid`='".$vars['fuid']."'"; //fans
	}else{
		$where_sql = "WHERE `uid`='".$vars['userid']."'";//follow
	}

	$offset	= 0;
	$limit  = "LIMIT {$maxperpage}";
	if($vars['page']){
		$total	= iPHP::total('sql.md5',"SELECT count(*) FROM `#iCMS@__user_follow` {$where_sql} {$limit}");
		$multi  = iPHP::page(array('total'=>$total,'perpage'=>$maxperpage,'unit'=>iUI::lang('iCMS:page:sql'),'nowindex'=>$GLOBALS['page']));
		$offset = $multi->offset;
		$limit  = "LIMIT {$offset},{$maxperpage}";
        iPHP::assign("user_follow_total",$total);
	}
    $hash = md5($where_sql.$limit);

    if($vars['cache']){
		$cache_name = iPHP_DEVICE.'/user_follow/'.$hash;
		$resource   = iCache::get($cache_name);
    }
	$resource = iDB::all("SELECT * FROM `#iCMS@__user_follow` {$where_sql} {$limit}");
    if($vars['data']){
        $uidArray = iSQL::values($resource,array('uid','fuid'),'array',null);
        $uidArray && $user_data = (array) user::data($uidArray);
    }
    $vars['followed'] && $follow_data = user::follow($vars['followed'],'all');

	if($resource)foreach ($resource as $key => $value) {
		if($vars['fuid']){
			$value['avatar'] = user::router($value['uid'],'avatar');
			$value['url']    = user::router($value['uid'],'url');
		}else{
			$value['avatar'] = user::router($value['fuid'],'avatar');
			$value['url']    = user::router($value['fuid'],'url');
			$value['uid']    = $value['fuid'];
			$value['name']   = $value['fname'];
		}
        if($vars['data'] && $user_data){
            $value['data']  = (array)$user_data[$value['uid']];
        }
		$vars['followed'] && $value['followed'] = $follow_data[$value['uid']]?1:0;
		$resource[$key] = $value;
	}
	//var_dump($rs);
	return $resource;
}
function user_stat($vars=null){

}
//
function user_inbox($vars=null){
	$maxperpage = 30;
	$where_sql  = "WHERE `status` ='1'";
	if($_GET['user']){
		if($_GET['user']=="10000"){
			$where_sql.= " AND `userid`='10000' AND `friend` IN ('".user::$userid."','0')";
		}else{
			$friend = (int)$_GET['user'];
			$where_sql.= " AND `userid`='".user::$userid."' AND `friend`='".$friend."'";
		}
		$group_sql = '';
		$p_fields  = 'COUNT(*)';
		$s_fields  = '*';
		iPHP::assign("msg_count",false);
	}else{
//	 	$where_sql.= " AND (`userid`='".user::$userid."' OR (`userid`='10000' AND `friend`='0'))";
	 	$where_sql.= " AND `userid`='".user::$userid."'";
		$group_sql = ' GROUP BY `friend` DESC';
		$p_fields  = 'COUNT(DISTINCT id)';
		$s_fields  = 'max(id) AS id ,COUNT(id) AS msg_count,`userid`, `friend`, `send_uid`, `send_name`, `receiv_uid`, `receiv_name`, `content`, `type`, `sendtime`, `readtime`';
	 	iPHP::assign("msg_count",true);
	}

	$offset	= 0;
	$total	= iPHP::total($md5,"SELECT {$p_fields} FROM `#iCMS@__message` {$where_sql} {$group_sql}",'nocache');
	iPHP::assign("msgs_total",$total);
    $multi	= iPHP::page(array('total'=>$total,'perpage'=>$maxperpage,'unit'=>iUI::lang('iCMS:page:list'),'nowindex'=>$GLOBALS['page']));
    $offset	= $multi->offset;
	$resource = iDB::all("SELECT {$s_fields} FROM `#iCMS@__message` {$where_sql} {$group_sql} ORDER BY `id` DESC LIMIT {$offset},{$maxperpage}");
	$msg_type_map = array(
		'0'=>'系统信息',
		'1'=>'私信',
		'2'=>'提醒',
		'3'=>'留言',
	);
	if($resource)foreach ($resource as $key => $value) {
		$value['sender']   = user::info($value['send_uid'],$value['send_name']);
		$value['receiver'] = user::info($value['receiv_uid'],$value['receiv_name']);
		$value['label']    = $msg_type_map[$value['type']];

		if($value['userid']==$value['send_uid']){
			$value['is_sender'] = true;
			$value['user']      = $value['receiver'];
		}
		if($value['userid']==$value['receiv_uid']){
			$value['is_sender'] = false;
			$value['user']      = $value['sender'];
		}
		$value['url'] = iURL::router(array('user:inbox:uid',$value['user']['uid']));
		$resource[$key] = $value;
	}
	return $resource;
}
