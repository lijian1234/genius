<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Genius</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- For all the post method -->
    <meta name="csrf-token" content="{!! csrf_token() !!}"/> 
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/font-awesome/css/font-awesome.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="plugins/iCheck/flat/blue.css">
    <!-- Morris chart -->
    <link rel="stylesheet" href="plugins/morris/morris.css">
    <!-- Date Picker -->
    <link rel="stylesheet" href="plugins/datepicker/datepicker3.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker-bs3.css">
    <!-- bootstrap wysihtml5 - text editor -->
    <link type="text/css" rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
    <!-- grid -->
    <link type="text/css" rel="stylesheet" href="plugins/bootstrap-grid/css/grid.css" >
    <!-- datatables -->
     <link rel="stylesheet" href="plugins/datatables/grid.css">
    <!--select2-->
    <link type="text/css" href="plugins/select2/select2.css" rel="stylesheet" />
    <!-- treeview -->
    <link type="text/css" href="plugins/treeview/bootstrap-treeview.min.css" rel="stylesheet"/>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="plugins/html5shiv/html5shiv/3.7.3/"></script>
    <script src="plugins/respond/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="dist/css/nav.css">
</head>
<body class="hold-transition skin-blue sidebar-mini" style="font-family: Arial,Microsoft YaHei,微软雅黑,sans-serif">
<div class="wrapper">
    <header class="main-header header-v6">
        <!-- Logo -->
        <a href="" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>E</b>GS</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>Ericsson</b> Genius</span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top mega-menu">
            <!-- Sidebar toggle button-->
            <!-- <a href="" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a> -->
            <div class="collapse navbar-collapse navbar-responsive-collapse">
                <div class="menu-container">
                    <ul class="nav navbar-nav">
                        <!-- 网络概览 -->
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-line-chart"></i>网络概览
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="scale"><i class="fa fa-circle-o"></i>规模概览</a></li>
                                <li><a href="network"><i class="fa fa-circle-o"></i>指标概览</a></li>
                                <li><a href="weak"><i class="fa fa-circle-o"></i>短板概览</a></li>
                            </ul>
                        </li>
                        <!-- end 网络概览 -->

                        <!-- 日常优化 -->
                        <li class="dropdown mega-menu-fullwidth">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-dashboard"></i>日常优化
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <div class="mega-menu-content disable-icons">
                                        <div class="container">
                                            <div class="row equal-height">
                                                <div class="col-md-3 equal-height-in" style="width:20%;">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>指标分析</h3></li>
                                                        <!-- 指标分析 -->
                                                        <li><a href="LTEQuery"><i class="fa fa-circle-o"></i> LTE指标查询</a></li>
                                                        <li><a href="NBIQuery"><i class="fa fa-circle-o"></i> NBI指标查询</a></li>
                                                        <li><a href="GSMQuery"><i class="fa fa-circle-o"></i> GSM指标查询</a></li>
                                                        <li><a href="CustomQuery"><i class="fa fa-circle-o"></i> LTE语句查询</a></li> 
                                                        <!-- End 指标分析 -->
                                                    </ul>
                                                </div>
                                                <div class="col-md-3 equal-height-in" style="width:20%;">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>参数分析</h3></li>
                                                        <!-- 参数分析 -->
                                                        <li><a href="paramQuery"><i class="fa fa-circle-o"></i> 参数查询</a><li>
                                                        <li><a href="paramDistribution"><i class="fa fa-circle-o"></i> 参数分布</a><li>
                                                        <li><a href="consistencyCheck"><i class="fa fa-circle-o"></i> 一致性检查</a><li>
                                                        <li><a href="baselineCheck"><i class="fa fa-circle-o"></i> Baseline检查</a><li>
                                                        <li><a href="bulkcmMark"><i class="fa fa-circle-o"></i> bulkcm留痕</a><li>
                                                        <li><a href="kgetpartMark"><i class="fa fa-circle-o"></i> kgetpart留痕</a><li>
                                                        <!-- End 参数分析 -->
                                                    </ul>
                                                </div>
                                                <div class="col-md-3 equal-height-in" style="width:20%;">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>差小区分析</h3></li>
                                                        <!-- 差小区分析s -->
                                                        <li><a href="lowAccessCell"><i class="fa fa-circle-o"></i> 低接入小区</a><li>
                                                        <li><a href="highLostCell"><i class="fa fa-circle-o"></i> 高掉线小区</a><li>
                                                        <li><a href="badHandoverCell"><i class="fa fa-circle-o"></i> 切换差小区</a><li>
                                                        <li><a href="failureAnalysis"><i class="fa fa-circle-o"></i> 失败原因分析</a><li>
                                                        <!-- End 差小区分析 -->
                                                    </ul>
                                                </div>
                                                <div class="col-md-3 equal-height-in" style="width:20%;">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>邻区分析</h3></li>
                                                        <!-- 邻区分析 -->
                                                        <li><a href="switch"><i class="fa fa-circle-o"></i>切换查询</a></li>
                                                        <!-- End 邻区分析 -->
                                                    </ul>
                                                </div>
                                                <div class="col-md-3 equal-height-in" style="width:20%;">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>告警分析</h3></li>
                                                        <!-- 告警分析 -->
                                                        <li><a href="currentAlarmQuery"><i class="fa fa-circle-o"></i>当前告警查询</a></li>
                                                        <li><a href="historyAlarmQuery"><i class="fa fa-circle-o"></i>历史告警查询</a></li>
                                                        <!-- End 告警分析 -->
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- end 日常优化 -->
                        <!-- 投诉处理 -->
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-phone-square"></i>投诉处理
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="signalingBacktracking"><i class="fa fa-circle-o"></i> 信令回溯</a></li>
                                <li><a href="signalingAnalysis"><i class="fa fa-circle-o"></i> 信令分析</a></li>
                            </ul>
                        </li>
                        <!-- end 投诉处理 -->
                        <!-- 专项研究 -->
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-rocket"></i>专项研究
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <div class="mega-menu-content disable-icons">
                                        <div class="container" style="width:500px;">
                                            <div class="row equal-height">
                                                <div class="col-md-6 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>弱覆盖分析</h3></li>
                                                        <!-- 弱覆盖分析 -->
                                                        <li><a href="weakCover"><i class="fa fa-circle-o"></i> <span>弱覆盖点图</span></a></li>
                                                        <li><a href="weakCoverRatio"><i class="fa fa-circle-o"></i> <span>弱覆盖指标</span></a></li>
                                                        <!-- End 弱覆盖分析 -->
                                                    </ul>
                                                </div>
                                                <div class="col-md-6 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>高干扰分析</h3></li>
                                                        <!-- 高干扰分析 -->
                                                        <li><a href="interCloud"><i class="fa fa-circle-o"></i> <span>干扰云图</span></a></li>
                                                        <li><a href="interPointCloud"><i class="fa fa-circle-o"></i> <span>干扰点图</span></a></li>
                                                        <!-- End 高干扰分析 -->
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- end 专项研究 -->
                        <!-- 网格优化 -->
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-delicious"></i>网格优化
                            </a>
                        </li>
                        <!-- end 网格优化 -->
                        <!-- 网络规划 -->
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-align-justify"></i>网络规划
                            </a>
                            <ul class="dropdown-menu" style="left:250px">
                                <li>
                                    <div class="mega-menu-content disable-icons">
                                        <div class="container" style="width:800px;">
                                            <div class="row equal-height">
                                                <div class="col-md-3 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>邻区分析</h3></li>
                                                        <!-- 邻区分析 -->
                                                        <li><a href="GSMNeighborAnalysis"><i class="fa fa-circle-o"></i> <span>MRE补2G邻区</span></a></li>
                                                        <li><a href="CDRServeNeighAnalysis"><i class="fa fa-circle-o"></i> <span>CDR补2G邻区</span></a></li>
                                                        <li><a href="LTENeighborAnalysis"><i class="fa fa-circle-o"></i> <span>异频补邻区</span></a></li>
                                                        <li><a href="MROServeNeighAnalysis"><i class="fa fa-circle-o"></i> <span>同频补邻区</span></a></li>
                                                        <!-- End 邻区分析 -->
                                                    </ul>
                                                </div>
                                                <div class="col-md-3 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>问题邻区分析</h3></li>
                                                        <!-- 问题邻区分析 -->
                                                        <li><a href="relationNonHandover"><i class="fa fa-circle-o"></i> <span>无切换邻区分析</span></a></li>
                                                        <li><a href="relationBadHandover"><i class="fa fa-circle-o"></i> <span>切换差邻区分析</span></a></li>
                                                        <!-- End 问题邻区分析 -->
                                                    </ul>
                                                </div>
                                                <div class="col-md-3 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>PCI分析</h3></li>
                                                        <!-- PCI分析 -->
                                                        <li><a href="PCIMOD3Analysis"><i class="fa fa-circle-o"></i> <span>PCI MOD 3分析</span></a></li>
                                                        <!-- End PCI分析 -->
                                                    </ul>
                                                </div>
                                                <div class="col-md-3 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>门限分析</h3></li>
                                                        <!-- 门限分析 -->
                                                        <li><a href="A2ThresholdAnalysis"><i class="fa fa-circle-o"></i> <span>A2门限分析</span></a></li>
                                                        <li><a href="A5ThresholdAnalysis"><i class="fa fa-circle-o"></i> <span>A5门限分析</span></a></li>
                                                        <!-- End 门限分析 -->
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- end 网络规划 -->
                        <!-- 系统管理 -->
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-cogs"></i>系统管理
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <div class="mega-menu-content disable-icons">
                                        <div class="container" style="width:500px;" id="sys_container">
                                            <div class="row equal-height">
                                                <div class="col-md-6 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>数据管理</h3></li>
                                                        <!-- 数据管理 -->
                                                        <li><a href="siteManage"><i class="fa fa-circle-o"></i>站点管理</a></li>
                                                        <li><a href="storageManage"><i class="fa fa-circle-o"></i>入库管理</a></li>
                                                        <li><a href="paramsManage"><i class="fa fa-circle-o"></i>参数管理</a></li>
                                                        <li><a href="dataSourceManage"><i class="fa fa-circle-o"></i>数据源管理</a></li>
                                                        <!-- End 数据管理 -->
                                                    </ul>
                                                </div>
                                                <div class="col-md-6 equal-height-in" id="adminOnly">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>权限管理</h3></li>
                                                        <!-- 权限管理 -->
                                                        <li><a href="userManage"><i class="fa fa-circle-o"></i>账户管理</a></li>
                                                        <li><a href="emailManage"><i class="fa fa-circle-o"></i>邮箱管理</a></li>
                                                        <li><a href="ENIQManage"><i class="fa fa-circle-o"></i>ENIQ管理</a></li>
                                                        <li><a href="noticeManage"><i class="fa fa-circle-o"></i>通知管理</a></li>
                                                        <!-- End 权限管理 -->
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- end 系统管理 -->
                    </ul>
                </div>
                <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- Messages: style can be found in dropdown.less-->
                    <li class="dropdown messages-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-envelope-o"></i>
                        </a>
                    </li>
                    <!-- Notifications: style can be found in dropdown.less -->
                    <li class="dropdown notifications-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-bell-o"></i>
                            <span class="label label-warning" id="noticeNumber"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header adminOnly">
                                <a class="btn btn-default" onclick="addNotice()">
                                    <i class="fa fa-plus"></i>新增通知
                                </a>
                            </li>
                            <li>
                            <!-- inner menu: contains the actual data -->
                                <ul class="menu" id="noticeUl">
                                    <!-- <li><a href="#">5 new members joined today</a></li> -->
                                </ul>
                            </li>
                            <li class="header">
                                <input type="hidden" value="" id="noticeIds">
                                <a class="btn btn-default" onclick="readAll()">全部已读</a>
                            </li>
                        </ul>
                    </li>
                    <!-- Tasks: style can be found in dropdown.less -->
                    <li class="dropdown tasks-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-flag-o"></i>
                        </a>
                    </li>
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-user"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- Menu Body -->
                            <li class="user-body">
                                <div class="row">
                                    <label class="col-sm-4 text-right">user：</label>
                                    <span class="col-sm-8" id="user_user"></span>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 text-right">type：</label>
                                    <span class="col-sm-8" id="user_type"></span>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 text-right">email：</label>
                                    <span class="col-sm-8" id="user_email"></span>
                                </div>
                                
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="text-center">
                                    <a class="btn btn-default btn-flat" onclick="signout()">Sign out</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <!-- Control Sidebar Toggle Button -->
                    <li class="dropdown options-menu">
                        <a href="#"  class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-gears"></i></a>
                    </li>
                </ul>
            </div>
            </div>
            <!--/navbar-collapse-->
            
        </nav>
    </header>
   
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content-header">
            @yield('content-header')
        </section>
        <section class="content">
            @yield('content')
        </section>
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> 2.3.3
        </div>
        <strong>Copyright &copy; 2015-2016 Ericsson.</strong> All rights
        reserved.
    </footer>
    <!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div>
