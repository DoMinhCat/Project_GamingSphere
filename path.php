<?php
//FRONT OFFICE
define('index_front', '/'); // index.php

//connexion
define('login', '/connexion/login'); // login.php
define('inscription', '/connexion/inscription'); // inscription.php
define('forgot_mdp', '/connexion/forgot_password'); // forgot_mdp.php
define('resend_verify_inscrire', '/connexion/verify_email'); //resend_verify_inscrire.php
define('reset_mdp_err', '/connexion/reset_password_error'); // reset_mdp_err.php
define('reset_mdp', '/connexion/reset_password'); // reset_mdp.php
define('session_timeout', '/connexion/session_timeout'); // session_timeout.php
define('status_verify', '/connexion/status_verify'); // status_verify.php

//magasin
define('magasin_main', '/magasin'); // magasin_main.php
define('magasin_game', '/magasin/game'); // game_info.php
define('magasin_category', '/magasin/category'); // magasin_category.php

//credits
define('add_credits', '/credits/add'); // add_credits.php
define('cancel', '/credits/cancel'); // cancel.php
define('credits_main', '/credits'); // credits_main.php
define('success', '/credits/paiement'); // success.php

//tournois
define('tournois_main', '/tournois'); // tournois_main.php
define('tournois_details', '/tournois/details'); // tournois_details.php
define('tournois_category', '/tournois/category'); // tournois_category.php
define('tournois_jeux', '/tournois/jeux'); // tournois_jeux.php

//team
define('create_team', '/team/create'); // create_team.php
define('team_details', '/team/details'); // team_details.php
define('team_list', '/team/list'); // team_list.php

//communaute
define('communaute_main', '/communaute'); // communaute.php

//actualite
define('actualite_main', '/actualite'); // actualite_main.php
define('actualite_article', '/actualite/article'); // actualite_article.php
define('actualite_categorie', '/actualite/categorie'); // actualite_categorie.php

//forum
define('forum_main', '/forum'); // forum_main.php
define('forum_category', '/forum/category'); // categorie.php
define('nouveau_sujet', '/forum/nouveau_sujet.php'); // nouveau_sujet.php
define('sujet', '/forum/sujet'); // sujet.php

//panier
define('panier', '/panier'); // panier_main.php
define('confirmation_achat', '/panier/confirmation_achat/'); // confirmation_achat.php

//profil
define('my_account', '/mon_compte'); // my_account.php
define('edit_account', '/mon_compte/edit'); // edit_account.php
define('friend_list', '/mon_compte/friend_list'); // friend_list.php
define('my_teams', '/mon_compte/teams'); // my_teams.php
define('order_history', '/mon_compte/order_history'); // order_history.php
define('profil', '/profil'); // profil.php
define('tournament_list', '/mon_compte/tournament_list'); // tournament_list.php

//messages
define('conversation', '/messages/conversation'); // conversation.php
define('messagerie', '/messages'); // messagerie.php
define('nouvelle_conversation', '/messages/new'); // nouvelle_conversation.php

//search
define('search', '/include/search'); // search.php

//paris
define('paris_main', '/paris'); // paris_main.php
define('parier', '/paris/parier'); // parier.php
define('mes_paris', '/paris/mes paris'); // paris_details.php


//easter
define('easter', '/easter'); // easter.php

//newsletter
define('unsubscribe', '/unsubscribe'); // unsubscribe.php





//BACK OFFICE
define('index_back', '/back-office'); // index.php

//article
define('article_back', '/back-office/article'); //articles.php
define('article_edit_back', '/back-office/article/edit'); //article_edit.php

//captcha
define('captcha_back', '/back-office/captcha'); // captcha.php
define('captcha_edit_back', '/back-office/captcha/edit'); //captcha_edit.php

//communication
define('communication_back', '/back-office/communication'); // communication.php

//event
define('event_back', '/back-office/event'); // evenements.php

//forum
define('forum_back', '/back-office/forum'); // forum.php
define('forum_annonce_back', '/back-office/forum/annonce'); // annonce_main.php
define('forum_discussion_back', '/back-office/forum/discussion'); // discussion_main.php
define('forum_support_back', '/back-office/forum/support'); // support_main.php

define('support_edit_back', '/back-office/forum/support/edit'); //  edit.php support
define('annonce_edit_back', '/back-office/forum/annonce/edit'); // edit.php annonce
define('discussion_edit_back', '/back-office/forum/discussion/edit'); // edit.php discussion

//jeux
define('jeux_back', '/back-office/jeux'); // jeux.php
define('jeux_edit_back', '/back-office/jeux/edit'); //modify_game.php
define('jeux_add_back', '/back-office/jeux/add'); // add_game.php

//paris
define('paris_back', '/back-office/paris'); // paris.php

//profils
define('profils_back', '/back-office/profils'); // profils.php
define('profils_edit_back', '/back-office/profils/edit'); // edit_user.php

//tournois
define('tournois_back', '/back-office/tournois'); // tournois_main.php
define('tournois_add_back', '/back-office/tournois/add'); // add_tournoi.php
define('tournois_edit_back', '/back-office/tournois/edit'); // modify_tournoi.php
define('tournois_result_back', '/back-office/tournois/result'); // tournoi_result.php

//stats
define('stats_main', '/back-office/stats'); // stats_main.php
define('log_inscription', '/back-office/stats/log/inscription'); // log_display/inscription.php
define('log_login', '/back-office/stats/log/login'); // log_display/login.php
define('log_transaction', '/back-office/stats/log/transaction'); // log_display/transaction.php
define('stats_duree', '/back-office/stats/duree'); // log_display/duree.php
