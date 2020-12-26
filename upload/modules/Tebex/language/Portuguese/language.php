<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr5
 *
 *  License: MIT
 *
 *  Language file for Buycraft module for NamelessMC
 */
// Which version of NamelessMC is this language file updated to?
$language_version = '2.0.0-pr6';
$language = array(
    /*
     *  General terms
     */
    'store' => 'Loja',
    'buycraft' => 'Tebex',
    'home' => 'Início',
    'view_full_store' => 'Ver loja completa',
    'buy' => 'Comprar',
    'sale' => 'Venda',
    /*
     *  Admin terms
     */
    'force_sync' => 'Sincronizar',
    'settings' => 'Configurações',
    'categories' => 'Categorias',
    'packages' => 'Pacotes',
    'payments' => 'Pagamentos',
    'bans' => 'Banimentos',
    'coupons' => 'Cupons',
    'gift_cards' => 'Cartões Presente',
    'server_key' => 'Chave do Servidor',
    'server_key_info' => 'Essa é a chave secreta mostrada ao editar seu servidor <a href=&quot;https://server.tebex.io/settings/servers&quot; rel=&quot;nofollow&quot; target=&quot;_blank&quot;>aqui</a>', // &quot; = ", don't change
    'invalid_server_key' => 'Chave do Servidor inválida.',
    'store_content_max' => 'O índice de conteúdo da loja deve ser de no máximo 1.000.000 caracteres.',
    'store_path' => 'Caminho da Loja',
    'store_index_content' => 'Índice do Conteúdo da Loja',
    'allow_guests' => 'Permitir visitantes verem a loja?',
    'show_home_tab' => 'Show home tab?',
    'updated_successfully' => 'Atualizado com sucesso.',
    'no_categories' => 'Nenhuma categoria encontrada! Certifique-se que você sincronizou a sua loja primeiro.',
    'no_packages' => 'Nenhum pacote encontrado! Cerifique-se que você sincronizou a sua loja primeiro.',
    'no_payments' => 'Nenhum pagamento encontrado!',
    'no_bans' => 'Nenhum banimento encontrado!',
    'no_coupons' => 'Nenhum cupom encontrado!',
    'no_gift_cards' => 'Nenhum cartão presente encontrado!',
    'no_subcategories' => 'Nenhuma subcategoria.',
    'editing_category_x' => 'Editando categoria {x}', // Don't replace {x}
    'category_description' => 'Descrição da Categoria',
    'category_image' => 'Imagem da Categoria',
    'upload_new_image' => 'Enviar Nova Imagem',
    'description_max_100000' => 'A descrição deve ser de no máximo 100.000 caracteres.',
    'description_updated_successfully' => 'Descrição atualizada com sucesso.',
    'image_updated_successfully' => 'Imagem atualizada com sucesso.',
    'unable_to_upload_image' => 'Não foi possível enviar a imagem: {x}', // Don't replace {x} (error message)
    'unable_to_create_image_directory' => 'Não foi possível criar o diretório <strong>/uploads/store</strong> para guardar as imagens.',
    'editing_package_x' => 'Editando pacote {x}', // Don't replace {x}
    'package_description' => 'Descrição do Pacote',
    'package_image' => 'Imagem do Pacote',
    'user' => 'Usuário',
    'amount' => 'Quantia',
    'date' => 'Data',
    'view' => 'Visualizar',
    'viewing_payments_for_user_x' => 'Visualizando pagamentos para o usuário {x}', // Don't replace {x}
    'no_payments_for_user' => 'Nenhum pagamento encontrado para esse usuário.',
    'package' => 'Pacote',
    'ign' => 'Nome no Jogo',
    'uuid' => 'UUID',
    'please_enter_valid_ign_package' => 'Por favor insira um nome válido e selecione um pacote.',
    'price' => 'Preço',
    'please_enter_valid_price' => 'Por favor insira um preço válido.',
    'payment_created_successfully' => 'Pagamento criado com sucesso.',
    'viewing_payment' => 'Visualizando pagamento {x}', // Don't replace {x}
	'pending_commands' => 'Comandos pendentes',
    'no_pending_commands' => 'Nenhum comando pendente.',
    'reason' => 'Motivo',
    'email' => 'Email',
    'must_enter_uuid' => 'Você deve inserir um UUID!',
    'ban_created_successfully' => 'Banimento criado com sucesso.',
    'creating_ban' => 'Criando Banimento',
    'ip_address' => 'Endereço IP',
    'optional' => 'Opcional',
    'viewing_ban_x' => 'Visualizando banimento {x}', // Don't replace {x}
	'remove_ban_in_buycraft' => 'Você pode remover esse banimento no seu painel de controle do Tebex.',
    'creating_coupon' => 'Criando Cupom',
    'coupon_code' => 'Código do Cupom',
    'coupon_code_alphanumeric' => 'O cupom deve ser alfanumérico.',
    'coupon_code_required' => 'O código do cupom é requerido.',
    'coupon_note' => 'Nota',
    'coupon_note_required' => 'A nota é requerida.',
    'invalid_expire_date' => 'Data de vencimento inválida.',
    'invalid_start_date' => 'Data de início inválida.',
    'effective_on' => 'Válido em',
    'cart' => 'Carrinho',
    'category' => 'Categoria',
    'select_multiple_with_ctrl' => '(selecione múltiplos segurando Ctrl (Cmd no Mac))',
    'discount_type' => 'Tipo de Desconto',
    'value' => 'Valor',
    'percentage' => 'Porcentagem',
    'unlimited_usage' => 'Uso Ilimitado',
    'uses' => 'Usos',
    'never_expire' => 'Nunca Vence',
    'never' => 'Nunca',
    'expiry_date' => 'Data de Vencimento (yyyy-mm-dd)',
    'start_date' => 'Data de Início (yyyy-mm-dd)',
    'expiry_date_table' => 'Data de Vencimento', // expiry_date without (yyyy-mm-dd)
    'basket_type' => 'Tipo de Cesta',
    'all_purchases' => 'Todas as compras',
    'one_off_purchases' => 'Compras únicas',
    'subscriptions' => 'Assinaturas',
    'discount_application_type' => 'Tipo de Aplicação do Disconto',
    'each_package' => 'Aplicar a cada pacote',
    'basket_before_sales' => 'Aplicar à cesta antes das vendas',
    'basket_after_sales' => 'Aplicar à cesta depois das vendas',
    'minimum_spend' => 'Gasto Mínimo',
    'user_coupon_for' => 'Nome do Usuário que esse cupom é para',
    'user_limit' => 'Limitar por usuário/endereço IP',
    'coupon_created_successfully' => 'Cupom criado com sucesso.',
    'confirm_delete_coupon' => 'Tem certeza que quer excluir esse cupom?',
    'coupon_deleted_successfully' => 'Cupom excluído com sucesso.',
    'viewing_coupon_x' => 'Visualizando cupom {x}', // Don't replace {x}
	'edit_coupon_in_buycraft' => 'Você pode editar esse cupom no seu painel de controle do Tebex.',
    'creating_gift_card' => 'Criando Cartão Presente',
    'gift_card_value_required' => 'Você deve inserir um valor para o cartão presente!',
    'gift_card_value' => 'Valor',
    'gift_card_note' => 'Nota',
    'gift_card_created_successfully' => 'Cartão presente criado com sucesso.',
    'gift_card_created_successfully_with_code' => 'Cartão presente criado com sucesso com o código <strong>{x}</strong>.', // Don't replace {x}
    'gift_card_updated_successfully' => 'Cartão presente atualizado com sucesso.',
    'gift_card_voided_successfully' => 'Cartão presente excluído com sucesso.',
    'gift_card_code' => 'Código',
    'gift_card_start_balance' => 'Saldo inicial',
    'gift_card_balance_remaining' => 'Saldo restante',
    'gift_card_active' => 'Ativo',
    'viewing_gift_card_x' => 'Visualizando cartão presentecard {x}',
    'add_credit' => 'Adicionar crédito ao cartão presente',
    'credit' => 'Crédito',
    'credit_required' => 'A quantia de crédito para adicionar é requerida!',
    'void_gift_card' => 'Excluir cartão presente',
    'confirm_void_gift_card' => 'Tem certeza que quer excluir esse cartão presente?',
    'id_x' => 'ID: {x}', // Don't replace {x}
    /*
     *  Other permissions
     */
    'update_category' => 'Atualizar categoria',
    'update_package' => 'Atualizar pacote',
    'new_payment' => 'Novo pagamento',
    'new_gift_card' => 'Novo cartão presente',
    'update_gift_card' => 'Atualizar cartão presente',
    'new_coupon' => 'Novo cupom',
    'delete_coupon' => 'Excluir cupom',
    'new_ban' => 'Novo banimento',
    /*
     *  Admin force sync
     */
    'unable_to_get_information' => 'Não foi possível obter informação do Tebex: {x}', // Don't replace {x} (error code)
    'information_retrieved_successfully' => 'Informação obtida com sucesso',
    'unable_to_get_command_queue' => 'Não foi possível obter a fila de comandos do Tebex: {x}', // Don't replace {x} (error code)
    'command_queue_retrieved_successfully' => 'Fila de comandos obtida com sucesso',
    'unable_to_get_listing' => 'Não foi possível obter a lista do Tebex: {x}', // Don't replace {x} (error code)
    'listing_retrieved_successfully' => 'Lista obtida com sucesso',
    'unable_to_get_packages' => 'Unable to get packages from Tebex: {x}', // Don't replace {x} (error code)
    'packages_retrieved_successfully' => 'Packages retrieved successfully',
    'unable_to_get_payments' => 'Não foi possível obter pagamentos do Tebex: {x}', // Don't replace {x} (error code)
    'payments_retrieved_successfully' => 'Pagamentos obtidos com sucesso',
    'unable_to_get_gift_cards' => 'Não foi possível obter cartões presente do Tebex: {x}', // Don't replace {x} (error code)
    'gift_cards_retrieved_successfully' => 'Cartões presente obtidos com sucesso',
    'unable_to_get_coupons' => 'Não foi possível obter cupons do Tebex: {x}', // Don't replace {x} (error code)
    'coupons_retrieved_successfully' => 'Cupons obtidos com sucesso',
    'unable_to_get_bans' => 'Não foi possível obter banimentos do Tebex: {x}', // Don't replace {x} (error code)
    'bans_retrieved_successfully' => 'Banimentos obtidos com sucesso',
	/*
	 *  Hooks
	 */
	'purchase_hook_info' => 'Nova compra na loja',
	'new_coupon_hook_info' => 'Cupom da loja criado',
	'new_gift_card_hook_info' => 'Cartão presente da loja criado',
	/*
	 *  Widgets
	 */
	'latest_purchases' => 'Últimas compras',
	'no_purchases' => 'Nenhuma compra',
	'latest_purchases_limit' => 'Limite de últimas compras',
	'widget_cached' => 'O widget das últimas compras fica em cache por 2 minutos, suas mudanças não surtirão efeito imediatamente.',
	'featured_package' => 'Pacote em Destaque',
	'featured_packages' => 'Pacotes em Destaque',
	'featured_packages_info' => 'Um pacote será escolhido aleatoriamente a partir dos pacotes selecionados',
);
