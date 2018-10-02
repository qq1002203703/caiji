{%content@common/main%}
<div class="wrapper">
    <!-- container -->
    <div class="container-fluid">
        <!-- page-title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="btn-group pull-right">

                        <ol class="breadcrumb hide-phone p-0 m-0">
                            <li class="breadcrumb-item"><a href="#">首页</a></li>
                            <li class="breadcrumb-item"><a href="#">PHP</a></li>
                            <li class="breadcrumb-item"><a href="#"><?=$title?></a></li>
                        </ol>
                    </div>
                    
                </div>
            </div>
        </div>
        <!-- //page title -->
        <div>{%block@middle%}</div>
    </div>
    <!-- //container -->
</div>
<!-- end wrapper -->