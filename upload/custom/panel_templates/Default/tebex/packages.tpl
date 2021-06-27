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
                    <h1 class="h3 mb-0 text-gray-800">{$PACKAGES}</h1>
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{$PANEL_INDEX}">{$DASHBOARD}</a></li>
                        <li class="breadcrumb-item active">{$BUYCRAFT}</li>
                        <li class="breadcrumb-item active">{$PACKAGES}</li>
                    </ol>
                </div>

                <!-- Update Notification -->
                {include file='includes/update.tpl'}

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <!-- Success and Error Alerts -->
                        {include file='includes/alerts.tpl'}

                        {if isset($NO_PACKAGES)}
                            <p>{$NO_PACKAGES}</p>
                        {else}
                            {foreach from=$ALL_CATEGORIES item=category}
                                {if count($category.packages)}
                                    <div class="card card-default">
                                        <div class="card-header">
                                            {$category.name}
                                        </div>
                                        <div class="card-body">
                                            {foreach from=$category.packages item=package name=package_loop}
                                                {$package.name} <small>{$package.id_x}</small>
                                                {if isset($package.edit_link)}
                                                    <span class="float-md-right">
                                                            <a href="{$package.edit_link}" class="btn btn-info btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                                        </span>
                                                {/if}
                                                {if !$smarty.foreach.package_loop.last}<hr />{/if}
                                            {/foreach}
                                        </div>
                                    </div>
                                {/if}
                            {/foreach}
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
