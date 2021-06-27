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
                        <h5 style="display:inline">{$CREATING_COUPON}</h5>
                        <div class="float-md-right">
                            <button role="button" class="btn btn-warning" onclick="showCancelModal()">{$CANCEL}</button>
                        </div>
                        <hr />

                        <!-- Success and Error Alerts -->
                        {include file='includes/alerts.tpl'}

                        <form action="" method="post">
                            <div class="form-group">
                                <label for="inputCode">{$COUPON_CODE}</label>
                                <input id="inputCode" type="text" class="form-control" name="code" placeholder="{$COUPON_CODE}" value="{$COUPON_CODE_VALUE}">
                            </div>

                            <div class="form-group">
                                <label for="inputNote">{$COUPON_NOTE}</label>
                                <textarea class="form-control" id="inputNote" name="note">{$COUPON_NOTE_VALUE}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="inputEffectiveOn">{$EFFECTIVE_ON}</label>
                                <select class="form-control" name="effective_on" id="inputEffectiveOn">
                                    <option value="1"{if $EFFECTIVE_ON_VALUE eq '1'} selected{/if}>{$CART}</option>
                                    <option value="2"{if $EFFECTIVE_ON_VALUE eq '2'} selected{/if}>{$PACKAGE}</option>
                                    <option value="3"{if $EFFECTIVE_ON_VALUE eq '3'} selected{/if}>{$CATEGORY}</option>
                                </select>
                            </div>

                            <div class="form-group" id="effectiveOnPackages">
                                <label for="inputEffectiveOnPackages">{$PACKAGES}</label> <small>{$SELECT_MULTIPLE_WITH_CTRL}</small>
                                <select class="form-control" id="inputEffectiveOnPackages" name="packages[]" multiple>
                                    {if count($AVAILABLE_PACKAGES)}
                                        {foreach from=$AVAILABLE_PACKAGES item=available_package}
                                            <option value="{$available_package->id|escape}">{$available_package->name|escape}</option>
                                        {/foreach}
                                    {/if}
                                </select>
                            </div>

                            <div class="form-group" id="effectiveOnCategories">
                                <label for="inputEffectiveOnCategories">{$CATEGORIES}</label> <small>{$SELECT_MULTIPLE_WITH_CTRL}</small>
                                <select class="form-control" id="inputEffectiveOnCategories" name="categories[]" multiple>
                                    {if count($AVAILABLE_CATEGORIES)}
                                        {foreach from=$AVAILABLE_CATEGORIES item=available_category}
                                            <option value="{$available_category->id|escape}">{$available_category->name|escape}</option>
                                        {/foreach}
                                    {/if}
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="inputDiscountType">{$DISCOUNT_TYPE}</label>
                                        <select class="form-control" id="inputDiscountType" name="discount_type">
                                            <option value="value">{$DISCOUNT_TYPE_VALUE}</option>
                                            <option value="percentage">{$DISCOUNT_TYPE_PERCENTAGE}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" id="discountTypeValue">
                                        <label for="inputDiscountTypeValue">{$DISCOUNT_TYPE_VALUE}</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">{$CURRENCY}</span>
                                            </div>
                                            <input type="number" class="form-control" name="discount_amount" id="inputDiscountTypeValue" placeholder="{$DISCOUNT_TYPE_VALUE}" value="{$DISCOUNT_TYPE_VALUE_VALUE}" step="0.01">
                                        </div>
                                    </div>
                                    <div class="form-group" id="discountTypePercentage">
                                        <label for="inputDiscountTypePercentage">{$DISCOUNT_TYPE_PERCENTAGE}</label>
                                        <div class="input-group mb-3">
                                            <input type="number" class="form-control" name="discount_percentage" id="inputDiscountTypePercentage" placeholder="{$DISCOUNT_TYPE_PERCENTAGE}" value="{$DISCOUNT_TYPE_PERCENTAGE_VALUE}">
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 align-self-center">
                                    <div class="form-group">
                                        <label for="inputRedeemUnlimited">{$UNLIMITED_USAGE}</label>
                                        <input type="checkbox" name="redeem_unlimited" id="inputRedeemUnlimited" class="js-switch" {if $UNLIMITED_USAGE_VALUE}checked {/if}/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" id="redeemLimit">
                                        <label for="inputRedeemLimit">{$USES}</label>
                                        <input type="number" class="form-control" name="expire_limit" id="inputRedeemLimit" placeholder="{$USES}" value="{$USES_VALUE}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 align-self-center">
                                    <div class="form-group">
                                        <label for="inputExpireNever">{$NEVER_EXPIRE}</label>
                                        <input type="checkbox" name="expire_never" id="inputExpireNever" class="js-switch" {if $NEVER_EXPIRE_VALUE}checked {/if}/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" id="expireDate">
                                        <label for="inputExpiryDate">{$EXPIRY_DATE}</label>
                                        <input type="text" class="form-control" name="expire_date" id="inputExpiryDate" placeholder="{$EXPIRY_DATE}" value="{$EXPIRY_DATE_VALUE}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputStartDate">{$START_DATE}</label>
                                <input type="text" class="form-control" name="start_date" id="inputStartDate" placeholder="{$START_DATE}" value="{$START_DATE_VALUE}">
                            </div>

                            <div class="form-group">
                                <label for="inputBasketType">{$BASKET_TYPE}</label>
                                <select class="form-control" name="basket_type" id="inputBasketType">
                                    <option value="both"{if $BASKET_TYPE_VALUE eq 'both'} selected{/if}>{$ALL_PURCHASES}</option>
                                    <option value="single"{if $BASKET_TYPE_VALUE eq 'single'} selected{/if}>{$ONE_OFF_PURCHASES}</option>
                                    <option value="subscription"{if $BASKET_TYPE_VALUE eq 'subscription'} selected{/if}>{$SUBSCRIPTIONS}</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="inputDiscountApplicationType">{$DISCOUNT_APPLICATION_TYPE}</label>
                                <select class="form-control" name="discount_application_method" id="inputDiscountApplicationType">
                                    <option value="1"{if $DISCOUNT_APPLICATION_TYPE_VALUE eq '1'} selected{/if}>{$EACH_PACKAGE}</option>
                                    <option value="2"{if $DISCOUNT_APPLICATION_TYPE_VALUE eq '2'} selected{/if}>{$BASKET_BEFORE_SALES}</option>
                                    <option value="3"{if $DISCOUNT_APPLICATION_TYPE_VALUE eq '3'} selected{/if}>{$BASKET_AFTER_SALES}</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="inputMinimum">{$MINIMUM_SPEND}</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{$CURRENCY}</span>
                                    </div>
                                    <input type="number" class="form-control" name="minimum" id="inputMinimum" placeholder="{$MINIMUM_SPEND}" value="{$MINIMUM_SPEND_VALUE}" step="0.01">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputUsername">{$USER_COUPON_FOR}</label> <small>{$OPTIONAL}</small>
                                <input type="text" class="form-control" id="inputUsername" name="username" placeholder="{$USER_COUPON_FOR}" value="{$USER_COUPON_FOR_VALUE}">
                            </div>

                            <div class="form-group">
                                <input type="hidden" name="token" value="{$TOKEN}">
                                <input type="submit" class="btn btn-primary" value="{$SUBMIT}">
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

        <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{$ARE_YOU_SURE}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {$CONFIRM_CANCEL}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{$NO}</button>
                        <a href="{$CANCEL_LINK}" class="btn btn-primary">{$YES}</a>
                    </div>
                </div>
            </div>
        </div>

        {include file='footer.tpl'}

    </div>
</div>

{include file='scripts.tpl'}

<script type="text/javascript">
  function showCancelModal(){
    $('#cancelModal').modal().show();
  }
</script>

</body>
</html>
