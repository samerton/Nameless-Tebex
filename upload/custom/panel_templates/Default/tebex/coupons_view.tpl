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
                        <h1 class="m-0 text-dark">{$COUPONS}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{$PANEL_INDEX}">{$DASHBOARD}</a></li>
                            <li class="breadcrumb-item active">{$BUYCRAFT}</li>
                            <li class="breadcrumb-item active">{$COUPONS}</li>
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
                            <h5 style="display:inline">{$VIEWING_COUPON}</h5>
                            <div class="float-md-right">
                                {if isset($DELETE_COUPON)}
                                    <button type="button" onclick="showDeleteModal()" class="btn btn-danger">{$DELETE_COUPON}</button>
                                {/if}
                                <a href="{$BACK_LINK}" class="btn btn-primary">{$BACK}</a>
                            </div>
                            <hr />

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

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <colgroup>
                                        <col span="1" style="width: 50%">
                                        <col span="1" style="width: 50%">
                                    </colgroup>
                                    <tbody>
                                    <tr>
                                        <td><strong>{$COUPON_CODE}</strong></td>
                                        <td>{$COUPON_CODE_VALUE}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{$COUPON_NOTE}</strong></td>
                                        <td>{$COUPON_NOTE_VALUE}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{$EFFECTIVE_ON}</strong></td>
                                        <td>{$EFFECTIVE_ON_VALUE}</td>
                                    </tr>
                                    {if $EFFECTIVE_ON_TYPE == 'package'}
                                        <tr>
                                            <td><strong>{$PACKAGES}</strong></td>
                                            <td>{if count($PACKAGES_VALUE)}{foreach from=$PACKAGES_VALUE item=package name=packages}{$package}{if not $smarty.foreach.packages.last}, {/if}{/foreach}{/if}</td>
                                        </tr>
                                    {elseif $EFFECTIVE_ON_TYPE == 'category'}
                                        <tr>
                                            <td><strong>{$CATEGORIES}</strong></td>
                                            <td>{if count($CATEGORIES_VALUE)}{foreach from=$CATEGORIES_VALUE item=category name=categories}{$category}{if not $smarty.foreach.categories.last}, {/if}{/foreach}{/if}</td>
                                        </tr>
                                    {/if}
                                    <tr>
                                        <td><strong>{$BASKET_TYPE}</strong></td>
                                        <td>{$BASKET_TYPE_VALUE}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{$DISCOUNT_TYPE}</strong></td>
                                        <td>{$DISCOUNT_TYPE_VALUE}</td>
                                    </tr>
                                    {if $DISCOUNT_TYPE_RAW == 'value'}
                                        <tr>
                                            <td><strong>{$VALUE}</strong></td>
                                            <td>{$CURRENCY}{$DISCOUNT_VALUE}</td>
                                        </tr>
                                    {elseif $DISCOUNT_TYPE_RAW == 'percentage'}
                                        <tr>
                                            <td><strong>{$PERCENTAGE}</strong></td>
                                            <td>{$DISCOUNT_PERCENTAGE}%</td>
                                        </tr>
                                    {/if}
                                    <tr>
                                        <td><strong>{$START_DATE}</strong></td>
                                        <td>{$START_DATE_VALUE}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{$END_DATE}</strong></td>
                                        <td>{$END_DATE_VALUE}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{$USES}</strong></td>
                                        <td>{if $UNLIMITED_VALUE}{$UNLIMITED_USAGE}{else}{$USES_COUNT}{/if}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{$MINIMUM_SPEND}</strong></td>
                                        <td>{$CURRENCY}{$MINIMUM_SPEND_VALUE}</td>
                                    </tr>
                                    {if $USER_COUPON_FOR_VALUE}
                                        <tr>
                                            <td><strong>{$USER_COUPON_FOR}</strong></td>
                                            <td>{$USER_COUPON_FOR_VALUE}</td>
                                        </tr>
                                    {/if}
                                    </tbody>
                                </table>
                            </div>

                            <div class="callout callout-info">
                                <h5><i class="icon fa fa-info-circle"></i> {$INFO}</h5>
                                {$EDIT_IN_BUYCRAFT}
                            </div>

                        </div>
                    </div>

                    <!-- Spacing -->
                    <div style="height:1rem;"></div>

                </div>
        </section>
    </div>

    {if isset($DELETE_COUPON)}
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{$DELETE_COUPON}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {$CONFIRM_DELETE_COUPON}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{$CANCEL}</button>
                        <form action="" method="post">
                            <input type="hidden" name="token" value="{$TOKEN}">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="{$COUPON_ID}">
                            <input type="submit" class="btn btn-danger" value="{$DELETE_COUPON}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    {/if}

    {include file='footer.tpl'}

</div>
<!-- ./wrapper -->

{include file='scripts.tpl'}

<script type="text/javascript">
    {if isset($DELETE_COUPON)}
    function showDeleteModal(){
        $('#deleteModal').modal().show();
    }
    {/if}
</script>

</body>
</html>