<!-- 新增通知 -->
<div class="modal fade" id="add_notice">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">通知</h8>
            </div>
            <form class="form-horizontal" role="form" id="noticeForm">
            <div class="modal-body text-center">
                <input type="hidden" id="noticeId" value="">
                <div class="form-group">
                    <label for="noticeTitle" class="col-sm-2 col-sm-offset-2 control-label">标题：</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="noticeTitle" id="noticeTitle" placeholder="通知标题" maxlength="50">
                    </div>
                </div>
                <div class="form-group">
                    <label for="noticeContent" class="col-sm-2 col-sm-offset-2 control-label">内容：</label>
                    <div class="col-sm-6">
                        <textarea class="form-control" name="noticeContent" id="noticeContent" style="height : 100px;resize: none;" maxlength="500"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" name="submit" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="saveBtn" onclick="updateNotice()">保存</button>
                <button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- 查看通知 -->
<div class="modal fade" id="read_notice">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title">通知</h8>
            </div>
            <div class="modal-body text-center row">
                <input type="hidden" name="noticeId_read" id="noticeId_read" value="">
                <div class="col-sm-8 col-sm-offset-2" >
                    <h4 id="noticeTitle_read" style="word-break:break-all;"></h4>
                </div>
                <div class="col-sm-6 col-sm-offset-3">
                    <small id="noticePublisher" class="hidden"></small>
                    <small id="noticePublishTime"></small>
                </div>
                <div class="col-sm-10 col-sm-offset-1" id="noticeContent_read" style="padding-top:20px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" name="submit" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="readBtn" onclick="setNoticeReaded()">设为已读</button>
                <button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>
<!-- ./wrapper -->

<!-- jQuery 2.2.0 -->
<script src="plugins/jQuery/jQuery-2.2.0.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="plugins/jQueryUI/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- datepicker -->
<script src="plugins/datepicker/bootstrap-datepicker.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="plugins/fastclick/fastclick.js"></script>

<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>
<script src="dist/js/genius/sidebar/locate.js"></script>

<!--input select-->
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

<!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>

<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<script src="dist/js/nav.js"></script>
<!-- For all the post method --> 
<!-- <script>
    $.ajaxSetup({  
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}  
    }); 
</script> -->

@yield('scripts')
<!--end-->
</body>
</html>
