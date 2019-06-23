<div class="card">
    <div class="card-body">
        <h2>{$FEATURED_PACKAGE}</h2>
        <div class="card text-center">
            {if $PACKAGE.image}
                <img class="card-img-top" src="{$PACKAGE.image}" alt="{$PACKAGE.name}">
            {/if}
            <div class="card-body">
                <h5 class="card-title">{$PACKAGE.name}</h5>
                <div class="ui divider"></div>
                {if $PACKAGE.sale_active}
                    <span style="color: #dc3545;text-decoration:line-through;">{$CURRENCY}{$PACKAGE.price}</span>
                {/if}
                {$CURRENCY}{$PACKAGE.real_price}

                <hr />

                <a class="btn btn-primary" href="{$PACKAGE.link}">
                    {$VIEW} &raquo;
                </a>
            </div>
        </div>
    </div>
</div>