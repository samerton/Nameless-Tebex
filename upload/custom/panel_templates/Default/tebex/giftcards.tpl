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
                    <h1 class="h3 mb-0 text-gray-800">{$GIFT_CARDS}</h1>
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{$PANEL_INDEX}">{$DASHBOARD}</a></li>
                        <li class="breadcrumb-item active">{$BUYCRAFT}</li>
                        <li class="breadcrumb-item active">{$GIFT_CARDS}</li>
                    </ol>
                </div>

                <!-- Update Notification -->
                {include file='includes/update.tpl'}

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <a class="btn btn-primary" href="{$NEW_GIFT_CARD_LINK}">{$NEW_GIFT_CARD}</a>
                        <hr />

                        <!-- Success and Error Alerts -->
                        {include file='includes/alerts.tpl'}

                        {if isset($NO_GIFT_CARDS)}
                            <p>{$NO_GIFT_CARDS}</p>
                        {else}
                            <div class="table-responsive">
                                <table class="table table-striped dataTables-giftcards">
                                    <thead>
                                    <tr>
                                        <th>{$GIFT_CARD_CODE}</th>
                                        <th>{$GIFT_CARD_NOTE}</th>
                                        <th>{$GIFT_CARD_BALANCE_REMAINING}</th>
                                        <th>{$GIFT_CARD_ACTIVE}</th>
                                        <th>{$VIEW}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {foreach from=$ALL_GIFT_CARDS item=giftcard}
                                        <tr>
                                            <td>{$giftcard.code}</td>
                                            <td>{$giftcard.note}</td>
                                            <td>{$giftcard.remaining}</td>
                                            <td>{if $giftcard.void}<i class="fas fa-times-circle fa-2x text-danger"></i>{else}<i class="fas fa-check-circle fa-2x text-success"></i>{/if}</td>
                                            <td><a href="{$giftcard.view_link}" class="btn btn-primary btn-sm">{$VIEW}</a></td>
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
