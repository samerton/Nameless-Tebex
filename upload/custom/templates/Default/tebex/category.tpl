{include file='header.tpl'}
{include file='navbar.tpl'}

<div class="container">
    <div class="card">
        <div class="card-body">
            <h2 style="display:inline">{$STORE} &raquo; {$ACTIVE_CATEGORY}</h2>
            <a class="btn btn-primary float-lg-right" href="{$STORE_URL}" target="_blank">{$VIEW_FULL_STORE}</a>

            <hr />

            <nav class="navbar navbar-expand-lg navbar-dark bg-primary" style="border-radius:5px;">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#storeNav" aria-controls="storeNav" aria-expanded="false" aria-label="Toggle category navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="storeNav">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{$HOME_URL}">{$HOME}</a>
                        </li>
                        {foreach from=$CATEGORIES item=category}
                            {if count($category.subcategories)}
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                                        {$category.title}
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item {if $category.active}active{/if}" href="{$category.url}">{$category.title}</a>
                                        {foreach from=$category.subcategories item=subcategory}
                                            <a class="dropdown-item {if $subcategory.active}active{/if}" href="{$subcategory.url}">{$subcategory.title}</a>
                                        {/foreach}
                                    </div>
                                </li>
                            {else}
                                <li class="nav-item">
                                    <a class="nav-link {if $category.active}active{/if}" href="{$category.url}">{$category.title}</a>
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

                    {if isset($NO_PACKAGES)}
                        <div class="alert alert-info">
                            {$NO_PACKAGES}
                        </div>
                    {else}
                        {assign var=i value=0}
                        {foreach from=$PACKAGES item=package name=packageArray}
                            {if $i eq 0 OR ($i % 4) eq 0}
                                <div class="card-deck">
                            {/if}

                            <div class="card text-center">
                                <div class="card-body">
                                    {if $package.image}
                                        <img class="rounded" style="max-height: 150px; max-width: 150px;" src="{$package.image}" alt="{$package.name}">
                                    {/if}

                                    <hr />

                                    <h5 class="card-title">{$package.name}</h5>
                                    <div class="ui divider"></div>
                                    {if $package.sale_active}
                                        <span style="color: #dc3545;text-decoration:line-through;">{$CURRENCY}{$package.price}</span>
                                    {/if}
                                    {$CURRENCY}{$package.real_price}

                                    <hr />

                                    <button role="button" class="btn btn-primary" data-toggle="modal" data-target="#modal{$package.id}">
                                        {$VIEW} &raquo;
                                    </button>
                                </div>
                            </div>

                            <div class="modal fade" id="modal{$package.id}" tabindex="-1" role="dialog" aria-labelledby="modal{$package.id}Label" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content" style="text-align: center;">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modal{$package.id}Label">{$package.name}<span aria-hidden="true"> | {$CURRENCY}{$package.real_price}</span></h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            {if $package.image}
                                                <img class="rounded" style="max-width: 200px; max-height: 200px" src="{$package.image}" alt="{$package.name}" />
                                                <hr />
                                            {/if}
                                            <div class="forum_post">
                                                {$package.description}
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">{$CLOSE}</button>
                                            <a href="{$package.link}" target="_blank" rel="nofollow noopener" class="btn btn-success">{$BUY}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {if (($i+1) % 3) eq 0 OR $smarty.foreach.packageArray.last}
                                </div><br />
                            {/if}
                            {assign var=i value=$i+1}
                        {/foreach}
                    {/if}

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