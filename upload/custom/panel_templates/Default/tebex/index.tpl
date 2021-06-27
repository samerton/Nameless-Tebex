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
                    <h1 class="h3 mb-0 text-gray-800">{$BUYCRAFT}</h1>
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{$PANEL_INDEX}">{$DASHBOARD}</a></li>
                        <li class="breadcrumb-item active">{$BUYCRAFT}</li>
                    </ol>
                </div>

                <!-- Update Notification -->
                {include file='includes/update.tpl'}

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <!-- Success and Error Alerts -->
                        {include file='includes/alerts.tpl'}

                        <form action="" method="post">
                            <div class="form-group">
                                <label for="inputSecretKey">{$SERVER_KEY}</label>
                                <span class="badge badge-info" data-html="true" data-toggle="popover" title="{$INFO}" data-content="{$SERVER_KEY_INFO}"><i class="fas fa-question-circle"></i></span>
                                <input id="inputSecretKey" name="server_key" class="form-control" placeholder="{$SERVER_KEY}" value="{$SERVER_KEY_VALUE}">
                            </div>

                            <div class="form-group">
                                <label for="inputAllowGuests">{$ALLOW_GUESTS}</label>
                                <input type="checkbox" name="allow_guests" id="inputAllowGuests" class="js-switch" {if $ALLOW_GUESTS_VALUE} checked{/if} />
                            </div>

                            <div class="form-group">
                                <label for="inputHomeTab">{$HOME_TAB}</label>
                                <input type="checkbox" name="home_tab" id="inputHomeTab" class="js-switch" {if $HOME_TAB_VALUE} checked{/if} />
                            </div>

                            <div class="form-group">
                                <label for="inputStorePath">{$STORE_PATH}</label>
                                <input type="text" class="form-control" id="inputStorePath" name="store_path" placeholder="{$STORE_PATH}" value="{$STORE_PATH_VALUE}">
                            </div>

                            <div class="form-group">
                                <label for="inputStoreContent">{$STORE_INDEX_CONTENT}</label>
                                <textarea id="inputStoreContent" name="store_content">{$STORE_INDEX_CONTENT_VALUE}</textarea>
                            </div>

                            <div class="form-group">
                                <input type="hidden" name="token" value="{$TOKEN}">
                                <input type="submit" value="{$SUBMIT}" class="btn btn-primary">
                            </div>
                        </form>

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
