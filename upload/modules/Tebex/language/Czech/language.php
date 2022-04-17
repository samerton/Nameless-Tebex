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
    'store' => 'Obchod',
    'buycraft' => 'Tebex',
    'home' => 'Domů',
    'view_full_store' => 'Zobrazit plný obchod',
    'buy' => 'Zakoupit',
    'sale' => 'Sleva',

    /*
     *  Admin terms
     */
    'force_sync' => 'Vynutit synchronizaci',
    'settings' => 'Nastavení',
    'categories' => 'Kategorie',
    'packages' => 'Balíčky',
    'payments' => 'Platby',
    'bans' => 'Zákazy',
    'coupons' => 'Kupóny',
    'gift_cards' => 'Dárkové karty',
    'server_key' => 'Klíč serveru',
    'server_key_info' => 'Toto je tajný klíč zobrazený při úpravě vašeho serveru <a href=&quot;https://server.tebex.io/game-servers&quot; rel=&quot;nofollow&quot; target=&quot;_blank&quot;>zde</a>', // &quot; = ", don't change
    'invalid_server_key' => 'Neplatný klíč serveru.',
    'store_content_max' => 'Obsah hlavní stránky obchodu může obsahovat maximálně 1 000 000 znaků.',
    'store_path' => 'Cesta k obchodu',
    'store_index_content' => 'Obsah hlavní stránky obchodu',
    'allow_guests' => 'Umožnit hostům zobrazit si obchod?',
    'show_home_tab' => 'Zobrazit domovskou záložku?',
    'updated_successfully' => 'Úspěšně aktualizováno.',
    'no_categories' => 'Nebyly nalezeny žádné kategorie! Ujistěte se, že jste nejprve synchronizovali váš obchod.',
    'no_packages' => 'Nebyly nalezeny žádné balíčky! Ujistěte se, že jste nejprve synchronizovali váš obchod.',
    'no_payments' => 'Nebyly nalezeny žádné platby!',
    'no_bans' => 'Nebyly nalezeny žádné zákazy!',
    'no_coupons' => 'Nebyly nalezeny žádné kupóny!',
    'no_gift_cards' => 'Nebyly nalezeny žádné dárkové karty!',
    'no_subcategories' => 'Žádné podkategorie.',
    'editing_category_x' => 'Úprava kategorie {x}', // Don't replace {x}
    'category_description' => 'Popis kategorie',
    'category_image' => 'Obrázek kategorie',
    'upload_new_image' => 'Nahrát nový obrázek',
    'description_max_100000' => 'Popis může obsahovat maximálně 100 000 znaků.',
    'description_updated_successfully' => 'Popis úspěšně upraven.',
    'image_updated_successfully' => 'Obrázek úspěšně upraven.',
    'unable_to_upload_image' => 'Nepodařilo se nahrát obrázek: {x}', // Don't replace {x} (error message)
    'unable_to_create_image_directory' => 'Nepodařilo se vytvořit adresář <strong>/uploads/store</strong> pro ukládání obrázků.',
    'editing_package_x' => 'Úprava balíčku {x}', // Don't replace {x}
    'package_description' => 'Popis balíčku',
    'package_image' => 'Obrázek balíčku',
    'user' => 'Uživatel',
    'amount' => 'Množství',
    'date' => 'Datum',
    'view' => 'Zobrazit',
    'viewing_payments_for_user_x' => 'Zobrazování plateb uživatele {x}', // Don't replace {x}
    'no_payments_for_user' => 'U daného uživatele nebyly nalezeny žádné platby.',
    'package' => 'Balíček',
    'ign' => 'Jméno ve hře',
    'uuid' => 'UUID',
    'please_enter_valid_ign_package' => 'Zadejte platné herní jméno a vyberte balíček.',
    'price' => 'Cena',
    'please_enter_valid_price' => 'Zadejte platnou cenu.',
    'payment_created_successfully' => 'Platba úspěšně vytvořena.',
    'viewing_payment' => 'Zobrazování platby {x}', // Don't replace {x}
	  'pending_commands' => 'Čekající příkazy',
    'no_pending_commands' => 'Žádné čekající příkazy.',
    'reason' => 'Důvod',
    'email' => 'E-mail',
    'must_enter_uuid' => 'Musíte zadat UUID!',
    'ban_created_successfully' => 'Zákaz úspěšně vytvořen.',
    'creating_ban' => 'Vytváření zákazu',
    'ip_address' => 'IP adresa',
    'optional' => 'Volitelné',
    'viewing_ban_x' => 'Zobrazování zákazu {x}', // Don't replace {x}
	'remove_ban_in_buycraft' => 'Tento zákaz můžete odebrat ve vašem ovládacím panelu Tebexu.',
    'creating_coupon' => 'Vytváření kupónu',
    'coupon_code' => 'Kód kupónu',
    'coupon_code_alphanumeric' => 'Kód kupónu musí být alfanumerický.',
    'coupon_code_required' => 'Je vyžadován kód kupónu.',
    'coupon_note' => 'Poznámka',
    'coupon_note_required' => 'Je vyžadována poznámka.',
    'invalid_expire_date' => 'Neplatné datum vypršení.',
    'invalid_start_date' => 'Neplatné datum začátku.',
    'effective_on' => 'Platné',
    'cart' => 'Košík',
    'category' => 'Kategorie',
    'select_multiple_with_ctrl' => '(vyberte více držením (Cmd na Macu))',
    'discount_type' => 'Typ slevy',
    'value' => 'Hodnota',
    'percentage' => 'Procento',
    'unlimited_usage' => 'Neomezené použití',
    'uses' => 'POužití',
    'never_expire' => 'Nikdy nevyprší',
    'never' => 'Nikdy',
    'expiry_date' => 'Datum vypršení (yyyy-mm-dd)',
    'start_date' => 'Datum začátku (yyyy-mm-dd)',
    'expiry_date_table' => 'Datum vypršení', // expiry_date without (yyyy-mm-dd)
    'basket_type' => 'Typ košíku',
    'all_purchases' => 'Všechny platby',
    'one_off_purchases' => 'Jednorázové platby',
    'subscriptions' => 'Předplatná',
    'discount_application_type' => 'Typ použití slevy',
    'each_package' => 'Použít na každý balíček',
    'basket_before_sales' => 'Použít na košík před slevami',
    'basket_after_sales' => 'Použít na košík po slevách',
    'minimum_spend' => 'Minimum k utracení',
    'user_coupon_for' => 'Uživatelské jméno nebo uživatel, jemuž je kupón určen',
    'user_limit' => 'Limit na uživatele/IP adresu',
    'coupon_created_successfully' => 'Kupón úspěšně vytvořen.',
    'confirm_delete_coupon' => 'Opravdu chcete odstranit tento kupón?',
    'coupon_deleted_successfully' => 'Kupón úspěšně odstraněn.',
    'viewing_coupon_x' => 'Zobrazování kupónu {x}', // Don't replace {x}
	'edit_coupon_in_buycraft' => 'Tento kupón můžete upravit ve vašem ovládacím panelu Tebexu.',
    'creating_gift_card' => 'Vytváření dárkové karty',
    'gift_card_value_required' => 'Musíte zadat hodnotu dárkové karty!',
    'gift_card_value' => 'Hodnota',
    'gift_card_note' => 'Poznámka',
    'gift_card_created_successfully' => 'Dárková karta úspěšně vytvořena.',
    'gift_card_created_successfully_with_code' => 'Dárková karta s kódem <strong>{x}</strong> úspěšně vytvořena.', // Don't replace {x}
    'gift_card_updated_successfully' => 'Dárková karta úspěšně upravena.',
    'gift_card_voided_successfully' => 'Dárková karta úspěšně zneplatněna.',
    'gift_card_code' => 'Kód',
    'gift_card_start_balance' => 'Zůstatek na začátku',
    'gift_card_balance_remaining' => 'Zbývající zůstatek',
    'gift_card_active' => 'Aktivní',
    'viewing_gift_card_x' => 'Zobrazování dárkové karty {x}',
    'add_credit' => 'Přidat kredit k dárkové kartě',
    'credit' => 'Kredit',
    'credit_required' => 'Je vyžadována hodnota kreditu k přičtení!',
    'void_gift_card' => 'Zneplatnit dárkovou kartu',
    'confirm_void_gift_card' => 'Opravdu chcete zneplatnit tuto dárkovou kartu?',
    'id_x' => 'ID: {x}', // Don't replace {x}

    /*
     *  Other permissions
     */
    'update_category' => 'Upravit kategorii',
    'update_package' => 'Upravit balíček',
    'new_payment' => 'Nová platba',
    'new_gift_card' => 'Nová dárková karta',
    'update_gift_card' => 'Upravit dárkovou kartu',
    'new_coupon' => 'Nový kupón',
    'delete_coupon' => 'Odstranit kupón',
    'new_ban' => 'Nový zákaz',

    /*
     *  Admin force sync
     */
    'unable_to_get_information' => 'Nepodařilo se získat informace z Tebexu: {x}', // Don't replace {x} (error code)
    'information_retrieved_successfully' => 'Informace úspěšně načteny',
    'unable_to_get_command_queue' => 'Nepodařilo se získat frontu příkazů z Tebexu: {x}', // Don't replace {x} (error code)
    'command_queue_retrieved_successfully' => 'Fronta příkazů úspěšně načtena',
    'unable_to_get_listing' => 'Nepodařilo se získat seznam z Tebexu: {x}', // Don't replace {x} (error code)
    'listing_retrieved_successfully' => 'Seznam úspěšně načten',
    'unable_to_get_packages' => 'Nepodařilo se získat balíčky z Tebexu: {x}', // Don't replace {x} (error code)
    'packages_retrieved_successfully' => 'Balíčky úspěšně načteny',
    'unable_to_get_payments' => 'Nepodařilo se získat platby z Tebexu: {x}', // Don't replace {x} (error code)
    'payments_retrieved_successfully' => 'Platby úspěšně načteny',
    'unable_to_get_gift_cards' => 'Nepodařilo se získat dárkové karty z Tebexu: {x}', // Don't replace {x} (error code)
    'gift_cards_retrieved_successfully' => 'Dárkové karty úspěšně načteny',
    'unable_to_get_coupons' => 'Nepodařilo se získat kupóny z Tebexu: {x}', // Don't replace {x} (error code)
    'coupons_retrieved_successfully' => 'Kupóny úspěšně načteny',
    'unable_to_get_bans' => 'Nepodařilo se získat zákazy: {x}', // Don't replace {x} (error code)
    'bans_retrieved_successfully' => 'Zákazy úspěšně načteny',

	/*
	 *  Hooks
	 */
	'purchase_hook_info' => 'Nové zakoupení v obchodě',
	'new_coupon_hook_info' => 'Kupón v obchodě vytvořen',
	'new_gift_card_hook_info' => 'Dárková karta v obchodě vytvořena',

	/*
	 *  Widgets
	 */
	'latest_purchases' => 'Poslední platby',
	'no_purchases' => 'Žádné platby',
	'latest_purchases_limit' => 'Limit posledních plateb',
	'widget_cached' => 'Widget posledních plateb je v mezipaměti po dobu 2 minut, vaše změny se nemusí ihned projevit.',
	'featured_package' => 'Doporučený balíček',
	'featured_packages' => 'Doporučené balíčky',
	'featured_packages_info' => 'Jeden balíček bude náhodně vybrán ze zvolených balíčků',

);
