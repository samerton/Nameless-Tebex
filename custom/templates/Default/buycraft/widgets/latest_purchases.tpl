<div class="card">
    <div class="card-body">
        <h2>{$LATEST_PURCHASES}</h2>
        {foreach from=$PURCHASES item=purchase name=purchases_array}
            <div class="row align-items-center">
                <div class="col-md-3">
                    <a href="{$purchase.profile}"><img class="img-centre rounded" style="max-height:30px;max-width:30px;" src="{$purchase.avatar}" alt="{$purchase.username}"/></a>
                </div>
                <div class="col-md-9">
                    <a href="{$purchase.profile}" style="{$purchase.style}">{$purchase.username}</a><br />
                    <span data-toggle="tooltip" data-trigger="hover" data-original-title="{$purchase.date_full}">{$purchase.date_friendly}</span>
                </div>
            </div>
            {if not $smarty.foreach.purchases_array.last}<br />{/if}
        {/foreach}
    </div>
</div>