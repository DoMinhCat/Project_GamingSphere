<?php
//FRONT OFFICE
define('index_front', '/'); // index.php

define('login', '/connexion'); // connexion/login.php
define('inscription', '/inscription'); // connexion/inscription.php

define('magasin_main', '/magasin'); // magasin/magasin_main.php
define('magasin_game', '/magasin/game'); // magasin/game_info.php

define('tournois_main', '/tournois'); // tournois/tournois_main.php
define('tournois_details', '/tournois/details'); // tournois/tournois_details.php
define('tournois_jeux', '/tournois/jeux'); // tournois/tournois_jeux.php

define('create_team', '/team/create'); // team/create_team.php
define('team_details', '/team/details'); // team/team_details.php
define('team_list', '/team/list'); // team/team_list.php

define('communaute_main', '/communaute'); // communaute/communaute.php

define('actualite_main', '/actualite'); // actualite/actualite_main.php
define('actualite_article', '/actualite/article'); // actualite/actualite_article.php
define('actualite_categorie', '/actualite/categorie'); // actualite/actualite_categorie.php

define('forum_main', '/forum'); // forum/forum.php
define('forum_category', '/forum/category'); // forum/categorie.php
define('nouveau_sujet', '/forum/new'); // forum/nouveau_sujet.php
define('sujet', '/forum/sujet'); // forum/sujet.php

define('panier', '/panier'); // panier/panier_main.php
define('confirmation_achat', '/panier/confirmation_achat/'); // panier/confirmation_achat.php

define('my_account', '/mon_compte'); // profil/my_account.php
define('edit_account', '/mon_compte/edit'); // profil/edit_account.php
define('friend_list', '/mon_compte/friend_list'); // profil/friend_list.php
define('my_teams', '/mon_compte/teams'); // profil/my_teams.php
define('order_history', '/mon_compte/order_history'); // profil/order_history.php
define('profil', '/profil'); // profil/profil.php
define('tournament_list', '/mon_compte/tournament_list'); // profil/tournament_list.php



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
