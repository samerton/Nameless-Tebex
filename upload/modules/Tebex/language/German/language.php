<?php
/*
 *	Made by Samerton
 *  Translation by enno123 & Justman10000
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
    'store' => 'Store',
    'buycraft' => 'Tebex',
    'home' => 'Startseite',
    'view_full_store' => 'Gesamten Store ansehen',
    'buy' => 'Kaufen',
    'sale' => 'Verkaufen',

    /*
     *  Admin terms
     */
    'force_sync' => 'Synchronisation erzwingen',
    'settings' => 'Einstellungen',
    'categories' => 'Kategorien',
    'packages' => 'Pakete',
    'payments' => 'Zahlungen',
    'bans' => 'Sperren',
    'coupons' => 'Coupons',
    'gift_cards' => 'Geschenkkarten',
    'server_key' => 'Server Key',
    'server_key_info' => 'Dies ist der Secret Key, der beim Bearbeiten deines Servers <a href=&quot;https://server.tebex.io/game-servers&quot; rel=&quot;nofollow&quot; target=&quot;_blank&quot;>hier</a> angezeigt wird', // &quot; = ", don't change
    'invalid_server_key' => 'Ungültiger server key.',
    'store_content_max' => 'Der Inhalt des Store Indexes darf maximal 1.000.000 Zeichen lang sein.',
    'store_path' => 'Store Path',
    'store_index_content' => 'Store Index Content',
    'allow_guests' => 'Erlaube den Gästen, den Laden zu besichtigen?',
    'show_home_tab' => 'Registerkarte "Startseite" anzeigen?',
    'updated_successfully' => 'Erfolgreich aktualisiert.',
    'no_categories' => 'Es wurden keine Kategorien gefunden! Stelle sicher, dass Du Deinen Shop zuerst synchronisiert hast.',
    'no_packages' => 'Es wurden keine Pakete gefunden! Stelle sicher, dass Du Deinen Shop zuerst synchronisiert hast.',
    'no_payments' => 'Es wurden keine Zahlungen gefunden!',
    'no_bans' => 'Es wurden keine Sperren gefunden!',
    'no_coupons' => 'Es wurden keine Coupons gefunden!',
    'no_gift_cards' => 'Es wurden keine Geschenkkarten gefunden!',
    'no_subcategories' => 'Keine Unterkategorien.',
    'editing_category_x' => 'Bearbeitung der Kategorie {x}', // Don't replace {x}
    'category_description' => 'Kategorie Beschreibung',
    'category_image' => 'Kategorie Bild',
    'upload_new_image' => 'Neues Bild hochladen',
    'description_max_100000' => 'Die Beschreibung darf maximal 100000 Zeichen lang sein.',
    'description_updated_successfully' => 'Beschreibung erfolgreich aktualisiert.',
    'image_updated_successfully' => 'Bild erfolgreich aktualisiert.',
    'unable_to_upload_image' => 'Bild kann nicht hochgeladen werden: {x}', // Don't replace {x} (error message)
    'unable_to_create_image_directory' => 'Das Verzeichnis <strong>/uploads/store</strong> zum Speichern von Bildern kann nicht erstellt werden.',
    'editing_package_x' => 'Bearbeitung des Pakets {x}', // Don't replace {x}
    'package_description' => 'Paket Beschreibung',
    'package_image' => 'Paket Bild',
    'user' => 'Benutzer',
    'amount' => 'Betrag',
    'date' => 'Datum',
    'view' => 'Anzeigen',
    'viewing_payments_for_user_x' => 'Anzeigen von Zahlungen für Benutzer {x}', // Don't replace {x}
    'no_payments_for_user' => 'Es wurden keine Zahlungen für diesen Benutzer gefunden.',
    'package' => 'Paket',
    'ign' => 'Ingame Username',
    'uuid' => 'UUID',
    'please_enter_valid_ign_package' => 'Bitte gib einen gültigen Ingame-Username ein und wähle ein Paket.',
    'price' => 'Price',
    'please_enter_valid_price' => 'Bitte gebe einen gültigen Preis ein.',
    'payment_created_successfully' => 'Zahlung erfolgreich erstellt.',
    'viewing_payment' => 'Ansicht der Zahlung {x}', // Don't replace {x}
	'pending_commands' => 'Ausstehende Befehle',
    'no_pending_commands' => 'Keine ausstehenden Befehle.',
    'reason' => 'Grund',
    'email' => 'E-Mail',
    'must_enter_uuid' => 'Du musst eine UUID eingeben!',
    'ban_created_successfully' => 'Sperre erfolgreich erstellt.',
    'creating_ban' => 'Sperre erstellen',
    'ip_address' => 'IP Addresse',
    'optional' => 'Optional',
    'viewing_ban_x' => 'Sperre einsehen {x}', // Don't replace {x}
	'remove_ban_in_buycraft' => 'Du kannst dieses Verbot im Tebex-Kontrollzentrum aufheben.',
    'creating_coupon' => 'Coupon erstellen',
    'coupon_code' => 'Coupon Code',
    'coupon_code_alphanumeric' => 'Der Coupon Code muss alphanumerisch sein.',
    'coupon_code_required' => 'Ein Coupon Code ist erforderlich.',
    'coupon_note' => 'Hinweis',
    'coupon_note_required' => 'Ein Hinweis ist erforderlich.',
    'invalid_expire_date' => 'Ungültiges Ablaufdatum.',
    'invalid_start_date' => 'Ungültiges Startdatum.',
    'effective_on' => 'Wirksam am',
    'cart' => 'Warenkorb',
    'category' => 'Kategorie',
    'select_multiple_with_ctrl' => '(wähle mehrere mit gedrückter Strg-Taste (Cmd auf einem Mac))',
    'discount_type' => 'Rabatt Typ',
    'value' => 'Wert',
    'percentage' => 'Prozentsatz',
    'unlimited_usage' => 'Unbegrenzte Nutzung',
    'uses' => 'Verwendet',
    'never_expire' => 'Läuft niemals ab',
    'never' => 'Niemals',
    'expiry_date' => 'Ablaufdatum (jjjj-mm-tt)',
    'start_date' => 'Startdatum (jjjj-mm-tt)',
    'expiry_date_table' => 'Ablaufdatum', // expiry_date without (yyyy-mm-dd)
    'basket_type' => 'Warenkorb Typ',
    'all_purchases' => 'Alle Einkäufe',
    'one_off_purchases' => 'Einmalige Käufe',
    'subscriptions' => 'Abonnements',
    'discount_application_type' => 'Rabatt Anwendungsart',
    'each_package' => 'Auf jedes Paket anwenden',
    'basket_before_sales' => 'Vor dem Verkauf in den Warenkorb legen',
    'basket_after_sales' => 'Nach dem Verkauf in den Warenkorb legen',
    'minimum_spend' => 'Mindestausgaben',
    'user_coupon_for' => 'Benutzername des Benutzers, für den der Coupon gilt',
    'user_limit' => 'Limit pro Benutzer/IP-Adresse',
    'coupon_created_successfully' => 'Coupon erfolgreich erstellt.',
    'confirm_delete_coupon' => 'Bist du sicher, dass du diesen Coupon löschen willst?',
    'coupon_deleted_successfully' => 'Coupon erfolgreich gelöscht.',
    'viewing_coupon_x' => 'Coupon ansehen {x}', // Don't replace {x}
	'edit_coupon_in_buycraft' => 'Du kannst diesen Coupon im Tebex-Kontrollzentrum bearbeiten.',
    'creating_gift_card' => 'Gift Card erstellen',
    'gift_card_value_required' => 'Du musst einen Gift Card Wert eingeben!',
    'gift_card_value' => 'Wert',
    'gift_card_note' => 'Hinweis',
    'gift_card_created_successfully' => 'Geschenkkarte erfolgreich erstellt.',
    'gift_card_created_successfully_with_code' => 'Geschenkkarte mit Code <strong>{x}</strong> erfolgreich erstellt.', // Don't replace {x}
    'gift_card_updated_successfully' => 'Geschenkkarte erfolgreich aktualisiert.',
    'gift_card_voided_successfully' => 'Die Geschenkkarte wurde erfolgreich entwertet.',
    'gift_card_code' => 'Code',
    'gift_card_start_balance' => 'Startguthaben',
    'gift_card_balance_remaining' => 'Verbleibender Saldo',
    'gift_card_active' => 'Aktiv',
    'viewing_gift_card_x' => 'Geschenkkarte ansehen {x}',
    'add_credit' => 'Guthaben zur Geschenkkarte hinzufügen',
    'credit' => 'Kredit',
    'credit_required' => 'Die Höhe des hinzuzufügenden Guthabens ist erforderlich!',
    'void_gift_card' => 'Ungültige Geschenkkarte',
    'confirm_void_gift_card' => 'Bist du sicher, dass du diese Geschenkkarte für ungültig erklären willst?',
    'id_x' => 'ID: {x}', // Don't replace {x}

    /*
     *  Other permissions
     */
    'update_category' => 'Kategorie aktualisieren',
    'update_package' => 'Paket aktualisieren',
    'new_payment' => 'Neue Zahlung',
    'new_gift_card' => 'Neue Geschenkkarte',
    'update_gift_card' => 'Geschenkkarte aktualisieren',
    'new_coupon' => 'Neuer Coupon',
    'delete_coupon' => 'Coupon löschen',
    'new_ban' => 'Neue Sperre',

    /*
     *  Admin force sync
     */
    'unable_to_get_information' => 'Informationen von Tebex können nicht abgerufen werden: {x}', // Don't replace {x} (error code)
    'information_retrieved_successfully' => 'Informationen erfolgreich abgerufen',
    'unable_to_get_command_queue' => 'Befehlswarteschlange kann nicht von Tebex abgerufen werden: {x}', // Don't replace {x} (error code)
    'command_queue_retrieved_successfully' => 'Befehlswarteschlange erfolgreich abgerufen',
    'unable_to_get_listing' => 'Eintrag von Tebex kann nicht abgerufen werden: {x}', // Don't replace {x} (error code)
    'listing_retrieved_successfully' => 'Auflistung erfolgreich abgerufen',
    'unable_to_get_packages' => 'Kann keine Pakete von Tebex erhalten: {x}', // Don't replace {x} (error code)
    'packages_retrieved_successfully' => 'Pakete erfolgreich abgerufen',
    'unable_to_get_payments' => 'Kann keine Zahlungen von Tebex erhalten: {x}', // Don't replace {x} (error code)
    'payments_retrieved_successfully' => 'Zahlungen erfolgreich abgerufen',
    'unable_to_get_gift_cards' => 'Kann keine Gift cards von Tebex erhalten: {x}', // Don't replace {x} (error code)
    'gift_cards_retrieved_successfully' => 'Gift cards erfolgreich abgerufen',
    'unable_to_get_coupons' => 'Es ist nicht möglich, Coupons von Tebex zu erhalten: {x}', // Don't replace {x} (error code)
    'coupons_retrieved_successfully' => 'Coupons erfolgreich abgerufen',
    'unable_to_get_bans' => 'Kann keine Sperren von Tebex erhalten: {x}', // Don't replace {x} (error code)
    'bans_retrieved_successfully' => 'Sperren erfolgreich abgerufen',

	/*
	 *  Hooks
	 */
	'purchase_hook_info' => 'Kauf eines neuen Produkts',
	'new_coupon_hook_info' => 'Store-Coupon erstellt',
	'new_gift_card_hook_info' => 'Geschenkkarte für den Store erstellt',

	/*
	 *  Widgets
	 */
	'latest_purchases' => 'Letzte Einkäufe',
	'no_purchases' => 'Keine Käufe',
	'latest_purchases_limit' => 'Letzte Käufe begrenzen',
	'widget_cached' => 'Das Widget für die letzten Einkäufe wird für 2 Minuten zwischengespeichert, so dass deine Änderungen möglicherweise nicht sofort wirksam werden.',
	'featured_package' => 'Ausgezeichnetes Paket',
	'featured_packages' => 'Ausgezeichnete Packages',
	'featured_packages_info' => 'Ein Paket wird nach dem Zufallsprinzip aus allen ausgewählten Paketen ausgewählt',
);