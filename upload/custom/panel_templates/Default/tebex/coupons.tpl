{include file='header.tpl'}
<body id="page-top">

<!-- Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    {include file='sidebar.tpl'}

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main content -->
        <div id="content">

            <!-- Topbar -->
            {include file='navbar.tpl'}

            <!-- Begin Page Content -->
            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">{$COUPONS}</h1>
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{$PANEL_INDEX}">{$DASHBOARD}</a></li>
                        <li class="breadcrumb-item active">{$BUYCRAFT}</li>
                        <li class="breadcrumb-item active">{$COUPONS}</li>
                    </ol>
                </div>

                <!-- Update Notification -->
                {include file='includes/update.tpl'}

                <div class="card shadow mb-4">
                    <div class="card-body">
                        {if isset($NEW_COUPON)}
                            <a href="{$NEW_COUPON_LINK}" class="btn btn-primary">{$NEW_COUPON}</a>
                            <hr />
                        {/if}

                        <!-- Success and Error Alerts -->
                        {include file='includes/alerts.tpl'}

                        {if isset($NO_COUPONS)}
                            <p>{$NO_COUPONS}</p>
                        {else}
                            <div class="table-responsive">
                                <table class="table table-bordered dataTables-coupons">
                                    <colgroup>
                                        <col span="1" style="width: 35%;">
                                        <col span="1" style="width: 30%;">
                                        <col span="1" style="width: 20%">
                                        <col span="1" style="width: 15%">
                                    </colgroup>
                                    <thead>
                                    <tr>
                                        <th>{$COUPON_CODE}</th>
                                        <th>{$EXPIRY_DATE_TABLE}</th>
                                        <th>{$USES}</th>
                                        <th>{$VIEW}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {foreach from=$COUPON_LIST item=coupon}
                                        <tr>
                                            <td>{$coupon.code}</td>
                                            <td>{$coupon.expiry}</td>
                                            <td>{$coupon.limit}</td>
                                            <td>
                                                <a href="{$coupon.link}" class="btn btn-primary">{$VIEW}</a>
                                            </td>
                                        </tr>
                                    {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        {/if}

                    </div>
                </div>

                <!-- Spacing -->
                <div style="height:1rem;"></div>

                <!-- End Page Content -->
            </div>

            <!-- End Main Content -->
        </div>

        {include file='footer.tpl'}

    </div>
</div>

{include file='scripts.tpl'}

</body>
</html>
