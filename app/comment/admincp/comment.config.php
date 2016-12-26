<?php /**
* @package iCMS
* @copyright 2007-2017, iDreamSoft
* @license http://www.idreamsoft.com iDreamSoft
* @author coolmoo <idreamsoft@qq.com>
*/
defined('iPHP') OR exit('What are you doing?');
configAdmincp::head("评论系统配置");
?>
<div class="input-prepend">
    <span class="add-on">评论</span>
    <div class="switch">
        <input type="checkbox" data-type="switch" name="config[enable]" id="comment_enable" <?php echo $config['enable']?'checked':''; ?>/>
    </div>
</div>
<div class="clearfloat mb10"></div>
<div class="input-prepend">
    <span class="add-on">审核评论</span>
    <div class="switch">
        <input type="checkbox" data-type="switch" name="config[examine]" id="comment_examine" <?php echo $config['examine']?'checked':''; ?>/>
    </div>
</div>
<div class="clearfloat mb10"></div>
<div class="input-prepend">
    <span class="add-on">验证码</span>
    <div class="switch">
        <input type="checkbox" data-type="switch" name="config[seccode]" id="comment_seccode" <?php echo $config['seccode']?'checked':''; ?>/>
    </div>
</div>
<span class="help-inline">开启后发表评论需要验证码</span>
<hr />
<div class="input-prepend">
    <span class="add-on">第三方评论</span>
    <div class="switch">
        <input type="checkbox" data-type="switch" name="config[plugin][changyan][enable]" id="comment_plugin_changyan_enable" <?php echo $config['plugin']['changyan']['enable']?'checked':''; ?>/>
    </div>
    <span class="help-inline">使用云存储后,相关管理请到云存储管理</span>
</div>
<div class="clearfloat mb10"></div>
<h3 class="title">畅言评论</h3>
<span class="help-inline">申请地址:<a href="http://changyan.kuaizhan.com/?from=iCMS" target="_blank">http://changyan.kuaizhan.com</a></span>
<div class="clearfloat"></div>
<div class="input-prepend">
    <span class="add-on">APP ID</span>
    <input type="text" name="config[plugin][changyan][appid]" class="span4" id="changyan_appid" value="<?php echo $config['plugin']['changyan']['appid'] ; ?>"/>
</div>
<div class="clearfloat mb10"></div>
<div class="input-prepend">
    <span class="add-on">APP KEY</span>
    <input type="text" name="config[plugin][changyan][appkey]" class="span4" id="changyan_appkey" value="<?php echo $config['plugin']['changyan']['appkey'] ; ?>"/>
</div>
<?php configAdmincp::foot();?>
