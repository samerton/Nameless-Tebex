{include file='header.tpl'}
{include file='navbar.tpl'}

<div class="container">
    <div class="card">
        <div class="card-body">
            <h2 style="display:inline">{$STORE}</h2>
            <a class="btn btn-primary float-lg-right" href="{$STORE_URL}" target="_blank">{$VIEW_FULL_STORE}</a>

            <hr />

            <nav class="navbar navbar-expand-lg navbar-dark bg-primary" style="border-radius:5px;">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#storeNav" aria-controls="storeNav" aria-expanded="false" aria-label="Toggle category navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="storeNav">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="{$HOME_URL}">{$HOME}</a>
                        </li>
                        {foreach from=$CATEGORIES item=category}
                            {if count($category.subcategories)}
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                                        {$category.title}
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{$category.url}">{$category.title}</a>
                                        {foreach from=$category.subcategories item=subcategory}
                                            <a class="dropdown-item" href="{$subcategory.url}">{$subcategory.title}</a>
                                        {/foreach}
                                    </div>
                                </li>
                            {else}
                                <li class="nav-item">
                                    <a class="nav-link" href="{$category.url}">{$category.title}</a>
                                </li>
                            {/if}
                        {/foreach}
                    </ul>
                </div>
            </nav>

            <hr />

            {if count($WIDGETS)}
            <div class="row">
                <div class="col-md-9">
                    {/if}

                    {$CONTENT}

                    {if count($WIDGETS)}
                </div>
                <div class="col-md-3">
                    {foreach from=$WIDGETS item=widget}
                        {$widget}
                        <br />
                    {/foreach}
                </div>
            </div>
            {/if}
        </div>
    </div>
</div>

{include file='footer.tpl'}