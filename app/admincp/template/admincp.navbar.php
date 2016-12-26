<?php /**
 * @package iCMS
 * @copyright 2007-2017, iDreamSoft
 * @license http://www.idreamsoft.com iDreamSoft
 * @author coolmoo <idreamsoft@qq.com>
 */
defined('iPHP') OR exit('What are you doing?');
?>
<div id="header" class="navbar navbar-static-top">
  <div class="navbar-inner">
    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
    <span class="fa fa-bars"></span>
    </a>
    <a class="brand iCMS-logo" href="http://www.idreamsoft.com" target="_blank">
      <img src="./app/admincp/ui/iCMS.logo.mini.png" />
    </a>
      <div class="nav-collapse collapse">
        <ul class="nav iMenu-nav" id="iCMS-menu">
          <?php echo admincp::$menu->nav(); ?>
        </ul>
        <ul class="nav pull-right">
          <li><a href="<?php echo iCMS_URL;?>" target="_blank" title="网站首页"><i class="fa fa-home fa-lg"></i></a></li>
          <li class="divider-vertical"></li>
          <li class="dropdown"> <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" title="<?php echo iMember::$data->nickname;?>"><i class="fa fa-user"></i> <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="javascript:;"><?php echo iMember::$group->name;?></a></li>
              <li class="divider"></li>
              <li><a href="<?php echo __ADMINCP__; ?>=members&do=job"><i class="fa fa-bar-chart-o"></i> 工作统计</a></li>
              <li><a href="<?php echo __ADMINCP__; ?>=members&do=edit"><i class="fa fa-user"></i> 更改信息</a></li>
              <li class="divider"></li>
              <li><a href="<?php echo __SELF__; ?>?do=logout&frame=iPHP" target="iPHP_FRAME"><i class="fa fa-sign-out"></i> 注销</a></li>
            </ul>
          </li>
        </ul>
      </div>
  </div>
</div>
<?php if(iCMS::$config['other']['sidebar_enable']){?>
<div id="sidebar" class="navbar">
	<div id="sidebartop" class="navbar-inner">
    <a class="brand iCMS-logo" href="http://www.idreamsoft.com" target="_blank">
    <img src="./app/admincp/ui/iCMS.logo.mini.png" />
    </a>
  </div>
  <ul class="iMenu-sidebar">
    <?php echo admincp::$menu->sidebar(); ?>
    <li class="last"></li>
  </ul>
  <div class="clearfloat"></div>
  <span id="mini"> <i class="fa fa-arrow-circle-left"></i> </span>
</div>
<?php }?>
<div id="content">
  <div id="breadcrumb">
    <a href="<?php echo __SELF__; ?>" title="返回管理首页" class="tip-bottom"><i class="fa fa-home"></i> 管理中心</a>
  </div>
<script type="text/javascript">
$(".iMenu-nav,.iMenu-sidebar")
.find('a[href="<?php echo admincp::$menu->url; ?>"]')
.each(function(){
  find_parent(this);
});
var href = '/'+window.location.href.split("/")[3];

$(".submenu,.dropdown-submenu").find('a[href="'+href+'"]').each(function(){
  $(this).addClass("active");
  $(this).parent().addClass("active");
});

$("li.active>a","#iCMS-menu").each(function(){
  var a = $(this).clone();
  a.removeClass('dropdown-toggle').removeAttr('data-toggle');
  $("#breadcrumb").append(a);
});

function find_parent (a) {
  var p = $(a).parent();
  if(p[0].nodeName=="LI"||p[0].nodeName=="UL"){
    $(p).addClass("active");
    if($(p).hasClass("submenu")){
      $(p).addClass("open");
      $("ul",$(p)).show();
    }
    if(p[0].nodeName=="UL" && !$(p).hasClass("dropdown-menu")){
      $(p).addClass("open").show();
    }
    find_parent(p[0]);
  }
  return true;
}

</script>
