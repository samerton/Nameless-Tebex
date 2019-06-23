{include file='header.tpl'}
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    {include file='navbar.tpl'}
    {include file='sidebar.tpl'}

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">{$BANS}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{$PANEL_INDEX}">{$DASHBOARD}</a></li>
                            <li class="breadcrumb-item active">{$BUYCRAFT}</li>
                            <li class="breadcrumb-item active">{$BANS}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                {if isset($NEW_UPDATE)}
                {if $NEW_UPDATE_URGENT eq true}
                <div class="alert alert-danger">
                    {else}
                    <div class="alert alert-primary alert-dismissible" id="updateAlert">
                        <button type="button" class="close" id="closeUpdate" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        {/if}
                        {$NEW_UPDATE}
                        <br />
                        <a href="{$UPDATE_LINK}" class="btn btn-primary" style="text-decoration:none">{$UPDATE}</a>
                        <hr />
                        {$CURRENT_VERSION}<br />
                        {$NEW_VERSION}
                    </div>
                    {/if}

                    <div class="card">
                        <div class="card-body">
                            {if isset($NEW_BAN)}
                                <a href="{$NEW_BAN_LINK}" class="btn btn-primary">{$NEW_BAN}</a>
                                <hr />
                            {/if}

                            {if isset($SUCCESS)}
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h5><i class="icon fa fa-check"></i> {$SUCCESS_TITLE}</h5>
                                    {$SUCCESS}
                                </div>
                            {/if}

                            {if isset($ERRORS) && count($ERRORS)}
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h5><i class="icon fas fa-exclamation-triangle"></i> {$ERRORS_TITLE}</h5>
                                    <ul>
                                        {foreach from=$ERRORS item=error}
                                            <li>{$error}</li>
                                        {/foreach}
                                    </ul>
                                </div>
                            {/if}

                            {if isset($NO_BANS)}
                                <p>{$NO_BANS}</p>
                            {else}
                                <div class="table-responsive">
                                    <table class="table table-bordered dataTables-bans">
                                        <colgroup>
                                            <col span="1" style="width: 35%;">
                                            <col span="1" style="width: 30%;">
                                            <col span="1" style="width: 20%">
                                            <col span="1" style="width: 15%">
                                        </colgroup>
                                        <thead>
                                        <tr>
                                            <th>{$USER}</th>
                                            <th>{$IP_ADDRESS}</th>
                                            <th>{$DATE}</th>
                                            <th>{$VIEW}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        {foreach from=$BAN_LIST item=ban}
                                            <tr>
                                                <td><img src="{$ban.avatar}" class="rounded" style="max-width:25px;max-height:25px;" alt="{$ban.ign}" /> <span style="{$ban.style}">{$ban.ign}</span></td>
                                                <td>{$ban.ip}</td>
                                                <td data-sort="{$ban.date_unix}">{$ban.date}</td>
                                                <td>
                                                    <a href="{$ban.link}" class="btn btn-primary">{$VIEW}</a>
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

                </div>
        </section>
    </div>

    {include file='footer.tpl'}

</div>
<!-- ./wrapper -->

{include file='scripts.tpl'}

</body>
</html>